<?php
namespace App\Controllers\Admin;

use App\Libraries\Asistencia\HorasQuery;
use App\Models\FestivoModel;

/**
 * Resumen de horas por persona: horas reales vs. programadas, saldo,
 * tardanzas y faltas de un rango de fechas. Exportable a CSV.
 */
class HorasAdminController extends AdminBaseController
{
    /** Días de la ventana por defecto: el mes en curso. */
    private function rango(): array
    {
        $params = $this->request->getGet();
        $fi = is_string($params['fecha_inicio'] ?? null) ? trim($params['fecha_inicio']) : '';
        $ff = is_string($params['fecha_fin'] ?? null) ? trim($params['fecha_fin']) : '';

        if ($fi === '' || $ff === '' || ! strtotime($fi) || ! strtotime($ff)) {
            return [date('Y-m-01'), date('Y-m-d')];
        }
        if ($fi > $ff) {
            [$fi, $ff] = [$ff, $fi];
        }

        return [$fi, $ff];
    }

    private function calcular(string $fi, string $ff): array
    {
        $adminId = $this->adminId();

        $filas = $this->attendanceModel->getByRange($fi, $ff, $adminId);

        // Personas: mismo criterio que el resto del panel (con horario asignado)
        $personal = array_values(array_filter(
            $this->getPersonal(),
            static fn ($p) => ($p['estado'] ?? '') !== 'Retirado' && ($p['estado'] ?? '') !== 'Despedido'
        ));

        $horarios = [];
        foreach ($this->scheduleModel->getAll($adminId) as $h) {
            $horarios[(int) $h['id']] = $h;
        }

        $festivos = (new FestivoModel())->fechasEnRango($fi, $ff, $adminId);

        $consulta = new HorasQuery();

        return $consulta->porEmpleado($filas, $personal, $horarios, $festivos, $fi, $ff);
    }

    // ══════════════════════════════════════════════
    // PÁGINA
    // ══════════════════════════════════════════════
    public function index()
    {
        if ($redirect = $this->guard()) return $redirect;

        [$fi, $ff] = $this->rango();
        $resultado  = $this->calcular($fi, $ff);

        // Búsqueda por persona (se filtra en el servidor, como en asistencias)
        $q = trim((string) $this->request->getGet('persona'));
        if ($q !== '') {
            $needle = mb_strtolower($q);
            $resultado['filas'] = array_values(array_filter(
                $resultado['filas'],
                static function ($f) use ($needle) {
                    return mb_strpos(mb_strtolower($f['name']), $needle) !== false
                        || ($f['dni'] !== '' && mb_strpos($f['dni'], $needle) !== false);
                }
            ));
        }

        return view('admin/horas', $this->viewData([
            'activePage'   => 'horas',
            'pageTitle'    => 'Horas por persona · DSG',
            'horas'        => $resultado['filas'],
            'horasTotal'   => $resultado['total'],
            'fecha_inicio' => $fi,
            'fecha_fin'    => $ff,
            'busqueda'     => $q,
        ]));
    }

    // ══════════════════════════════════════════════
    // EXPORTAR CSV (server-side, todo el rango)
    // ══════════════════════════════════════════════
    public function exportar()
    {
        if ($redirect = $this->guard()) return $redirect;

        $post = $this->request->getPost();
        $fi = is_string($post['fecha_inicio'] ?? null) ? trim($post['fecha_inicio']) : '';
        $ff = is_string($post['fecha_fin'] ?? null) ? trim($post['fecha_fin']) : '';
        if ($fi === '' || $ff === '' || ! strtotime($fi) || ! strtotime($ff)) {
            [$fi, $ff] = [date('Y-m-01'), date('Y-m-d')];
        }
        if ($fi > $ff) {
            [$fi, $ff] = [$ff, $fi];
        }

        $resultado = $this->calcular($fi, $ff);
        $filas     = $resultado['filas'];

        $q = trim((string) ($post['persona'] ?? ''));
        if ($q !== '') {
            $needle = mb_strtolower($q);
            $filas = array_values(array_filter(
                $filas,
                static fn ($f) => mb_strpos(mb_strtolower($f['name']), $needle) !== false
                    || ($f['dni'] !== '' && mb_strpos($f['dni'], $needle) !== false)
            ));
        }

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="horas_' . $fi . '_' . $ff . '.csv"')
            ->setBody($this->csv($filas));
    }

    /** CSV del resumen de horas (separador ';' + BOM, igual que asistencias). */
    private function csv(array $filas): string
    {
        $salida = "\xEF\xBB\xBF";
        $salida .= implode(';', [
            'Persona', 'DNI', 'Rol', 'Horario', 'Dias laborables',
            'Dias trabajados', 'Horas reales', 'Horas programadas',
            'Saldo (h)', 'Tardanzas', 'Faltas', 'Sin salida', 'Jornadas estimadas',
        ]) . "\r\n";

        foreach ($filas as $f) {
            $campos = [
                $f['name'],
                $f['dni'],
                $f['role'],
                $f['horario_txt'],
                $f['dias_laborables'],
                $f['dias_trabajados'],
                HorasQuery::horasDecimales($f['horas_reales']),
                HorasQuery::horasDecimales($f['horas_programadas']),
                HorasQuery::horasDecimales($f['saldo_min']),
                $f['tardanzas'],
                $f['faltas'],
                $f['sin_salida'],
                $f['estimadas'],
            ];
            $salida .= implode(';', array_map(
                static fn ($v) => preg_replace('/[\r\n;]+/', ' ', (string) $v),
                $campos
            )) . "\r\n";
        }

        return $salida;
    }
}
