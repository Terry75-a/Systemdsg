<?php
namespace App\Controllers\Admin;

use App\Libraries\Asistencia\AsistenciaQuery;
use App\Libraries\Asistencia\ExportCsv;

/**
 * Páginas y acciones del módulo de asistencia:
 * listado, reportes, edición con auditoría, historial y exportación CSV.
 */
class AsistenciaAdminController extends AdminBaseController
{
    public function asistencias()
    {
        if ($redirect = $this->guard()) return $redirect;

        $consulta   = new AsistenciaQuery($this->attendanceModel);
        $resultado  = $consulta->filtrar($this->request->getGet(), $this->adminId());
        $resumen    = AsistenciaQuery::resumir($resultado['filas']);
        $attendance = $resultado['filas'];

        $paginacion = $this->paginar($attendance, (int) ($this->request->getGet('page') ?? 1), $this->request->getGet());
        $attendance = $paginacion['filas'];
        unset($paginacion['filas']);

        return view('admin/asistencias', array_merge($this->viewData([
            'activePage'   => 'asistencias',
            'attendance'   => $attendance,
            'fecha_inicio' => $resultado['fecha_inicio'],
            'fecha_fin'    => $resultado['fecha_fin'],
            'estado'       => trim((string) $this->request->getGet('estado')),
            'resumen'      => $resumen,
        ]), $paginacion));
    }

    public function reportes()
    {
        if ($redirect = $this->guard()) return $redirect;

        $consulta   = new AsistenciaQuery($this->attendanceModel);
        $resultado  = $consulta->filtrar($this->request->getGet(), $this->adminId());
        $resumen    = AsistenciaQuery::resumir($resultado['filas']);
        $attendance = $resultado['filas'];

        $paginacion = $this->paginar($attendance, (int) ($this->request->getGet('page') ?? 1), $this->request->getGet());
        $attendance = $paginacion['filas'];
        unset($paginacion['filas']);

        return view('admin/reportes', array_merge($this->viewData([
            'activePage'   => 'reportes',
            'attendance'   => $attendance,
            'fecha_inicio' => $resultado['fecha_inicio'],
            'fecha_fin'    => $resultado['fecha_fin'],
            'estado'       => trim((string) $this->request->getGet('estado')),
            'resumen'      => $resumen,
        ]), $paginacion));
    }

    // ════════════════════════════════════════
    // AUDITORÍA — qué cambió en una marcación
    // ════════════════════════════════════════
    public function auditoria($attId = 0)
    {
        if ($redirect = $this->guard()) return $redirect;
        $s       = session();
        $adminId = $this->adminId();
        $attId   = (int) $attId;

        $att = $this->attendanceModel->find($attId);
        if (! $att || (int) ($att['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este registro no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/asistencias'));
        }

        return view('admin/auditoria', $this->viewData([
            'activePage' => 'asistencias',
            'pageTitle'  => 'Auditoría de asistencia · DSG',
            'att'        => $att,
            'logs'       => $this->logModel->deAsistencia($attId, 100),
        ]));
    }

    // ════════════════════════════════════════
    // EXPORTAR ASISTENCIAS (CSV)
    // ════════════════════════════════════════
    public function exportAsistencias()
    {
        if ($redirect = $this->guard()) return $redirect;

        [$fi, $ff] = AsistenciaQuery::rango($this->request->getPost());

        $consulta = new AsistenciaQuery($this->attendanceModel);
        $filas    = AsistenciaQuery::porEstado(
            $consulta->porRango($fi, $ff, $this->adminId()),
            (string) $this->request->getPost('estado')
        );

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . ExportCsv::nombreArchivo($fi, $ff) . '"')
            ->setBody(ExportCsv::asistencias($filas, $fi, $ff));
    }

    // ════════════════════════════════════════
    // ASISTENCIA (solo las de mi admin)
    // ════════════════════════════════════════
    public function updateAttendance()
    {
        if ($redirect = $this->guard()) return $redirect;
        $s = session();
        $adminId = $this->adminId();
        $attId   = (int) $this->request->getPost('att_id');

        $att = $this->attendanceModel->find($attId);
        if (!$att || (int) ($att['admin_id'] ?? -1) !== $adminId) {
            $s->setFlashdata('msg', 'Este registro no pertenece a tu admin');
            $s->setFlashdata('tipo', 'danger');
            return redirect()->to(base_url('admin/asistencias'));
        }

        $timeIn  = $this->request->getPost('time_in');
        $timeOut = $this->request->getPost('time_out');
        $status  = $this->request->getPost('status');
        $obs     = trim($this->request->getPost('observacion') ?? '');
        $motivo  = trim($this->request->getPost('motivo') ?? '');

        if (! in_array($status, ['present', 'late', 'absent', 'no_exit'], true)) {
            $s->setFlashdata('msg', 'Estado de asistencia no válido');
            $s->setFlashdata('tipo', 'warning');
            return redirect()->to(base_url('admin/asistencias'));
        }

        $data = [
            'status'      => $status,
            'observacion' => $obs,
        ];
        if ($timeIn)  $data['time_in']  = $timeIn;
        if ($timeOut) $data['time_out'] = $timeOut;

        $antes = [
            'status'      => (string) ($att['status'] ?? ''),
            'time_in'     => substr((string) ($att['time_in'] ?? ''), 0, 5),
            'time_out'    => substr((string) ($att['time_out'] ?? ''), 0, 5),
            'observacion' => trim((string) ($att['observacion'] ?? '')),
        ];
        $despues = [
            'status'      => $status,
            'time_in'     => $timeIn ? substr($timeIn, 0, 5) : $antes['time_in'],
            'time_out'    => $timeOut ? substr($timeOut, 0, 5) : $antes['time_out'],
            'observacion' => $obs,
        ];

        $this->attendanceModel->update($attId, $data);

        // Auditoría: un registro por campo que cambió
        foreach ($despues as $campo => $valor) {
            if ($antes[$campo] === (string) $valor) {
                continue;
            }
            $this->logModel->registrar([
                'attendance_id'  => $attId,
                'admin_id'       => $adminId,
                'user_id'        => (int) ($att['user_id'] ?? 0),
                'accion'         => 'edicion',
                'campo'          => $campo,
                'valor_anterior' => $antes[$campo] === '' ? null : mb_substr($antes[$campo], 0, 255),
                'valor_nuevo'    => $valor === '' ? null : mb_substr((string) $valor, 0, 255),
                'motivo'         => $motivo !== '' ? mb_substr($motivo, 0, 255) : null,
                'autor'          => session()->get('user_name') ?? 'admin',
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        if (in_array($status, ['late', 'absent', 'no_exit'])) {
            if (!$this->incidentModel->findByAttId($attId)) {
                $tipoMap = ['late' => 'Tardanza', 'absent' => 'Falta', 'no_exit' => 'Sin salida'];
                $this->incidentModel->insert([
                    'admin_id'      => $adminId,
                    'att_id'        => $attId,
                    'user_id'       => $att['user_id'] ?? 0,
                    'name'          => $att['name'] ?? '',
                    'tipo'          => $tipoMap[$status] ?? 'Otro',
                    'detalle'       => $obs,
                    'estado'        => 'Pendiente',
                    'justificacion' => '',
                ]);
            }
        }

        $s->setFlashdata('msg', 'Asistencia actualizada');
        $s->setFlashdata('tipo', 'success');
        return redirect()->to(base_url('admin/asistencias'));
    }
}
