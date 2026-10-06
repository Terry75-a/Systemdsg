<?php
namespace App\Controllers\Admin;

use App\Models\IncidentModel;

/**
 * Incidencias del equipo: alta, resolución y aviso por correo.
 */
class IncidenciaAdminController extends AdminBaseController
{
    public function incidencias()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/incidencias', $this->viewData(['activePage' => 'incidencias']));
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
