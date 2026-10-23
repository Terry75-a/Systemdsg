<?php
namespace App\Controllers\Admin;

/**
 * Configuración del admin: datos de empresa, notificaciones y festivos.
 */
class ConfigAdminController extends AdminBaseController
{
    public function configuracion()
    {
        if ($redirect = $this->guard()) return $redirect;
        return view('admin/configuracion', $this->viewData([
            'activePage' => 'configuracion',
            'config'     => $this->configModel->getConfig($this->adminId()),
            'festivos'   => $this->festivoModel->getAll($this->adminId()),
        ]));
    }

    // ════════════════════════════════════════
    // FESTIVOS (días no laborables)
    // ════════════════════════════════════════
    public function saveFestivo()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s       = session();
        $adminId = $this->adminId();

        if ($redirect = $this->validar([
            'fest_fecha'  => 'required|valid_date',
            'fest_nombre' => 'required|trim|min_length[2]|max_length[120]',
        ], 'admin/configuracion', [
            'fest_fecha'  => ['required' => 'Pon la fecha del festivo'],
            'fest_nombre' => ['required' => 'Ponle un nombre al festivo'],
        ])) {
            return $redirect;
        }

        $fecha  = trim((string) $this->request->getPost('fest_fecha'));
        $nombre = trim((string) $this->request->getPost('fest_nombre'));

        $existe = $this->festivoModel->where('fecha', $fecha)->first();
        if ($existe) {
            $this->festivoModel->update((int) $existe['id'], ['nombre' => $nombre, 'admin_id' => $adminId]);
            $s->setFlashdata('msg', "Festivo del {$fecha} actualizado");
        } else {
            $this->festivoModel->insert(['fecha' => $fecha, 'nombre' => $nombre, 'admin_id' => $adminId]);
            $s->setFlashdata('msg', "Festivo {$fecha} agregado: no se marcarán faltas ese día");
        }
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/configuracion'));
    }

    public function deleteFestivo($id = null)
    {
        if ($redirect = $this->guard()) return $redirect;
        $s       = session();
        $adminId = $this->adminId();

        $id = $id ?? $this->request->getPost('fest_id');
        if (empty($id) || ! ctype_digit((string) $id)) {
            $s->setFlashdata('msg', 'Festivo inválido');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/configuracion'));
        }

        $festivo = $this->festivoModel->find((int) $id);
        // Los globales (admin_id NULL) los borra cualquiera de los admins;
        // los propios, sólo su admin.
        if (! $festivo || (isset($festivo['admin_id']) && $festivo['admin_id'] !== null && (int) $festivo['admin_id'] !== $adminId)) {
            $s->setFlashdata('msg', 'Este festivo no te pertenece');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/configuracion'));
        }

        $this->festivoModel->delete((int) $id);
        $s->setFlashdata('msg', 'Festivo eliminado');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/configuracion'));
    }

    // ════════════════════════════════════════
    // CONFIGURACION (por admin)
    // ════════════════════════════════════════
    public function saveConfig()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $post = $this->request->getPost();

        $data = [];
        $map = [
            'cfg_empresa'         => ['empresa',            fn ($v) => trim($v) ?: 'DSG PERU TECHNOLOGY SAC'],
            'cfg_ruc'             => ['ruc',                fn ($v) => trim($v)],
            'cfg_direccion'       => ['direccion',          fn ($v) => trim($v)],
            'cfg_email'           => ['email',              fn ($v) => trim($v)],
            'cfg_telefono'        => ['telefono',           fn ($v) => trim($v)],
            'cfg_horario_default' => ['horario_default',    fn ($v) => trim($v) ?: '08:00-17:00'],
            'cfg_tolerancia'      => ['tolerancia_default', fn ($v) => (int) $v],
            'cfg_notif_email'     => ['notif_email',        fn ($v) => (bool) $v],
            'cfg_notif_email_dest' => ['notif_email_dest',  fn ($v) => trim($v)],
            'cfg_geo_lat'         => ['geo_lat',            fn ($v) => trim($v) !== '' && is_numeric($v) ? (float) $v : null],
            'cfg_geo_lng'         => ['geo_lng',            fn ($v) => trim($v) !== '' && is_numeric($v) ? (float) $v : null],
            'cfg_geo_radio_m'     => ['geo_radio_m',        fn ($v) => max(0, (int) $v)],
            'cfg_geo_requerido'   => ['geo_requerido',      fn ($v) => (int) ((bool) $v)],
        ];
        foreach ($map as $field => [$column, $process]) {
            if (array_key_exists($field, $post)) {
                $data[$column] = $process($post[$field]);
            }
        }

        if (empty($data)) {
            $s->setFlashdata('msg', 'No hay cambios para guardar');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/configuracion'));
        }

        $this->configModel->saveConfig($data, $adminId);
        $s->setFlashdata('msg', 'Configuración guardada');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/configuracion'));
    }

    /**
     * Polling de notificaciones (fallback cPanel cuando SSE no está disponible)
     * GET /admin/notificaciones/poll?since=<id>
     */
    public function pollNotificaciones()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = (int) $this->adminId();
        $since   = (int) $this->request->getGet('since');

        $notifs = (new \App\Models\NotificationModel())
            ->where('admin_id', $adminId)
            ->where('id >', $since)
            ->orderBy('id', 'ASC')
            ->limit(20)
            ->findAll();

        return $this->response->setJSON(['ok' => true, 'notificaciones' => $notifs]);
    }

    /**
     * Lista de notificaciones para el modal de la campana
     * GET /admin/notificaciones
     */
    public function listarNotificaciones()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = (int) $this->adminId();
        $model   = new \App\Models\NotificationModel();

        return $this->response->setJSON([
            'ok'            => true,
            'no_leidas'     => $model->getUnreadCount($adminId),
            'notificaciones' => $model->getForAdmin($adminId, 30),
        ]);
    }

    /**
     * Marca notificaciones como leídas: una por ?id= o todas
     * POST /admin/notificaciones/marcar-leidas
     */
    public function marcarLeidas()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = (int) $this->adminId();
        $model   = new \App\Models\NotificationModel();
        $id      = (int) $this->request->getPost('id');

        if ($id > 0) {
            $model->markAsRead($id, $adminId);
        } else {
            $model->markAllAsRead($adminId);
        }

        return $this->response->setJSON(['ok' => true, 'no_leidas' => $model->getUnreadCount($adminId)]);
    }

    /**
     * Contador ligero para el badge (no carga la lista)
     * GET /admin/notificaciones/badge
     */
    public function badgeNotificaciones()
    {
        if ($redirect = $this->guard()) return $redirect;
        $adminId = (int) $this->adminId();
        $model   = new \App\Models\NotificationModel();

        return $this->response->setJSON([
            'ok'         => true,
            'no_leidas'  => $model->getUnreadCount($adminId),
        ]);
    }
}
