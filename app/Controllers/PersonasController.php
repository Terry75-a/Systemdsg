<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\PersonaModel;
use App\Models\PlanModel;
use App\Models\TipoPlanModel;
use App\Models\EmpresaModel;
use Faker\Provider\Person;

class PersonasController extends BaseController
{
    public function index()
    {
        
        $model = new PersonaModel();
        $data['titulo'] = 'Gestión de Personas';
        $data['personas'] = $model->findAll();
        session()->set('last_page', 'personas');

        echo view('layouts/header'); //cabezera 
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('personas/personas', $data); // <-- contenido principal
        echo view('layouts/footer');
    }
   
    
    public function listar() 
{
    // 1. Capturamos el estado (1 o 0). Por defecto es 1 (Activos).
    $estado = $this->request->getGet('estado') ?? 1;

    $model = new PersonaModel();

    // 2. Especificamos los campos de la tabla personas de forma limpia para evitar ambigüedades
    // Usamos 'personas.id_persona' para decirle a MySQL exactamente de dónde sacar los datos.
    $data = $model->select('personas.id_persona, personas.nombre, personas.dni, personas.telefono, personas.direccion')
                  ->join('empresa', 'empresa.id_persona = personas.id_persona')
                  ->where('empresa.estado', (int)$estado) 
                  ->findAll();

    // Estructura requerida por DataTables
    return $this->response->setJSON([
        'data' => $data
    ]);
}

    public function eliminar($id = null)
{
    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID no válido']);
    }

    // 1. Capturamos el estado que manda el AJAX (0 o 1)
    // Si por algún motivo no viene, le ponemos 0 por defecto (dar de baja)
    $nuevoEstado = $this->request->getPost('estado') ?? 0;

    // 2. Cargamos el modelo de la EMPRESA (ya que tu ing te pidió que el estado esté en la empresa)
    $empresaModel = new \App\Models\EmpresaModel();

    // 3. Buscamos la empresa vinculada a esta persona para cambiarle el estado
    // NOTA: Asegúrate de que los nombres de las columnas coincidan con tu BD
    $empresa = $empresaModel->where('id_persona', $id)->first();

    if ($empresa) {
        // Actualizamos el estado de la empresa
        $empresaModel->update($empresa->id_empresa, ['estado' => $nuevoEstado]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $nuevoEstado == 0 ? 'Registro dado de baja correctamente.' : 'Registro activado correctamente.'
        ]);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontró una empresa asociada a esta persona.']);
}

public function obtenerDetalle()
{
    $id = $this->request->getGet('id');

    if (!$id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID no proporcionado.']);
    }

    $model = new PersonaModel();
    $empresaModel = new EmpresaModel();
    $db = \Config\Database::connect();

    // 1. Datos de la persona
    $persona = $model->where('id_persona', $id)->first();

    if (!$persona) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Persona no encontrada.']);
    }

    // 2. Datos de la empresa
    $empresa = $empresaModel->where('id_persona', $id)->first();

    $departamento = '-';
    $provincia    = '-';
    $distrito     = '-';
    $sucursales   = [];

    if ($empresa) {
        
        if (!empty($empresa->id_departamento)) {
            $dep = $db->table('ubigeo_peru_departments')
                      ->where('id', $empresa->id_departamento)
                      ->get()
                      ->getRow();
            $departamento = $dep->name ?? '-';
        }

        
        if (!empty($empresa->id_provincia)) {
            $prov = $db->table('ubigeo_peru_provinces')
                       ->where('id', $empresa->id_provincia)
                       ->get()
                       ->getRow();
            $provincia = $prov->name ?? '-';
        }

        
        if (!empty($empresa->id_distrito)) {
            $dist = $db->table('ubigeo_peru_districts')
                       ->where('id', $empresa->id_distrito)
                       ->get()
                       ->getRow();
            $distrito = $dist->name ?? '-';
        }

        
      $sucursales = $db->table('empresa_sucursales')
                         ->where('id_empresa', $empresa->id_empresa)
                         ->get()
                         ->getResultArray();
    }

    return $this->response->setJSON([
        'status' => 'success',
        'data' => [
            'nombre'           => $persona->nombre ?? '-',
            'apellido_paterno' => $persona->apellido_paterno ?? '-',
            'apellido_materno' => $persona->apellido_materno ?? '-',
            'dni'              => $persona->dni ?? '-',
            'telefono'         => $persona->telefono ?? '-',
            'direccion'        => $persona->direccion ?? '-',
            'ruc'              => $empresa->ruc ?? '-',
            'razon_social'     => $empresa->razon_social ?? '-',
            'departamento'     => $departamento,
            'provincia'        => $provincia,
            'distrito'         => $distrito,
            'sucursales'       => $sucursales,
        ]
    ]);
}


public function planesDisponibles(){
    $model = new PlanModel();
    $planes = $model->findAll();
    return $this->response->setJSON($planes);

}

public function tipoplan(){
    $model = new TipoPlanModel();
    $tipos = $model->findAll();
    return $this->response->setJSON($tipos);
}


public function Asignarplanes(){
    
    $empresaModel = new EmpresaModel();

    
    $id_persona = $this->request->getPost('id_persona');
    $empresa = $empresaModel->where('id_persona', $id_persona)->first();


    if (!$empresa) {
        return $this->response->setJSON([
            'status' => 'error', 
            'message' => 'No se encontró una empresa asociada a esta persona.'
        ]);
    }
    // 3. Juntamos los datos que se van a actualizar
    $data = [
        'id_plan'              => $this->request->getPost('id_plan'),
        'id_tipo_plan'         => $this->request->getPost('id_tipo_plan'),
        'fecha_de_inicio'      => $this->request->getPost('fecha_de_inicio'),
        'fecha_de_vencimiento' => $this->request->getPost('fecha_de_vencimiento'),
        'precio'               => $this->request->getPost('precio'),
        'estado_pago'          => $this->request->getPost('estado_pago'),
        'descripcion'          => $this->request->getPost('descripcion'),
    ];

    

    if($empresaModel->update($empresa->id_empresa, $data)){
        return $this->response->setJSON(['status' => 'success', 'message' => 'Plan actualizado correctamente.']);
    }else{
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar el plan.']);
    }

  

}
  public function mantenerPlanSeleccionado(){
    $idPersona = $this->request->getGet('idPersona');


    if(!$idPersona){
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID de persona no proporcionado.']);
    }
    $empresaModel = new EmpresaModel();

    $empresa = $empresaModel->where('id_persona', $idPersona)->first();

    if($empresa){
        $id_plan = is_object($empresa) ? $empresa->id_plan : $empresa['id_plan'];
        $id_tipo_plan = is_object($empresa) ? $empresa->id_tipo_plan : $empresa['id_tipo_plan'];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id_plan' => $id_plan,
                'id_tipo_plan' => $id_tipo_plan
            ]
        ]);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontró una empresa asociada a esta persona.']);
    }

    
  }
  

  public function registrarFechaInicio(){
    $idPersona = $this->request->getPost('id_persona');
    $fechaInicio = $this->request->getPost('fecha_inicio');

    if(!$idPersona || !$fechaInicio){
        return $this->response->setJSON(['status' => 'error', 'message' => 'ID de persona o fecha de inicio no proporcionados.']);
    }

    $empresaModel = new EmpresaModel();
    $empresa = $empresaModel->where('id_persona', $idPersona)->first();

    if($empresa){
        // Aquí podrías calcular la fecha de vencimiento basada en el tipo de plan, por ejemplo:
        // $fechaVencimiento = calcularFechaVencimiento($fechaInicio, $empresa->id_tipo_plan);
        // Luego actualizar la empresa con ambas fechas:
        // $empresaModel->update($empresa->id_empresa, ['fecha_inicio' => $fechaInicio, 'fecha_vencimiento' => $fechaVencimiento]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Fecha de inicio registrada correctamente.']);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontró una empresa asociada a esta persona.']);
    }
  }
}