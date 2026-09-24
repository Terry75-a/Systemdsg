<?php 

namespace App\Controllers;

use App\Models\TipoPlanModel;


class TipoplanaddController extends BaseController
{
    protected $db;

    public function index()
    {


       $tipoPlanModel = new TipoPlanModel();

        $data = [
            'titulo'    => 'Agregar Tipo de Plan',
            'tipo_plan' => $tipoPlanModel->findAll() 
        ];
        
        
        session()->set('last_page', 'planesadd');

        echo view('layouts/header'); //cabezera
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('mantenedor/tipo_planadd', $data); // <-- contenido principal
        echo view('layouts/footer');
       
        
    }

    public function guardar()
    {
        $nombre = $this->request->getPost('nombre_tipo');
        $id = $this->request->getPost('id_tipo_plan');
        $nombre = is_string($nombre) ? trim($nombre) : '';

        if ($nombre === '' || mb_strlen($nombre) > 50) {
            return $this->respuestaGuardar(false, 'Ingrese un nombre de entre 1 y 50 caracteres.', 422);
        }
        if ($id !== null && $id !== '' && (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1)) {
            return $this->respuestaGuardar(false, 'El tipo de plan no es válido.', 422);
        }

        try {
            $model = new TipoPlanModel();
            $editar = $id !== null && $id !== '';
            if ($editar && !$model->find($id)) {
                return $this->respuestaGuardar(false, 'El tipo de plan ya no existe. Actualice el listado.', 404);
            }

            $data = ['nombre_tipo' => $nombre];
            $guardado = $editar ? $model->update($id, $data) : $model->insert($data);
            if ($guardado === false) {
                return $this->respuestaGuardar(false, 'No se pudo guardar el tipo de plan. Intente nuevamente.', 500);
            }
            return $this->respuestaGuardar(true, $editar
                ? 'Tipo de plan actualizado correctamente.'
                : 'Tipo de plan registrado correctamente.');
        } catch (\Throwable $e) {
            log_message('error', 'Error al guardar tipo de plan: {message}', ['message' => $e->getMessage()]);
            return $this->respuestaGuardar(false, 'No se pudo guardar el tipo de plan. Intente nuevamente.', 500);
        }
    }

    private function respuestaGuardar(bool $success, string $message, int $status = 200)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode($status)->setJSON([
                'success' => $success,
                'message' => $message,
                'csrfHash' => csrf_hash(),
            ]);
        }
        if ($success) {
            return redirect()->to(base_url('tipo_plan'))->with('success', $message);
        }
        return redirect()->back()->withInput()->with('error', $message);
    }
}
