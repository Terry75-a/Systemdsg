<?php

namespace App\Controllers;

use App\Models\PlanModel;

class PlanesController extends BaseController
{
    public function index()
    {
        $data['titulo'] = 'Gestión de Planes';
        session()->set('last_page', 'planes');

        echo view('layouts/header');
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('mantenedor/planes', $data);
        echo view('layouts/footer');
    }

    public function listar()
    {
        $model = new PlanModel();
        $data = $model->findAll();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function guardar()
    {
        $nombre = $this->request->getPost('nombre_plan');
        $id     = $this->request->getPost('id_plan');
        $nombre = is_string($nombre) ? trim($nombre) : '';
        $precio = $this->request->getPost('precio');
        $precio = ($precio !== null && $precio !== '') ? (float) $precio : 0.00;

        if ($nombre === '' || mb_strlen($nombre) > 100) {
            return $this->respuesta(false, 'Ingrese un nombre de entre 1 y 100 caracteres.', 422);
        }
        if ($precio < 0) {
            return $this->respuesta(false, 'El precio no puede ser negativo.', 422);
        }
        if ($id !== null && $id !== '' && (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1)) {
            return $this->respuesta(false, 'El plan no es válido.', 422);
        }

        try {
            $model  = new PlanModel();
            $editar = $id !== null && $id !== '';
            if ($editar && !$model->find($id)) {
                return $this->respuesta(false, 'El plan ya no existe. Actualice el listado.', 404);
            }

            $fechaInicio = $this->request->getPost('fecha_de_inicio');
            $fechaVenc   = $this->request->getPost('fecha_de_vencimiento');

            $data = [
                'nombre_plan'          => $nombre,
                'descripcion'          => trim((string) $this->request->getPost('descripcion')),
                'precio'               => $precio,
                'fecha_de_inicio'      => ($fechaInicio !== null && $fechaInicio !== '') ? $fechaInicio : null,
                'fecha_de_vencimiento' => ($fechaVenc !== null && $fechaVenc !== '') ? $fechaVenc : null,
                'estado_pago'          => trim((string) $this->request->getPost('estado_pago')) ?: null,
            ];

            $guardado = $editar ? $model->update($id, $data) : $model->insert($data);
            if ($guardado === false) {
                return $this->respuesta(false, 'No se pudo guardar el plan. Intente nuevamente.', 500);
            }

            return $this->respuesta(true, $editar
                ? 'Plan actualizado correctamente.'
                : 'Plan registrado correctamente.');
        } catch (\Throwable $e) {
            log_message('error', 'Error al guardar plan: {message}', ['message' => $e->getMessage()]);
            return $this->respuesta(false, 'No se pudo guardar el plan. Intente nuevamente.', 500);
        }
    }

    public function eliminar()
    {
        $id = $this->request->getPost('id_plan');
        if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID no válido']);
        }

        $model = new PlanModel();
        if (!$model->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Plan no encontrado']);
        }

        $model->delete($id);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Plan eliminado correctamente']);
    }

    private function respuesta(bool $success, string $message, int $status = 200)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode($status)->setJSON([
                'success'  => $success,
                'message'  => $message,
                'csrfHash' => csrf_hash(),
            ]);
        }
        if ($success) {
            return redirect()->to(base_url('planes'))->with('success', $message);
        }
        return redirect()->back()->withInput()->with('error', $message);
    }
}
