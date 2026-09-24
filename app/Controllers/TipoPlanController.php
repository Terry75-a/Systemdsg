<?php 


namespace App\Controllers;
use CodeIgniter\Controller;

USE App\Models\TipoPlanModel;

class TipoPlanController extends Controller
{
  public function index()
  {
    $data['titulo'] = 'Tipo de Planes';
    session()->set('last_page', 'tipo_plan');
    echo view('layouts/header');
    echo  view('layouts/sidebar');
    echo view('layouts/topbar', $data);
    echo view('mantenedor/tipo_plan', $data);
            echo view('layouts/footer');
        }


    public function listar()
    {

   $model = new TipoPlanModel();

    $data = $model->select('tipo_plan.id_tipo_plan, tipo_plan.nombre_tipo')
                  ->findAll();

    return $this->response->setJSON([
        'data' => $data
    ]);

    }

    public function eliminar($id_tipo_plan = null)
    {
        $id_tipo_plan = $id_tipo_plan ?? $this->request->getPost('id_tipo_plan');
        if (!is_scalar($id_tipo_plan) || !ctype_digit((string) $id_tipo_plan) || (int) $id_tipo_plan < 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID no válido']);
        }

        $model = new TipoPlanModel();
        $tipoPlan = $model->find($id_tipo_plan);

        if (!$tipoPlan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tipo de plan no encontrado']);
        }

        $model->delete($id_tipo_plan);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Tipo de plan eliminado correctamente']);




    }

}
