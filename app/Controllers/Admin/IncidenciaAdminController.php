<?php
namespace App\Controllers\Admin;

use App\Models\IncidentModel;
use App\Services\NotificationService;

/**
 * Incidencias del equipo: alta, resolución y aviso por correo.
 */
class IncidenciaAdminController extends AdminBaseController
{
    protected NotificationService $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->notificationService = new NotificationService();
    }

    public function incidencias()
    {
        if ($redirect = $this->guard()) return $redirect;

        $params  = $this->request->getGet();
        $adminId = $this->adminId();

        // Filtrado por fecha y persona: es lo que alimenta métricas y chips
        $todas     = $this->incidentModel->getAll($adminId);
        $filtradas = $this->porFecha($todas, $params);

        // La tabla además aplica estado y pagina (antes: todo de golpe, sin paginar)
        $paraTabla = $this->porEstado($filtradas, (string) ($params['estado'] ?? ''));
        $paginado  = $this->paginar(
            $paraTabla,
            (int) ($params['page'] ?? 1),
            $params
        );
        $pagina = $paginado['filas'];
        unset($paginado['filas']);

        return view('admin/incidencias', array_merge($this->viewData([
            'activePage'    => 'incidencias',
            'incidents'     => $filtradas,
            'incidentsPage' => $pagina,
            'persona'       => trim((string) ($params['persona'] ?? '')),
            'incEstado'     => trim((string) ($params['estado'] ?? '')),
            'fecha_inicio'  => trim((string) ($params['fecha_inicio'] ?? '')),
            'fecha_fin'     => trim((string) ($params['fecha_fin'] ?? '')),
        ]), $paginado));
    }

    /** Deja sólo las incidencias de un rango de fechas (vacío = todas). */
    private function porFecha(array $incs, array $params): array
    {
        $fi = is_string($params['fecha_inicio'] ?? null) ? trim($params['fecha_inicio']) : '';
        $ff = is_string($params['fecha_fin'] ?? null) ? trim($params['fecha_fin']) : '';
        if ($fi === '' && $ff === '') {
            return $incs;
        }
        if ($fi !== '' && $ff !== '' && $fi > $ff) {
            [$fi, $ff] = [$ff, $fi];
        }

        return array_values(array_filter($incs, function ($i) use ($fi, $ff) {
            $d = substr((string) ($i['fecha'] ?? ''), 0, 10);
            if ($d === '') {
                return false;
            }
            if ($fi !== '' && $d < $fi) {
                return false;
            }

            return ! ($ff !== '' && $d > $ff);
        }));
    }

    /** Deja sólo las incidencias con el estado pedido. Vacío = todas. */
    private function porEstado(array $incs, string $estado): array
    {
        $estado = trim($estado);
        if ($estado === '' || $estado === 'todas') {
            return $incs;
        }

        return array_values(array_filter($incs, function ($i) use ($estado) {
            $e = (string) ($i['estado'] ?? '');
            if ($estado === 'Rechazada') {
                return in_array($e, ['Rechazada', 'Desestimada'], true);
            }

            return $e === $estado;
        }));
    }

    public function createIncident()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();

        if ($redirect = $this->validar([
            'inc_user'    => 'required|integer|greater_than[0]',
            'inc_tipo'    => 'permit_empty|in_list[Tardanza,Falta,Salida anticipada,Otro]',
            'inc_fecha'   => 'permit_empty|regex_match[/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2})?$/]',
            'inc_detalle' => 'required|trim|min_length[3]|max_length[500]',
        ], 'admin/incidencias')) {
            return $redirect;
        }

        $userId  = (int) $this->request->getPost('inc_user');
        $tipo    = $this->request->getPost('inc_tipo') ?? 'Otro';
        $fecha   = trim((string) ($this->request->getPost('inc_fecha') ?? ''));
        $detalle = trim((string) $this->request->getPost('inc_detalle'));

        $u = $this->userModel->find($userId);
        if (!$u || (int) ($u['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Elige una persona de tu personal');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/incidencias'));
        }
        $tipos = ['Tardanza', 'Falta', 'Salida anticipada', 'Otro'];
        if (!in_array($tipo, $tipos, true)) $tipo = 'Otro';

        $this->incidentModel->insert([
            'admin_id'      => $adminId,
            'att_id'        => null,
            'user_id'       => $userId,
            'name'          => $u['name'] ?? '',
            'tipo'          => $tipo,
            'fecha'         => $fecha !== '' ? date('Y-m-d H:i:s', strtotime($fecha)) : date('Y-m-d H:i:s'),
            'detalle'       => $detalle,
            'estado'        => 'Pendiente',
            'justificacion' => '',
        ]);

        // Notificar a admins sobre incidencia creada
        $incId = (int) db_connect()->insertID();
        $this->notificationService->incidenciaCreada($incId, $adminId);

        $s->setFlashdata('msg', 'Incidencia registrada y asignada a ' . ($u['name'] ?? 'la persona'));
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/incidencias'));
    }

    public function updateIncident()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $incId  = (int) $this->request->getPost('inc_id');

        $inc = $this->incidentModel->find($incId);
        if (!$inc || (int) ($inc['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/incidencias'));
        }

        $estado = $this->request->getPost('inc_estado');
        $just   = trim($this->request->getPost('inc_justificacion') ?? '');

        if (! in_array($estado, IncidentModel::ESTADOS, true)) {
            $s->setFlashdata('msg', 'Estado de incidencia no válido');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/incidencias'));
        }

        $data = [
            'estado'        => $estado,
            'justificacion' => $just,
        ];
        if (in_array($estado, IncidentModel::RESUELTOS, true)) {
            $data['resuelta_por'] = (string) (session()->get('user_name') ?: 'admin');
            $data['resuelta_en']  = date('Y-m-d H:i:s');
        }

        $this->incidentModel->update($incId, $data);

        // Falta justificada → la asistencia deja de contar como falta
        if (($inc['tipo'] ?? '') === 'Falta' && !empty($inc['att_id'])) {
            if ($estado === 'Justificada') {
                $this->attendanceModel->update((int) $inc['att_id'], ['status' => 'absent_justified']);
            } elseif (($inc['estado'] ?? '') === 'Justificada') {
                // Se revirtió de Justificada: vuelve a contar como falta
                $this->attendanceModel->update((int) $inc['att_id'], ['status' => 'absent']);
            }
        }

        // Notificar a admins sobre incidencia actualizada
        $this->notificationService->incidenciaActualizada($incId, $adminId, $estado);

        $s->setFlashdata('msg', "Incidencia actualizada a «{$estado}»");
        $s->setFlashdata('tipo', 'success');
        $this->notificarResolucion($inc, $estado, $just);
        return redirect()->to(base_url('admin/incidencias'));
    }

    /**
     * (#12) Avisa por correo cuando se resuelve una incidencia.
     * Nunca rompe el flujo: si el correo falla, sólo se registra.
     */
    private function notificarResolucion(array $inc, string $estado, string $nota): void
    {
        try {
            $cfg = $this->configModel->getConfig($this->adminId());

            if (empty($cfg['notif_email']) || trim((string) ($cfg['notif_email_dest'] ?? '')) === '') {
                return;
            }

            $destino = trim((string) $cfg['notif_email_dest']);
            if (! filter_var($destino, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            $nombre = (string) ($inc['name'] ?? 'un miembro del equipo');
            $fecha  = (string) ($inc['fecha'] ?? date('Y-m-d H:i:s'));
            $tipo   = (string) ($inc['tipo'] ?? 'Incidencia');

            $email = service('email');
            $email->setTo($destino);
            $email->setFrom(
                trim((string) ($cfg['email'] ?? '')) ?: 'no-reply@dsg.pe',
                (string) ($cfg['empresa'] ?? 'DSG')
            );
            $email->setSubject("Incidencia {$estado} · {$tipo}");
            $email->setMessage(
                "<p>La incidencia de <strong>{$nombre}</strong> fue <strong>{$estado}</strong>.</p>"
                . "<p><strong>Tipo:</strong> {$tipo}<br><strong>Fecha:</strong> {$fecha}</p>"
                . ($nota !== '' ? "<p><strong>Nota:</strong> {$nota}</p>" : '')
                . '<p>-- Panel de asistencia DSG</p>'
            );
            $email->setMailType('html');
            $email->send(false);
        } catch (\Throwable $e) {
            log_message('error', 'No se pudo enviar el correo de incidencia: {m}', ['m' => $e->getMessage()]);
        }
    }
}
