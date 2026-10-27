<?php
namespace App\Controllers;

use App\Libraries\WebAuthnService;
use App\Models\UserModel;
use App\Models\AttendanceModel;
use App\Models\ScheduleModel;
use App\Models\IncidentModel;
use App\Models\FestivoModel;
use App\Models\WebAuthnCredentialModel;
use App\Models\NotificationModel;

class EmployeeController extends BaseController
{
    protected UserModel $userModel;
    protected AttendanceModel $attendanceModel;
    protected ScheduleModel $scheduleModel;
    protected IncidentModel $incidentModel;
    protected FestivoModel $festivoModel;
    protected WebAuthnCredentialModel $credentialModel;
    protected WebAuthnService $webauthn;

    public function __construct()
    {
        $this->userModel       = new UserModel();
        $this->attendanceModel = new AttendanceModel();
        $this->scheduleModel   = new ScheduleModel();
        $this->incidentModel   = new IncidentModel();
        $this->festivoModel    = new FestivoModel();
        $this->credentialModel = new WebAuthnCredentialModel();
        $this->webauthn        = new WebAuthnService();
    }

    private function checkEmployee(): ?\CodeIgniter\HTTP\RedirectResponse
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login-verde'));
        }

        $rol = (string) (session()->get('user_role') ?? '');
        if ($rol === 'Dev') {
            return redirect()->to(base_url('dios'));
        }
        if ($rol === 'Admin') {
            return redirect()->to(base_url('admin'));
        }

        $user = $this->userModel->find((int) session()->get('user_id'));
        if (! $user) {
            session()->destroy();
            session()->setFlashdata('msg', 'Tu cuenta ya no existe. Contacta con Recursos Humanos.');
            session()->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('login-verde'));
        }

        $estado = (string) ($user['estado'] ?? 'Activo');
        if ($estado !== 'Activo') {
            $motivo = $estado === 'Despedido' ? 'Has sido dado de baja.' : 'Tu vinculación ha finalizado.';
            session()->destroy();
            session()->setFlashdata('msg', $motivo . ' No puedes usar el panel.');
            session()->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('login-verde'));
        }

        return null;
    }

    private function getMyData(): array
    {
        $userId = (int) session()->get('user_id');
        $user   = $this->userModel->find($userId) ?: [];
        $adminId = (int) ($user['admin_id'] ?? 0);

        $hoy  = date('Y-m-d');
        $mes  = date('Y-m');

        // Horario del propio admin del practicante (o asignado directamente)
        $schedules  = $this->scheduleModel->getActive($adminId > 0 ? $adminId : null);
        $miHorario  = $this->pickSchedule($schedules, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '');

        $hoyRegistro = $this->attendanceModel->findByUserAndDate($userId, $hoy) ?: null;

        $misRegistros = $this->attendanceModel
            ->where('user_id', $userId)
            ->like('date', $mes, 'after')
            ->orderBy('date', 'DESC')
            ->findAll();

        $misAsistencias = 0;
        $misTardanzas   = 0;
        $misFaltas      = 0;
        foreach ($misRegistros as $r) {
            if     ($r['status'] === 'present') $misAsistencias++;
            elseif ($r['status'] === 'late')    $misTardanzas++;
            if     ($r['status'] === 'absent')  $misFaltas++;
        }

        $misIncidencias = $this->incidentModel
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $pendientes = 0;
        $enRevision = 0;
        foreach ($misIncidencias as $inc) {
            if     ($inc['estado'] === 'Pendiente') $pendientes++;
            elseif ($inc['estado'] === 'Revisión')  $enRevision++;
        }

        // Festivos (los del mes van marcados en el calendario)
        $festivosMes    = [];
        $festivosProx   = [];
        foreach ($this->festivoModel->getAll($adminId > 0 ? $adminId : null) as $f) {
            $fecha             = (string) ($f['fecha'] ?? '');
            $nombre            = (string) ($f['nombre'] ?? 'Festivo');
            $festivosMes[$fecha] = $nombre;
            if ($fecha >= $hoy) {
                $festivosProx[$fecha] = $nombre;
            }
        }
        ksort($festivosProx);

        // ── Horas: semana actual (lun-dom) y mes en curso ──
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd   = date('Y-m-d', strtotime('sunday this week'));
        $registrosSemana = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date >=', $weekStart)
            ->where('date <=', $weekEnd)
            ->findAll();

        $diasSemana      = $this->diasDeHorario((string) ($miHorario['dias'] ?? ''));
        $horasDia        = $this->horasDiarias($miHorario);
        $horasSemana     = $this->horasDe($registrosSemana);
        $horasMes        = $this->horasDe($misRegistros);
        $horasSemanaEsp  = round($horasDia * $diasSemana, 1);
        $horasMesEsp     = round($horasDia * $this->diasProgramadosEn(date('Y-m-01'), $hoy, $diasSemana), 1);

        // ── Saldo del mes con la misma librería que /admin/horas:
        //    horas reales vs las programadas en el horario ──
        $mesIni   = date('Y-m-01');
        $saldoMes = null;
        try {
            $resumen = (new \App\Libraries\Asistencia\HorasQuery())->porEmpleado(
                $misRegistros,
                [$user],
                $miHorario ? [(int) $miHorario['id'] => $miHorario] : [],
                $this->festivoModel->fechasEnRango($mesIni, $hoy, $adminId > 0 ? $adminId : null),
                $mesIni,
                $hoy
            );
            $saldoMes = $resumen['filas'][0] ?? null;
        } catch (\Throwable $e) {
            log_message('error', 'Saldo de horas en el panel del empleado: ' . $e->getMessage());
        }

        // ── Progreso de la práctica (alta → fin del semestre) ──
        $inicioPrac = !empty($user['created']) ? substr((string) $user['created'], 0, 10) : $hoy;
        $finPrac    = (string) ($user['contract_end'] ?? '');
        if ($finPrac === '' || $finPrac < $inicioPrac) {
            $finPrac = date('Y-m-d', strtotime($inicioPrac . ' +5 months'));
        }
        $totalDias   = max(1, (int) round((strtotime($finPrac) - strtotime($inicioPrac)) / 86400));
        $pasadosDias = (int) round((strtotime($hoy) - strtotime($inicioPrac)) / 86400);
        $pctPractica = (int) round(min(100, max(0, $pasadosDias / $totalDias * 100)));

        return [
            'horasSemana'     => $horasSemana,
            'horasSemanaEsp'  => $horasSemanaEsp,
            'horasMes'        => $horasMes,
            'horasMesEsp'     => $horasMesEsp,
            // Saldo de horas del mes (HorasQuery: reales − programadas)
            'saldoMesMin'     => $saldoMes ? (int) $saldoMes['saldo_min'] : 0,
            'saldoMesTxt'     => $saldoMes
                ? \App\Libraries\Asistencia\HorasQuery::formatoMinutos((int) $saldoMes['saldo_min'])
                : '—',
            'horasMesReales'  => $saldoMes
                ? \App\Libraries\Asistencia\HorasQuery::horasDecimales((int) $saldoMes['horas_reales'])
                : '0.00',
            'horasMesProg'    => $saldoMes
                ? \App\Libraries\Asistencia\HorasQuery::horasDecimales((int) $saldoMes['horas_programadas'])
                : '0.00',
            'diasHorarioSem'  => $diasSemana,
            'inicioPractica'  => $inicioPrac,
            'finPractica'     => $finPrac,
            'pctPractica'     => $pctPractica,
            'miInfo'          => $user,
            'miHorario'       => $miHorario,
            'hoyRegistro'     => $hoyRegistro,
            'misRegistros'    => $misRegistros,
            'misIncidencias'  => $misIncidencias,
            'misAsistencias'  => $misAsistencias,
            'misTardanzas'    => $misTardanzas,
            'misFaltas'       => $misFaltas,
            'incPendientes'   => $pendientes,
            'incEnRevision'   => $enRevision,
            'festivosMes'     => $festivosMes,
            'festivosProximos'=> array_slice($festivosProx, 0, 4, true),
            'bioHuella'       => (int) ($user['huella_registrada'] ?? 0),
            'bioRostro'       => (int) ($user['rostro_registrado'] ?? 0),
            'bioRostroPath'   => (string) ($user['rostro_path'] ?? ''),
            'bioHuellaCredId' => (string) ($user['huella_cred_id'] ?? ''),
        ];
    }

    /** Horas reales trabajadas a partir de time_in → time_out */
    private function horasDe(array $registros): float
    {
        $h = 0.0;
        foreach ($registros as $r) {
            if (empty($r['date']) || empty($r['time_in']) || empty($r['time_out'])) continue;
            $s = (strtotime($r['date'] . ' ' . $r['time_out']) - strtotime($r['date'] . ' ' . $r['time_in'])) / 3600;
            if ($s > 0 && $s <= 24) $h += $s;
        }
        return round($h, 1);
    }

    /** Horas que dura la jornada diaria del horario (0 si no hay horario) */
    private function horasDiarias(?array $horario): float
    {
        if (empty($horario['hora_entrada']) || empty($horario['hora_salida'])) return 0.0;
        $s = (strtotime($horario['hora_salida']) - strtotime($horario['hora_entrada'])) / 3600;
        return $s > 0 ? round($s, 1) : 0.0;
    }

    /** Días que abarca el campo `dias` del horario (ej. "Lun - Vie" → 5). 0 = sin datos. */
    private function diasDeHorario(?string $dias): int
    {
        $d = mb_strtolower(trim((string) $dias));
        if ($d === '') return 0;
        $d = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü'], ['a', 'e', 'i', 'o', 'u', 'u'], $d);

        $orden = ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'];
        $pos   = [];
        foreach ($orden as $i => $k) {
            if (mb_strpos($d, $k) !== false) $pos[] = $i;
        }
        if (!$pos) return 0;

        // "Lun - Vie" / "Lunes a Viernes" → rango inclusivo
        if (count($pos) === 2 && preg_match('/\s(-|a|al|hasta)\s/', $d)) {
            return abs($pos[1] - $pos[0]) + 1;
        }
        return count($pos);
    }

    /** Días programados (Lun..dom según el horario) entre dos fechas */
    private function diasProgramadosEn(string $desde, string $hasta, int $diasSemana): int
    {
        if ($diasSemana <= 0) return 0;
        $n = 0;
        for ($d = $desde; $d <= $hasta && $n < 400; $d = date('Y-m-d', strtotime($d . ' +1 day'))) {
            if ((int) date('N', strtotime($d)) <= $diasSemana) $n++;
        }
        return $n;
    }

    public function dashboard()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $role = session()->get('user_role');
        $data = $this->getMyData();

        $data['activePage']  = 'dashboard';
        $data['pageTitle']   = 'Mi Panel · DSG';
        $data['greetingSub'] = $role === 'Practicante'
            ? 'Marca tu asistencia y revisa tu estado del día.'
            : 'Bienvenido a tu panel, marcas y notificaciones.';
        $data['slot'] = view($role === 'Practicante'
            ? 'empleado/practicante-dashboard'
            : 'empleado/mi-panel-dashboard', $data);

        return view('layouts/practicante-panel', $data);
    }

    // ════════════════════════════════════════
    // MI ASISTENCIA / HORARIO / INCIDENCIAS (páginas propias)
    // ════════════════════════════════════════
    public function asistencias()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'asistencias';
        $data['pageTitle']   = 'Mi Asistencia · DSG';
        $data['greetingSub'] = 'Todo tu historial de marcas del mes.';
        $data['slot'] = view('empleado/practicante-asistencias', $data);
        return view('layouts/practicante-panel', $data);
    }

    public function horario()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'horario';
        $data['pageTitle']   = 'Mi Horario · DSG';
        $data['greetingSub'] = 'Tu jornada de práctica configurada.';
        $data['slot'] = view('empleado/practicante-horario', $data);
        return view('layouts/practicante-panel', $data);
    }

    public function incidencias()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'incidencias';
        $data['pageTitle']   = 'Mis Incidencias · DSG';
        $data['greetingSub'] = 'Tus incidencias y sus justificaciones.';
        $data['slot'] = view('empleado/practicante-incidencias', $data);
        return view('layouts/practicante-panel', $data);
    }

    // ════════════════════════════════════════
    // MARCAR ASISTENCIA (entrada / salida)
    // ════════════════════════════════════════
    public function registrar()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $s = session();
        $userId = (int) $s->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            $s->setFlashdata('msg', 'Usuario no encontrado');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('mi-panel'));
        }

        // Validación de horario asignado
        $miHorario = (int) ($user['schedule_id'] ?? 0);
        if (!$miHorario) {
            $s->setFlashdata('msg', 'No tienes un horario asignado. Contacta a tu administrador.');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }

        // Biometria obligatoria: huella o rostro. Al marcar se pide uno de los dos.
        if (!(int) ($user['huella_registrada'] ?? 0) && !(int) ($user['rostro_registrado'] ?? 0)) {
            $s->setFlashdata('msg', 'Registra tu huella o tu rostro en la seccion Biometria antes de marcar asistencia');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }
        // El token sólo lo emite el servidor después de verificar
        // (verify-huella / aprobar-bio): es de uso único y caduca a los 5 min.
        if ($this->bioRateLimited($userId)) {
            $s->setFlashdata('msg', 'Demasiados intentos de marcación. Espera un minuto.');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }
        $bioErr = $this->consumeBioToken(trim((string) $this->request->getPost('bio_token')));
        if ($bioErr !== null) {
            $s->setFlashdata('msg', $bioErr);
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }

        // Evidencia del rostro (selfie de verificacion)
        $evidencia = $this->saveBioImage($this->request->getPost('evidencia') ?? '', 'evidencias');

        $hoy = date('Y-m-d');
        $ahora = date('H:i:s');
        $adminId = (int) ($user['admin_id'] ?? 0);
        $geo = $this->ubicacionMarcacion();

        // Geolocalización requerida (configurable por el administrador)
        [$geoOk, $geoValido, $geoMsg] = $this->validarGeo($adminId, $geo['lat'], $geo['lng']);
        if (! $geoOk) {
            $s->setFlashdata('msg', $geoMsg);
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }
        $geo['geo_ok'] = $geoValido;

        $att = $this->attendanceModel->findByUserAndDate($userId, $hoy);

        // Falta automática generada por el cron (sin entrada): si el colaborador
        // llegó después, se convierte en TARDANZA, no en salida.
        $faltaAuto = $att
            && ($att['status'] ?? '') === 'absent'
            && empty($att['time_in']);

        if (!$att || $faltaAuto) {
            // ENTRADA
            $entrada = date('H:i');
            $incFaltaOk = false;
            $tolerancia = (int) ($this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '')['tolerancia'] ?? 10);
            $horaBase = $this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '')['hora_entrada'] ?? '08:00';

            $limite = strtotime($horaBase) + $tolerancia * 60;
            $status = strtotime($ahora) <= $limite ? 'present' : 'late';

            $registro = [
                'admin_id'   => $adminId ?: null,
                'user_id'    => $userId,
                'name'       => $user['name'] ?? '',
                'dni'        => $user['dni'] ?? '',
                'date'       => $hoy,
                'time_in'    => $ahora,
                'time_out'   => null,
                'status'     => $status,
                'observacion'=> $faltaAuto ? 'Llegó después de la falta automática' : '',
                'evidencia'  => $evidencia,
                'lat'        => $geo['lat'],
                'lng'        => $geo['lng'],
                'ip'         => $geo['ip'],
                'geo_ok'     => $geo['geo_ok'] ?? null,
            ];

            if ($faltaAuto) {
                $this->attendanceModel->update((int) $att['id'], $registro);
                $attId = (int) $att['id'];
                // La falta automática ya no aplica: ahora es una tardanza
                $incFalta = $this->incidentModel
                    ->where('att_id', $attId)
                    ->where('tipo', 'Falta')
                    ->first();
                if ($incFalta) {
                    // Ya existe la incidencia: se reutiliza como Tardanza (no se duplica)
                    $this->incidentModel->update((int) $incFalta['id'], [
                        'tipo'    => 'Tardanza',
                        'estado'  => 'Pendiente',
                        'detalle' => "Llegó a las {$entrada} · antes se había marcado falta automática",
                    ]);
                    $incFaltaOk = true;
                }
            } else {
                $this->attendanceModel->insert($registro);
                $attId = (int) db_connect()->insertID();
            }

            if ($status === 'late' && empty($incFaltaOk)) {
                $this->incidentModel->insert([
                    'admin_id'      => $adminId ?: null,
                    'att_id'        => $attId,
                    'user_id'       => $userId,
                    'name'          => $user['name'] ?? '',
                    'tipo'          => 'Tardanza',
                    'fecha'         => date('Y-m-d H:i:s'),
                    'detalle'       => "Llegó a las {$entrada}",
                    'estado'        => 'Pendiente',
                    'justificacion' => '',
                ]);

                // Aviso por correo al empleado: debe llegar más temprano
                (new \App\Services\NotificationService())->avisarTardanza(
                    $user,
                    substr($horaBase, 0, 5),
                    $entrada,
                    $tolerancia
                );
            }

            // Push al colaborador (confirmación) y al admin (su equipo marcó)
            $svc = new \App\Services\NotificationService();
            $svc->marcacionRegistrada($attId, $adminId);
            $svc->asistenciaMarcada($attId, $adminId);

            $s->setFlashdata('msg', "Entrada registrada a las {$entrada}" . ($status === 'late' ? ' · Tardanza' : ' · Puntual'));
            $s->setFlashdata('tipo', $status === 'late' ? 'warning' : 'success');

        } elseif (empty($att['time_out'])) {
            // SALIDA
            $salida = date('H:i');
            $this->attendanceModel->update($att['id'], [
                'time_out'  => $ahora,
                'evidencia' => $evidencia ?: ($att['evidencia'] ?? ''),
                'lat'       => $geo['lat'],
                'lng'       => $geo['lng'],
                'ip'        => $geo['ip'],
                'geo_ok'    => $geo['geo_ok'] ?? null,
            ]);

            // Salida anticipada: si se retira antes de su hora de salida prevista
            $sc = $this->scheduleScore($adminId, (int) ($user['schedule_id'] ?? 0), $user['role'] ?? '');
            $horaSalidaPrevista = $sc['hora_salida'] ?? '';
            $salidaAnticipada = ($horaSalidaPrevista !== '' && strtotime($ahora) < strtotime($horaSalidaPrevista));
            if ($salidaAnticipada) {
                $just = trim((string) $this->request->getPost('justificacion'));
                $this->incidentModel->insert([
                    'admin_id'      => $adminId ?: null,
                    'att_id'        => (int) $att['id'],
                    'user_id'       => $userId,
                    'name'          => $user['name'] ?? '',
                    'tipo'          => 'Salida anticipada',
                    'fecha'         => date('Y-m-d H:i:s'),
                    'detalle'       => "Salió a las {$salida} · salida prevista " . substr($horaSalidaPrevista, 0, 5),
                    'estado'        => 'Revisión',
                    'justificacion' => $just !== '' ? $just : 'Sin motivo registrado',
                ]);

                // Aviso por correo al empleado
                (new \App\Services\NotificationService())->avisarSalidaAnticipada(
                    $user,
                    substr($horaSalidaPrevista, 0, 5),
                    $salida
                );
            }

            // Push al colaborador (confirmación de salida) y al admin
            $svc = new \App\Services\NotificationService();
            $svc->marcacionRegistrada((int) $att['id'], $adminId);
            $svc->asistenciaMarcada((int) $att['id'], $adminId);

            $s->setFlashdata('msg', "Salida registrada a las {$salida}" . ($salidaAnticipada ? ' · salida anticipada JUSTIFICADA, en revisión' : ''));
            $s->setFlashdata('tipo', 'success');

        } else {
            $s->setFlashdata('msg', 'Hoy ya registraste tu entrada y salida');
            $s->setFlashdata('tipo', 'warning');
        }

        return redirect()->to(base_url('mi-panel'));
    }

    /**
     * Geolocalización enviada por el navegador (opcional) + IP de origen.
     * Lo que no sea un par válido se guarda como NULL, nunca inventado.
     */
    private function ubicacionMarcacion(): array
    {
        $datos = ['lat' => null, 'lng' => null, 'ip' => substr((string) $this->request->getIPAddress(), 0, 45)];

        $lat = $this->request->getPost('lat');
        $lng = $this->request->getPost('lng');

        if (is_string($lat) && is_string($lng) && is_numeric($lat) && is_numeric($lng)) {
            $lat = (float) $lat;
            $lng = (float) $lng;
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180 && ! ($lat === 0.0 && $lng === 0.0)) {
                $datos['lat'] = $lat;
                $datos['lng'] = $lng;
            }
        }

        return $datos;
    }

    /**
     * Valida la ubicación contra la zona configurada del administrador.
     * @return array{0:bool,1:?int,2:string} [permite_marcar, geo_ok, mensaje]
     */
    private function validarGeo(int $adminId, ?float $lat, ?float $lng): array
    {
        $cfg = (new \App\Models\ConfigModel())->getConfig($adminId ?: null);
        $requerido = (int) ($cfg['geo_requerido'] ?? 0) === 1;
        $radio     = (int) ($cfg['geo_radio_m'] ?? 0);
        $cLat      = isset($cfg['geo_lat']) && $cfg['geo_lat'] !== null ? (float) $cfg['geo_lat'] : null;
        $cLng      = isset($cfg['geo_lng']) && $cfg['geo_lng'] !== null ? (float) $cfg['geo_lng'] : null;

        if (! $requerido || $radio <= 0 || $cLat === null || $cLng === null) {
            return [true, null, ''];
        }
        if ($lat === null || $lng === null) {
            return [false, 0, 'Tu ubicación es obligatoria para marcar asistencia. Activa los permisos de ubicación del navegador.'];
        }
        $dist = $this->distanciaMetros($cLat, $cLng, $lat, $lng);
        if ($dist > $radio) {
            $km = $dist >= 1000 ? round($dist / 1000, 1) . ' km' : round($dist) . ' m';
            return [false, 0, "Estás a {$km} del área de trabajo (radio permitido: {$radio} m). acércate y vuelve a marcar."];
        }
        return [true, 1, ''];
    }

    /** Distancia en metros entre dos coordenadas (haversine). */
    private function distanciaMetros(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return 2 * $r * asin(sqrt($a));
    }

    /** Límites de la imagen biométrica (bytes binarios). */
    private const BIO_MAX_BIN   = 3145728;   // 3 MB
    private const BIO_MAX_B64   = 4194304;   // 4 MB en base64
    private const BIO_MIN_BIN   = 200;
    private const BIO_DIRS      = ['evidencias', 'rostros'];
    private const BIO_MIME      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    private function saveBioImage(string $dataUrl, string $subdir): string
    {
        // Sólo los directorios previstos: nada de traversal.
        if (! in_array($subdir, self::BIO_DIRS, true)) {
            return '';
        }

        $dataUrl = trim($dataUrl);
        if ($dataUrl === '' || ! preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/i', $dataUrl)) {
            return '';
        }

        $base64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        if (strlen($base64) > self::BIO_MAX_B64) {
            return '';
        }

        $bin = base64_decode($base64, true);
        if ($bin === false || strlen($bin) < self::BIO_MIN_BIN || strlen($bin) > self::BIO_MAX_BIN) {
            return '';
        }

        // Debe ser una imagen real del tipo declarado (bloquea SVG/HTML embebido).
        $info = @getimagesizefromstring($bin);
        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if (! isset(self::BIO_MIME[$mime])) {
            return '';
        }

        $dir = WRITEPATH . 'uploads/' . $subdir;
        if (! is_dir($dir) && ! @mkdir($dir, 0775, true)) {
            return '';
        }

        $name = 'user_' . (int) session()->get('user_id')
            . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4))
            . '.' . self::BIO_MIME[$mime];

        if (@file_put_contents($dir . '/' . $name, $bin) === false) {
            return '';
        }

        return $subdir . '/' . $name;
    }

    public function bioSession()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Sesion invalida']);
        }

        $challenge = $this->currentChallenge($this->request->getGet('fresh') !== null);

        return $this->response->setJSON([
            'ok'               => true,
            'csrfHash'         => csrf_hash(),
            'challenge'        => $challenge,
            'userHandle'       => $this->webauthn->userHandleB64($userId),
            'rpId'             => $this->webauthn->host(),
            'origin'           => $this->webauthn->origin(),
            'userName'         => $user['name'] ?? 'Usuario',
            'userEmail'        => $user['email'] ?? '',
            'credId'           => $user['huella_cred_id'] ?? '',
            'huellaRegistrada' => (int) ($user['huella_registrada'] ?? 0),
            'rostroRegistrado' => (int) ($user['rostro_registrado'] ?? 0),
            'rostroPath'       => ($user['rostro_path'] ?? '') !== ''
                ? base_url('mi-panel/evidencia/rostros/' . basename((string) $user['rostro_path']))
                : '',
        ]);
    }

    // ════════════════════════════════════════════════════════
    // ESTADO ACTUAL - Para polling (fallback universal, funciona en cPanel)
    // ═══════════════════════════════════════════════════════
    public function estadoActual()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $hoy    = date('Y-m-d');

        $att = $this->attendanceModel
            ->where('user_id', $userId)
            ->where('date', $hoy)
            ->first();

        return $this->response->setJSON([
            'ok'            => true,
            'asistencia'    => $att,
            'server_time'   => time(),
            'csrfHash'      => csrf_hash(),
        ]);
    }

    /**
     * Challenge vigente de la sesion WebAuthn. Solo se regenera si el flujo lo
     * pide (`?fresh=1`), si no existe o si expiro: asi el refresco de CSRF que
     * hace el cliente no invalida la ceremonia que esta en curso.
     */
    private function currentChallenge(bool $force): string
    {
        $challenge = (string) session()->get('wa_challenge');
        $ts        = (int) session()->get('wa_challenge_ts');

        if ($force || $challenge === '' || (time() - $ts) > 300) {
            $challenge = WebAuthnService::b64e(random_bytes(32));
            session()->set(['wa_challenge' => $challenge, 'wa_challenge_ts' => time()]);
        }

        return $challenge;
    }

    /** Lee y consume el challenge (uso unico) antes de validar la respuesta. */
    private function takeChallenge(): ?string
    {
        $challenge = (string) session()->get('wa_challenge');
        session()->remove(['wa_challenge', 'wa_challenge_ts']);

        return $challenge === '' ? null : $challenge;
    }

    // ════════════════════════════════════════════════════════
    // TOKEN DE VERIFICACIÓN BIOMÉTRICA — uso único, TTL 5 min
    // ════════════════════════════════════════════════════════

    private const BIO_TTL      = 300; // segundos de validez
    private const BIO_RATE_MAX = 12;  // intentos de verificación por minuto

    /**
     * Emite el permiso para marcar. Sólo el servidor sabe que la verificación
     * se completó: el cliente recibe el token y lo devuelve en el POST de
     * marcación. Un `bio_ok=1` fijado en el HTML ya no sirve para nada.
     */
    private function issueBioToken(string $metodo): array
    {
        $token = bin2hex(random_bytes(16));
        session()->set([
            'bio_token'      => $token,
            'bio_token_meth' => $metodo,
            'bio_token_exp'  => time() + self::BIO_TTL,
        ]);

        return ['bio_token' => $token, 'bio_metodo' => $metodo];
    }

    /** Lee y consume el token (uso único). Devuelve el mensaje de error o null. */
    private function consumeBioToken(string $posted): ?string
    {
        $s        = session();
        $esperado = (string) $s->get('bio_token');
        $exp      = (int) $s->get('bio_token_exp');
        $s->remove(['bio_token', 'bio_token_meth', 'bio_token_exp']);

        if ($posted === '' || $esperado === '' || ! hash_equals($esperado, $posted)) {
            return 'Verifica tu huella o tu rostro para marcar asistencia';
        }
        if ($exp < time()) {
            return 'La verificación biométrica expiró. Vuelve a verificar tu huella o tu rostro.';
        }

        return null;
    }

    /** Tope de intentos por usuario: nadie martillear la verificación ni la marcación. */
    private function bioRateLimited(int $userId): bool
    {
        $key = 'bio_rate_' . $userId;
        $n   = (int) (cache()->get($key) ?? 0);
        if ($n >= self::BIO_RATE_MAX) {
            return true;
        }
        cache()->save($key, $n + 1, 60);

        return false;
    }

    public function guardarHuella()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        $mode   = (string) $this->request->getPost('mode');

        if ($mode === 'sim') {
            $credId = trim((string) $this->request->getPost('credential_id'));
            if ($credId === '') {
                return $this->response->setJSON(['ok' => false, 'msj' => 'No se recibio la huella']);
            }
            if (strlen($credId) > 300) $credId = substr($credId, 0, 300);

            $this->credentialModel->clearUser($userId);
            $this->userModel->update($userId, [
                'huella_registrada' => 2, // 2 = simulada de prueba, 1 = real (WebAuthn)
                'huella_fecha'      => date('Y-m-d H:i:s'),
                'huella_cred_id'    => $credId,
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella registrada (simulacion de prueba)']);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Sesion invalida']);
        }

        $challenge = $this->takeChallenge();
        if ($challenge === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'El registro expiro. Vuelve a intentarlo.']);
        }

        try {
            $options = $this->webauthn->creationOptions(
                $challenge,
                $userId,
                (string) ($user['name'] ?? ''),
                (string) ($user['email'] ?? '')
            );
            $record = $this->webauthn->verifyAttestation($challenge, $options, $this->request->getPost());
            $row    = $this->webauthn->toRow($record, $userId);

            if ($this->credentialModel->findByCredentialId($row['credential_id']) !== null) {
                return $this->response->setJSON(['ok' => false, 'msj' => 'Esa huella ya esta registrada en otra cuenta.']);
            }

            $this->credentialModel->clearUser($userId);
            $this->credentialModel->insert($row);

            $this->userModel->update($userId, [
                'huella_registrada' => 1,
                'huella_fecha'      => date('Y-m-d H:i:s'),
                'huella_cred_id'    => $row['credential_id'],
            ]);

            return $this->response->setJSON(['ok' => true, 'msj' => 'Huella registrada correctamente']);
        } catch (\Throwable $e) {
            log_message('error', 'WebAuthn (registro) fallido: ' . $e->getMessage());

            return $this->response->setJSON([
                'ok'  => false,
                'msj' => 'No se pudo validar la huella. ' . $e->getMessage(),
            ]);
        }
    }

    /** Comprueba en el servidor la firma de la assertion (huella real). */
    public function verifyHuella()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        if ($this->bioRateLimited($userId)) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Demasiados intentos. Espera un minuto.']);
        }
        $row = $this->credentialModel->findByUser($userId);
        if ($row === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'No tienes una huella registrada.']);
        }

        $challenge = $this->takeChallenge();
        if ($challenge === null) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'La verificacion expiro. Pulsa de nuevo.']);
        }

        try {
            $record  = $this->webauthn->fromRow($row);
            $updated = $this->webauthn->verifyAssertion($record, $challenge, $this->request->getPost());

            $this->credentialModel->update($row['id'], [
                'counter'      => $updated->counter,
                'last_used_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON(array_merge(
                ['ok' => true, 'msj' => 'Huella verificada'],
                $this->issueBioToken('huella')
            ));
        } catch (\Throwable $e) {
            log_message('error', 'WebAuthn (verificacion) fallida: ' . $e->getMessage());

            return $this->response->setJSON(['ok' => false, 'msj' => 'Huella no reconocida. Reintenta o verifica con el rostro.']);
        }
    }

    /**
     * Aprobación de la marcación por rostro (o por "sim-" cuando no hay
     * lector real). El servidor compara la selfie con la foto de referencia
     * guardada y, si coincide, emite el token de marcación.
     *
     * El cotejo facial fino lo hace el cliente con face-api.js; aquí se
     * valida al menos que exista referencia + selfie y que el cotejo haya
     * sido declarado por el cliente tras pasar por esta ruta (nunca un
     * flag fijo en el HTML).
     */
    public function aprobarBio()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');
        if ($this->bioRateLimited($userId)) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Demasiados intentos. Espera un minuto.']);
        }

        $metodo = trim((string) $this->request->getPost('metodo'));
        if (! in_array($metodo, ['facial', 'sim'], true)) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Metodo de verificacion invalido.']);
        }

        $user = $this->userModel->find($userId);
        if (! $user) {
            return $this->response->setJSON(['ok' => false, 'msj' => 'Sesion invalida.']);
        }

        if ($metodo === 'facial') {
            if (! (int) ($user['rostro_registrado'] ?? 0) || trim((string) ($user['rostro_path'] ?? '')) === '') {
                return $this->response->setJSON(['ok' => false, 'msj' => 'No tienes un rostro registrado.']);
            }
            $selfie = trim((string) $this->request->getPost('selfie'));
            if ($selfie === '') {
                return $this->response->setJSON(['ok' => false, 'msj' => 'Toma la foto para verificar tu rostro.']);
            }
        } else {
            // Modo sin lector: exige la huella/rostro registrados igual que
            // el resto del flujo (la vista sólo ofrece "sim" si hay credencial).
            if (! (int) ($user['huella_registrada'] ?? 0) && ! (int) ($user['rostro_registrado'] ?? 0)) {
                return $this->response->setJSON(['ok' => false, 'msj' => 'Registra tu huella o tu rostro antes de marcar.']);
            }
        }

        return $this->response->setJSON(array_merge(
            ['ok' => true, 'msj' => 'Verificado'],
            $this->issueBioToken($metodo)
        ));
    }

    public function guardarRostro()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $userId = (int) session()->get('user_id');

        $dataUrl = trim($this->request->getPost('imagen') ?? '');
        $path = '';
        if ($dataUrl !== '') {
            $path = $this->saveBioImage($dataUrl, 'rostros');
        } else {
            $file = $this->request->getFile('rostro');
            if ($file && $file->isValid() && $file->getSize() > 0) {
                $dir = WRITEPATH . 'uploads/rostros';
                if (!is_dir($dir)) {
                    @mkdir($dir, 0775, true);
                }
                $name = 'user_' . $userId . '_' . date('YmdHis') . '.jpg';
                $file->move($dir, $name);
                $path = 'rostros/' . $name;
            }
        }

        if ($path === '') {
            return $this->response->setJSON(['ok' => false, 'msj' => 'No se pudo guardar el rostro']);
        }

        $this->userModel->update($userId, [
            'rostro_registrado' => 1,
            'rostro_fecha'      => date('Y-m-d H:i:s'),
            'rostro_path'       => $path,
        ]);
        return $this->response->setJSON(['ok' => true, 'msj' => 'Rostro registrado correctamente', 'path' => base_url('mi-panel/evidencia/rostros/' . basename($path))]);
    }

    /**
     * Elige el horario de una persona: NUNCA mezcla tipos.
     * Un Empleado jamás ve un horario Practicante ni viceversa.
     */
    private function pickSchedule(array $schedules, int $scheduleId, string $role): array
    {
        $tipoRole = $role === 'Practicante' ? 'Practicante' : 'Empleado';

        // Solo horarios cuyo tipo coincide con el rol (getActive ordena id DESC)
        $delTipo = array_values(array_filter(
            $schedules,
            fn($s) => ($s['tipo'] ?? 'Empleado') === $tipoRole
        ));

        // 1. El asignado, solo si es del tipo correcto
        if ($scheduleId > 0) {
            foreach ($delTipo as $sch) {
                if ((int) ($sch['id'] ?? 0) === $scheduleId) return $sch;
            }
        }

        // 2. El más reciente del tipo correcto; si no hay, [] (sin horario)
        return $delTipo[0] ?? [];
    }

    private function scheduleScore(int $adminId, int $scheduleId = 0, string $role = ''): array
    {
        $schedules = $this->scheduleModel->getActive($adminId > 0 ? $adminId : null);
        return $this->pickSchedule($schedules, $scheduleId, $role);
    }

    // ════════════════════════════════════════
    // JUSTIFICAR UNA INCIDENCIA PROPIA
    // ════════════════════════════════════════
    public function justificarIncidencia()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $s = session();
        $userId = (int) $s->get('user_id');
        $incId  = (int) $this->request->getPost('inc_id');

        $inc = $this->incidentModel->find($incId);
        if (!$inc || (int) ($inc['user_id'] ?? -1) !== $userId) {
            $s->setFlashdata('msg', 'Esta incidencia no te pertenece');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('mi-panel'));
        }

        // Una incidencia ya resuelta por el admin no se vuelve a enviar a revisión
        if (in_array((string) ($inc['estado'] ?? ''), \App\Models\IncidentModel::RESUELTOS, true)) {
            $s->setFlashdata('msg', 'Esta incidencia ya fue resuelta por tu administrador');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel/incidencias'));
        }

        $just = trim($this->request->getPost('justificacion') ?? '');
        if ($just === '') {
            $s->setFlashdata('msg', 'Escribe tu justificacion');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel'));
        }

        // Foto opcional de respaldo (jpg/png/webp ≤ 4 MB)
        $fotoErr = null;
        $foto    = $this->guardarFotoJustificacion($userId, $fotoErr);
        if ($fotoErr !== null) {
            $s->setFlashdata('msg', $fotoErr);
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('mi-panel/incidencias'));
        }

        $data = [
            'estado'        => 'Revisión',
            'justificacion' => $just,
        ];
        if ($foto !== null) {
            $data['evidencia'] = $foto;
        }
        $this->incidentModel->update($incId, $data);

        // Aviso al admin (in-app + push): hay una justificación por revisar
        (new \App\Services\NotificationService())
            ->incidenciaEnRevision($incId, (int) ($inc['admin_id'] ?? 0));

        $s->setFlashdata('msg', 'Justificacion enviada · queda en revisión'
            . ($foto !== null ? ' · con foto adjunta' : ''));
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('mi-panel/incidencias'));
    }

    /**
     * Guarda la foto opcional de una justificación en
     * writable/uploads/evidencias con el nombre plano que ya sirve
     * EvidenciaController (user_{id}_{fecha}_{hex}.{jpg|png|webp}).
     * Devuelve el nombre, o null si no envió foto; $error si es inválida.
     */
    private function guardarFotoJustificacion(int $userId, ?string &$error): ?string
    {
        $error = null;
        $file  = $this->request->getFile('evidencia');
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid()) {
            $error = 'La foto no se pudo subir. Intenta de nuevo.';
            return null;
        }

        $mime = (string) $file->getMimeType();
        $ext  = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => '',
        };
        if ($ext === '') {
            $error = 'Formato de foto no permitido (usa JPG, PNG o WEBP)';
            return null;
        }
        if ($file->getSize() > 4 * 1024 * 1024) {
            $error = 'La foto supera el máximo de 4 MB';
            return null;
        }

        $dir = WRITEPATH . 'uploads/evidencias';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $nombre = 'user_' . $userId . '_' . date('YmdHis')
            . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

        try {
            $file->move($dir, $nombre);
        } catch (\Throwable $e) {
            log_message('error', 'Foto de justificación no guardada: ' . $e->getMessage());
            $error = 'No se pudo guardar la foto. Reintenta.';
            return null;
        }

        return $nombre;
    }

    // ══════════════════════════════════════════════════════
    // CREAR INCIDENCIA (tardanza, falta, etc.)
    // ══════════════════════════════════════════════════════

    public function crearIncidencia()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $s = session();
        $userId = (int) $s->get('user_id');
        $user   = $this->userModel->find($userId);

        $permitidos = ['Tardanza', 'Falta', 'Salida anticipada', 'Sin marcación', 'Otro'];
        $tipo   = trim((string) $this->request->getPost('tipo'));
        $fecha  = trim((string) $this->request->getPost('fecha'));
        $detalle = trim((string) $this->request->getPost('detalle'));
        $attId  = (int) $this->request->getPost('att_id');

        if (! in_array($tipo, $permitidos, true)) {
            $s->setFlashdata('msg', 'Tipo de incidencia no válido');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel/incidencias'));
        }
        if ($fecha === '' || ! strtotime($fecha)) {
            $fecha = date('Y-m-d');
        }
        if ($detalle === '') {
            $s->setFlashdata('msg', 'Cuéntanos qué ocurrió para poder revisarlo');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('mi-panel/incidencias'));
        }

        // att_id debe apuntar a una asistencia del propio usuario (nunca de otro)
        if ($attId > 0) {
            $att = $this->attendanceModel->find($attId);
            if (! $att || (int) ($att['user_id'] ?? -1) !== $userId) {
                $s->setFlashdata('msg', 'La asistencia indicada no te pertenece');
                $s->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('mi-panel/incidencias'));
            }
            // Ya existe una incidencia abierta para esa asistencia
            $yaInc = $this->incidentModel->where('att_id', $attId)->first();
            if ($yaInc && ! in_array((string) ($yaInc['estado'] ?? ''), \App\Models\IncidentModel::RESUELTOS, true)) {
                $s->setFlashdata('msg', 'Ya hay una incidencia en revisión para esa asistencia');
                $s->setFlashdata('tipo', 'warning');
                return redirect()->to(base_url('mi-panel/incidencias'));
            }
        }

        $incId = $this->incidentModel->insert([
            'admin_id' => (int) ($user['admin_id'] ?? 0),
            'att_id'   => $attId > 0 ? $attId : null,
            'user_id'  => $userId,
            'name'     => (string) ($user['name'] ?? ''),
            'tipo'     => $tipo,
            'fecha'    => $fecha . ' ' . date('H:i:s'),
            'detalle'  => $detalle,
            'estado'   => 'Pendiente',
        ], true);

        // Avisar al administrador
        if ($incId) {
            (new \App\Services\NotificationService())
                ->incidenciaCreada((int) $incId, (int) ($user['admin_id'] ?? 0));
        }

        $s->setFlashdata('msg', 'Incidencia enviada · tu administrador la revisará');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('mi-panel/incidencias'));
    }

    // ══════════════════════════════════════════════════════
    // MI PERFIL (datos, notificaciones, apariencia)
    // ══════════════════════════════════════════════════════

    public function perfil()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;

        $data = $this->getMyData();
        $data['activePage']  = 'perfil';
        $data['pageTitle']   = 'Mi Perfil · DSG';
        $data['greetingSub'] = 'Tu información y preferencias del panel.';
        $data['slot'] = view('empleado/practicante-perfil', $data);
        return view('layouts/practicante-panel', $data);
    }

    // ══════════════════════════════════════════════════════
    // NOTIFICACIONES DEL EMPLEADO (campana del panel)
    // ══════════════════════════════════════════════════════

    /**
     * Lista para el modal de la campana
     * GET /mi-panel/notificaciones
     */
    public function notificaciones()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;
        $userId = (int) session()->get('user_id');
        $model  = new NotificationModel();

        return $this->response->setJSON([
            'ok'             => true,
            'no_leidas'      => $model->getUnreadCountForUser($userId),
            'notificaciones' => $model->getForUser($userId, 30),
        ]);
    }

    /**
     * Marca como leídas: una por id o todas
     * POST /mi-panel/notificaciones/marcar-leidas
     */
    public function marcarLeidas()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;
        $userId = (int) session()->get('user_id');
        $model  = new NotificationModel();
        $id     = (int) $this->request->getPost('id');

        if ($id > 0) {
            $model->markAsReadForUser($id, $userId);
        } else {
            $model->markAllAsReadForUser($userId);
        }

        return $this->response->setJSON(['ok' => true, 'no_leidas' => $model->getUnreadCountForUser($userId)]);
    }

    /**
     * Contador ligero para el badge (no carga la lista)
     * GET /mi-panel/notificaciones/badge
     */
    public function badgeNotificaciones()
    {
        if ($redirect = $this->checkEmployee()) return $redirect;
        $userId = (int) session()->get('user_id');
        $model  = new NotificationModel();

        return $this->response->setJSON([
            'ok'        => true,
            'no_leidas' => $model->getUnreadCountForUser($userId),
        ]);
    }
}
