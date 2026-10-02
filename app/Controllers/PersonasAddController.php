<?php

namespace App\Controllers;

use App\Models\PersonasAddModel;
use App\Models\Empresamodel;
use App\Models\empresaSucursalModel;
use App\Models\PagosModel;


class PersonasAddController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function verificarSesion(): mixed
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }
        return null;
    }

    // ══════════════════════════════════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════════════════════════════════

    
    // ══════════════════════════════════════════════════════════════════
// INDEX
// ══════════════════════════════════════════════════════════════════
public function index($id = null)
{
    $redir = $this->verificarSesion();
    if ($redir) return $redir;

    $empresaModel = new \App\Models\EmpresaModel(); 
    $data['empresa'] = null;

    $personaModel = new \App\Models\PersonasAddModel(); // Asegúrate de apuntar al modelo correcto
    $data['persona'] = null;
    $data['sucursales'] = []; // Inicializamos siempre como un array vacío

    // Si viene un ID, buscamos los datos para editar
    if ($id !== null) {
        $data['persona'] = $personaModel->find($id);
        if (!$data['persona']) {
            return redirect()->to(base_url('personas'))->with('error', 'Persona no encontrada.');
        }
        
        $empresa = $empresaModel->where('id_persona', $id)->first();
        $data['empresa'] = $empresa;

        // Si la persona tiene empresa, cargamos sus sucursales
        if ($empresa) {
            $empresaSucursalModel = new \App\Models\empresaSucursalModel();
            $data['sucursales'] = $empresaSucursalModel->where('id_empresa', $empresa->id_empresa)->findAll();
        }
    }
    
    try {
        $data['departamentos'] = $this->db
            ->table('ubigeo_peru_departments')
            ->orderBy('name', 'ASC')
            ->get()->getResultObject();
    }
    catch (\Throwable $e) {
        $data['departamentos'] = [];
    }

    $data['titulo'] = ($id === null) ? 'Agregar Persona' : 'Editar Persona';
    $data['username'] = session()->get('username');

    // Cuando se solicita por AJAX o con ?modal=1 (modal sobre la tabla de
    // clientes), se devuelve un documento mínimo (sin layouts) para que el
    // formulario viva dentro de un iframe con su propio contexto de scripts.
    if ($this->request->isAJAX() || $this->request->getGet('modal') === '1') {
        return view('personas/personasadd_frame', $data);
    }

    echo view('layouts/header', $data);
    echo view('layouts/sidebar');
    echo view('layouts/topbar', $data);
    echo view('personas/personasadd', $data);
    echo view('layouts/footer');
}

    // ══════════════════════════════════════════════════════════════════
    // BUSCAR DNI
    // CAMBIOS: http → https, allow_redirects: true, sin bloque debug
    // ══════════════════════════════════════════════════════════════════
    public function buscarDni()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $dni = $this->request->getPost('dni');

        if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El DNI debe tener 8 dígitos numéricos'
            ]);
        }
        $personaModel = new \App\Models\PersonasAddModel();
    if ($personaModel->existeDni($dni)) {
        return $this->response->setJSON([
            'success' => false,
            'registrado' => true, // Bandera extra para identificar que ya existe
            'message' => 'El DNI ya se encuentra registrada en el sistema.'
        ]);
    }


        try {
            $url = 'https://www.dsgperutech.com/api-customer/v1/dni/' . $dni; // ← https
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => '3d524a53c110e4c22463b10ed32cef9d',
                ],
                'http_errors' => false,
                'timeout' => 10,
                'allow_redirects' => true, // ← sigue redirecciones 301/302
            ]);

            $status = $apiResponse->getStatusCode();
            $body = $apiResponse->getBody();
            $dataApi = json_decode($body);

            if ($status !== 200 || !$dataApi || empty($dataApi->success)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'DNI no encontrado en el servicio externo'
                ]);
            }
            

            return $this->response->setJSON([
                'success' => true,
                'nombres' => $dataApi->result->name ?? '',
                'apellido_paterno' => $dataApi->result->paternal ?? '',
                'apellido_materno' => $dataApi->result->maternal ?? '',
            ]);

        }
        catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al conectar con el servicio: ' . $e->getMessage()
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // BUSCAR RUC
    // CAMBIOS: http → https, allow_redirects: true
    // ══════════════════════════════════════════════════════════════════
    public function buscarRuc()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $ruc = $this->request->getPost('ruc');

        if (!$ruc || !preg_match('/^\d{11}$/', $ruc)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El RUC debe tener 11 dígitos numéricos'
            ]);
        }
         $empresaModel = new \App\Models\EmpresaModel();

         if ($empresaModel->ExisteRuc($ruc)) {
        return $this->response->setJSON([
            'success' => false,
            'registrado' => true,
            'message' => 'Esta empresa ya se encuentra registrada.'
        ]);
    }

        try {
            $url = 'https://www.dsgperutech.com/api-customer/v1/ruc/' . $ruc; // ← https
            $client = \Config\Services::curlrequest();

            $apiResponse = $client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => '3d524a53c110e4c22463b10ed32cef9d',
                ],
                'http_errors' => false,
                'timeout' => 10,
                'allow_redirects' => true, // ← sigue redirecciones 301/302
            ]);

            $status = $apiResponse->getStatusCode();
            $body = $apiResponse->getBody();
            $dataApi = json_decode($body);

            if ($status !== 200 || !$dataApi || empty($dataApi->success)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'RUC no encontrado en el servicio externo'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'razon' => $dataApi->result->name ?? '',
                'direccion' => $dataApi->result->address ?? '',
            ]);

        }
        catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al conectar con el servicio: ' . $e->getMessage()
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // REGISTRAR PERSONA
    // ══════════════════════════════════════════════════════════════════
    // ══════════════════════════════════════════════════════════════════
    // REGISTRAR / EDITAR PERSONA (GUARDAR TODO)
    // ══════════════════════════════════════════════════════════════════

    
 public function guardar_todo()
    {
        $redir = $this->verificarSesion();
        if ($redir) return $redir;

        // Si la petición viene por AJAX (modal sobre la tabla), respondemos JSON
        // en lugar de redireccionar.
        $esAjax = $this->request->isAJAX();
        $finalizar = function (string $mensaje, bool $ok = true) use ($esAjax): \CodeIgniter\HTTP\ResponseInterface {
            if ($esAjax) {
                return $this->response->setJSON([
                    'success' => $ok,
                    'message' => $mensaje,
                    'redirect' => base_url('personas')
                ]);
            }
            if ($ok) {
                return redirect()->to(base_url('personas'))->with('success', $mensaje);
            }
            return redirect()->back()->withInput()->with('error', $mensaje);
        };

        $dni = $this->request->getPost('dni');
        $ruc = $this->request->getPost('ruc');
        $id_persona = $this->request->getPost('id_persona');

        $personaModel = new \App\Models\PersonasAddModel();
        $empresaModel = new \App\Models\EmpresaModel();
        $empresaSucursalModel = new \App\Models\empresaSucursalModel();

        // Iniciamos transacción
        $this->db->transStart();

        $idPersonaGenerada = null;

        if (!empty($dni)) {
            // Validar si el DNI ya pertenece a otra persona
            $existeQuery = $personaModel->where('dni', $dni);
            if (!empty($id_persona)) {
                $existeQuery->where('id_persona !=', $id_persona);
            }
            $existe = $existeQuery->first();

            if ($existe) {
                return $finalizar('El DNI ya pertenece a otra persona.', false);
            }

            $dataPersona = [
                'nombre'           => $this->request->getPost('nombre'),
                'apellido_paterno' => $this->request->getPost('apellido_paterno'),
                'apellido_materno' => $this->request->getPost('apellido_materno'),
                'dni'              => $dni,
                'telefono'         => $this->request->getPost('telefono'),
                'correo'           => $this->request->getPost('correo') ?: null,
                'direccion'        => $this->request->getPost('direccion_rep') ?: null,
                'id_distrito'      => $this->request->getPost('distrito_rep') ?: null,
                'id_departamento'  => $this->request->getPost('departamento_rep') ?: null,
                'id_provincia'     => $this->request->getPost('provincia_rep') ?: null,
                'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento') ?: null,
                'id_pais'          => 1,
                'id_tipo_documento'=> 1,
            ];

            if (!empty($id_persona)) {
                $personaModel->update($id_persona, $dataPersona);
                $idPersonaGenerada = $id_persona;
            } else {
                $dataPersona['created_at'] = date('Y-m-d H:i:s');
                $personaModel->insert($dataPersona);
                $idPersonaGenerada = $personaModel->getInsertID();
            }
        }

        // Si hay RUC procesamos la empresa y sus sucursales
        if (!empty($ruc) && $idPersonaGenerada) {
            if (strlen($ruc) != 11) {
                return $finalizar('El RUC debe tener 11 dígitos.', false);
            }

            $existeEmpresaQuery = $empresaModel->where('ruc', $ruc);
            if (!empty($id_persona)) {
                $existeEmpresaQuery->where('id_persona !=', $id_persona);
            }
            if ($existeEmpresaQuery->first()) {
                return $finalizar('El RUC ya está registrado.', false);
            }

            $dataEmpresa = [
                'ruc'             => $ruc,
                'razon_social'    => $this->request->getPost('razon_social'),
                'direccion'       => $this->request->getPost('direccion'),
                'correo'          => $this->request->getPost('correo_empresa') ?: null,
                'id_departamento' => $this->request->getPost('departamento_repdos') ?: null,
                'id_provincia'    => $this->request->getPost('provincia_repdos') ?: null,
                'id_distrito'     => $this->request->getPost('distrito_repdos') ?: null,
                
                'id_persona'      => $idPersonaGenerada,
                'estado'          => 1,
            ];

            $empresaExistente = $empresaModel->where('id_persona', $idPersonaGenerada)->first();
            $idEmpresaGenerada = null;

            if ($empresaExistente) {
                $empresaModel->update($empresaExistente->id_empresa, $dataEmpresa);
                $idEmpresaGenerada = $empresaExistente->id_empresa;
            } else {
                $dataEmpresa['created_at'] = date('Y-m-d H:i:s');
                $empresaModel->insert($dataEmpresa);
                $idEmpresaGenerada = $empresaModel->getInsertID();
            }

            // ══════════════════════════════════════════════════════════════════
            // GESTIÓN DE SUCURSALES (BORRADO Y SINCRONIZACIÓN)
            // ══════════════════════════════════════════════════════════════════
            // ══════════════════════════════════════════════════════════════════
            // GESTIÓN DE SUCURSALES (ACTUALIZAR EXISTENTES E INSERTAR NUEVAS)
            // ══════════════════════════════════════════════════════════════════
            if ($idEmpresaGenerada) {
                // 1. Eliminar únicamente las sucursales que el usuario borró explícitamente en la vista
                $sucursalesEliminadas = $this->request->getPost('sucursales_eliminadas');
                if (!empty($sucursalesEliminadas) && is_array($sucursalesEliminadas)) {
                    $empresaSucursalModel->where('id_empresa', $idEmpresaGenerada)
                                         ->whereIn('id_sucursal', $sucursalesEliminadas)
                                         ->delete();
                }

                // 2. Procesar la lista de sucursales recibidas
                $sucursales = $this->request->getPost('sucursales');

                if (!empty($sucursales) && is_array($sucursales)) {
                    foreach ($sucursales as $indice => $datosSucursal) {
                        $nombreSucursal = trim($datosSucursal['nombre_sucursal'] ?? '');

                        if (empty($nombreSucursal)) {
                            continue;
                        }

                        $dataSucursal = [
                            'id_empresa'       => (int)$idEmpresaGenerada,
                            'nombre_sucursal'  => $nombreSucursal,
                            'representante'    => $datosSucursal['representante_sucursal'] ?? '',
                            'direccion'        => $datosSucursal['direccion'] ?: null,
                            'telefono'         => $datosSucursal['telefono'] ?? '',
                            'correo'           => $datosSucursal['correo'] ?? '',
                            'id_pais'          => 1,
                            'id_departamento'  => $datosSucursal['departamento_sucursal'] ?: null,
                            'id_provincia'     => $datosSucursal['provincia_sucursal'] ?: null,
                            'id_distrito'      => $datosSucursal['distrito_sucursal'] ?: null,
                            'codigo_cliente'   => $datosSucursal['codigo_cliente_sucursal'] ?: null,
                            'estado'           => isset($datosSucursal['estado']) ? (int)$datosSucursal['estado'] : 1,
                        ];

                        // Si el índice viene como 'existing_123', es una sucursal que ya existe: la ACTUALIZAMOS
                        if (strpos((string)$indice, 'existing_') === 0) {
                            $idSucursalExistente = (int)str_replace('existing_', '', (string)$indice);
                            $dataSucursal['updated_at'] = date('Y-m-d H:i:s');
                            $empresaSucursalModel->update($idSucursalExistente, $dataSucursal);
                        } else {
                            // Es una sucursal nueva: la INSERTAMOS
                            $dataSucursal['created_at'] = date('Y-m-d H:i:s');
                            $empresaSucursalModel->insert($dataSucursal);
                        }
                    }
                }
            }
        }

        // Completamos la transacción
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            log_message('error', 'Error DB guardar_todo: ' . json_encode($this->db->error()));
            return $finalizar('Ocurrió un error al guardar los datos en el sistema.', false);
        }

        $mensaje = !empty($id_persona) ? 'Registro actualizado con éxito.' : 'Registro completado con éxito.';
        return $finalizar($mensaje, true);
    }
     
    public function guardarPlan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Petición no permitida.'
            ]);
        }

        $idSucursal = $this->request->getPost('id_sucursal');
        $idEmpresa  = $this->request->getPost('id_empresa');

        // Validar si la sucursal es temporal o no existe en BD
        if (empty($idSucursal) || strpos($idSucursal, 'temp_') === 0) {
            if (!empty($idEmpresa)) {
                $sucursalModel = new \App\Models\empresaSucursalModel();
                $sucursalPrincipal = $sucursalModel->where('id_empresa', $idEmpresa)
                    ->where('tipo', 'principal')
                    ->first();

                if ($sucursalPrincipal) {
                    $idSucursal = is_array($sucursalPrincipal) ? $sucursalPrincipal['id_sucursal'] : $sucursalPrincipal->id_sucursal;
                }
            }

            if (empty($idSucursal) || !is_numeric($idSucursal)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Primero debe guardar los cambios de la persona/sucursal antes de asignarle un plan.'
                ]);
            }
        }

        // Parámetros recibidos
        $idPlan           = $this->request->getPost('id_plan');
        $idTipoPlan       = $this->request->getPost('id_tipo_plan');
        $fechaInicio      = $this->request->getPost('fecha_de_inicio');
        $fechaVencimiento = $this->request->getPost('fecha_de_vencimiento');
        $precio           = $this->request->getPost('precio');
        $observaciones    = $this->request->getPost('observaciones');

        // Campos de finalización mensual
        $tipoFin         = $this->request->getPost('tipo_fin') ?: 'SIN_FIN';
        $numRepeticiones = $this->request->getPost('num_repeticiones');
        $fechaFin        = $this->request->getPost('fecha_fin');

        $repeticionesFinal = null;
        $fechaFinFinal     = null;

        if ($tipoFin === 'REPETICIONES') {
            $repeticionesFinal = !empty($numRepeticiones) ? (int)$numRepeticiones : 12;
        } elseif ($tipoFin === 'FECHA') {
            $fechaFinFinal = !empty($fechaFin) ? $fechaFin : null;
        }

        $dataPago = [
            'id_sucursal'          => (int)$idSucursal,
            'id_plan'              => !empty($idPlan) ? (int)$idPlan : null,
            'id_tipo_plan'         => !empty($idTipoPlan) ? (int)$idTipoPlan : null,
            'fecha_de_inicio'      => $fechaInicio,
            'fecha_de_vencimiento' => !empty($fechaVencimiento) ? $fechaVencimiento : null,
            'tipo_fin'             => in_array($tipoFin, ['SIN_FIN', 'REPETICIONES', 'FECHA']) ? $tipoFin : 'SIN_FIN',
            'num_repeticiones'     => $repeticionesFinal,
            'fecha_fin'            => $fechaFinFinal,
            'precio'               => !empty($precio) ? (float)$precio : 0.00,
            'observaciones'        => !empty($observaciones) ? trim($observaciones) : null,
            'estado'               => 1,
            'created_at'           => date('Y-m-d H:i:s'),
        ];

        try {
            // Instanciar tu modelo
            $pagosModel = new \App\Models\PagosModel();
            $idInsertado = $pagosModel->insert($dataPago);

            if ($idInsertado) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => '¡Plan y pago registrados correctamente!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'No se pudo registrar el pago.',
                    'errors'  => $pagosModel->errors()
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error al guardar pago: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ]);
        }
    }
}