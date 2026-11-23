<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'id_persona';

    // Total de clientes/personas registradas
    public function totalClientes()
    {
        return $this->countAllResults();
    }

    // Personas registradas en el mes actual
    public function nuevosClientesEsteMes()
    {
        $startDate = date('Y-m-01 00:00:00');
        return $this->where('created_at >=',$startDate)->countAllResults();
    }

    // Total de empresas registradas
    public function totalEmpresas(): int
    {
        try {
            $db = \Config\Database::connect();
            $tablasBD =$db->listTables();

            foreach (['empresas', 'empresa'] as $tabla) {
                if (in_array($tabla,$tablasBD, true)) {
                    $campos =$db->getFieldNames($tabla);$builder = $db->table($tabla);

                    if (in_array('estado', $campos, true)) {$builder->groupStart()
                            ->where('estado', 1)
                            ->orWhere('estado', 'Activo')
                            ->orWhere('estado IS NULL', null, false)
                            ->groupEnd();
                    }

                    return (int) $builder->countAllResults();
                }
            }

            // Respaldo si los datos de empresa están en la tabla personas
            $camposPersonas =$db->getFieldNames('personas');
            if (in_array('ruc', $camposPersonas, true)) {
                return (int) $db->table('personas')
                    ->where('ruc IS NOT NULL', null, false)
                    ->where("TRIM(ruc) != ''", null, false)
                    ->countAllResults();
            }

            if (in_array('razon_social', $camposPersonas, true)) {
                return (int) $db->table('personas')
                    ->where('razon_social IS NOT NULL', null, false)
                    ->where("TRIM(razon_social) != ''", null, false)
                    ->countAllResults();
            }
        } catch (\Throwable $e) {
            log_message('error', '[DashboardModel::totalEmpresas] ' . $e->getMessage());
        }

        return 0;
    }

    // Total de sucursales registradas
    // Total de sucursales registradas
    public function totalSucursales(): int
    {
        try {
            $db = \Config\Database::connect();
            $tablasBD = $db->listTables();

            foreach (['empresa_sucursales', 'empresa_sucursal', 'sucursales', 'sucursal'] as $tabla) {
                if (in_array($tabla, $tablasBD, true)) {
                    $campos = $db->getFieldNames($tabla);
                    $builder = $db->table($tabla);

                    if (in_array('estado', $campos, true)) {
                        $builder->groupStart()
                            ->where('estado', 1)
                            ->orWhere('estado', 'Activo')
                            ->orWhere('estado IS NULL', null, false)
                            ->groupEnd();
                    }

                    return (int) $builder->countAllResults();
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[DashboardModel::totalSucursales] ' . $e->getMessage());
        }

        return 0;
    }

    // Planes asignados actualmente vigentes/activos
    public function totalPlanesActivos(): int
    {
        try {
            $db = \Config\Database::connect();$fuente = $this->resolverTablaPlanesAsignados($db);

            if ($fuente === null) {
                return (int) $this->totalPlanes();
            }

            $tabla = $fuente['tabla'];$colFecha = $fuente['fecha'];$colEstado = $fuente['estado'];$hoy = date('Y-m-d');

            $builder = $db->table($tabla);

            if ($colEstado !== null) {$builder->groupStart()
                    ->where($colEstado, 1) ->orWhere($colEstado, 'Activo')
                    ->groupEnd();
            }

            $builder->groupStart()
                ->where($colFecha . ' IS NULL', null, false)
                ->orWhere($colFecha, '')
                ->orWhere($colFecha, '0000-00-00')
                ->orWhere('DATE(' . $colFecha . ') >=',$hoy)
                ->groupEnd();

            return (int) $builder->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', '[DashboardModel::totalPlanesActivos] ' . $e->getMessage());
        }

        return 0;
    }

    // Catálogo de planes disponibles
    public function totalPlanes()
    {
        $db = \Config\Database::connect();
        return $db->table('planes')->countAllResults();
    }

    // Tipos de plan
    public function totalTipoPlan()
    {
        $db = \Config\Database::connect();
        return $db->table('tipo_plan')->countAllResults();
    }

    // Usuarios del sistema
    public function totalUsuarios()
    {
        $db = \Config\Database::connect();
        return $db->table('usuarios')->countAllResults();
    }

    // Seguimiento de planes: próximos a vencer (hoy hasta +$diasAnticipacion días) y vencidos (< hoy)
    public function seguimientoPlanes(int $diasAnticipacion = 7): array
    {
        $resultado = [
            'proximos' => 0,
            'vencidos' => 0,
        ];

        try {
            $db = \Config\Database::connect();$fuente = $this->resolverTablaPlanesAsignados($db);

            if ($fuente === null) {
                return $resultado;
            }

            $tabla =$fuente['tabla'];
            $colFecha =$fuente['fecha'];
            $colEstado =$fuente['estado'];

            $hoy = date('Y-m-d');
            $limite = date('Y-m-d', strtotime('+' .$diasAnticipacion . ' days'));

            // Próximos a vencer (incluye los que vencen hoy)
            $qProximos = $db->table($tabla)
                ->where($colFecha . ' IS NOT NULL', null, false)
                ->where($colFecha . ' !=', '')
                ->where($colFecha . ' !=', '0000-00-00')
                ->where('DATE(' . $colFecha . ') >=',$hoy)
                ->where('DATE(' . $colFecha . ') <=',$limite);

            if ($colEstado !== null) {$qProximos->groupStart()
                    ->where($colEstado, 1) ->orWhere($colEstado, 'Activo')
                    ->groupEnd();
            }

            $resultado['proximos'] = (int)$qProximos->countAllResults();

            // Planes vencidos
            $qVencidos = $db->table($tabla)
                ->where($colFecha . ' IS NOT NULL', null, false)
                ->where($colFecha . ' !=', '')
                ->where($colFecha . ' !=', '0000-00-00')
                ->where('DATE(' . $colFecha . ') <',$hoy);

            if ($colEstado !== null) {$qVencidos->groupStart()
                    ->where($colEstado, 1) ->orWhere($colEstado, 'Activo')
                    ->groupEnd();
            }

            $resultado['vencidos'] = (int)$qVencidos->countAllResults();
        } catch (\Throwable $e) {
            log_message('error', '[DashboardModel::seguimientoPlanes] ' . $e->getMessage());
        }

        return $resultado;
    }

    private function resolverTablaPlanesAsignados($db): ?array
    {
        $fechasCandidatas = [ 'fecha_de_vencimiento', 'fecha_vencimiento', 'fecha_fin', 'vencimiento', ];$estadosCandidatos = ['estado', 'activo'];

        $tablasPrioritarias = [
            'sucursal_planes',
            'sucursales_planes',
            'planes_sucursales',
            'planes_sucursal',
            'persona_planes',
            'personas_planes',
            'planes_personas',
            'asignar_planes',
            'clientes_planes',
            'cliente_planes',
            'plan_sucursal',
            'plan_persona',
            'detalle_plan',
            'suscripciones',
        ];

        $tablasBD = $db->listTables();$ordenadas = array_values(array_unique(array_merge(
            array_intersect($tablasPrioritarias, $tablasBD),$tablasBD
        )));

        foreach ($ordenadas as $tabla) {$campos = $db->getFieldNames($tabla);
            if (!in_array('id_plan', $campos, true)) {
                continue;
            }

            $colFecha = null;
            foreach ($fechasCandidatas as$candidata) {
                if (in_array($candidata,$campos, true)) {
                    $colFecha =$candidata;
                    break;
                }
            }

            if ($colFecha === null) {
                continue;
            }

            $colEstado = null;
            foreach ($estadosCandidatos as$candidata) {
                if (in_array($candidata,$campos, true)) {
                    $colEstado =$candidata;
                    break;
                }
            }

            return [
                'tabla'  => $tabla,
                'fecha'  => $colFecha,
                'estado' => $colEstado,
            ];
        }

        return null;
    }
}