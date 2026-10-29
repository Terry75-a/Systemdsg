<?php

namespace App\Controllers;

use App\Libraries\NotificadorVencimientos;

class NotificacionesController extends BaseController 
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $hoy = date('Y-m-d');

        // Consulta completa trayendo Persona, Empresa, Sucursal y Plan/Servicio
        // Incluye vencidos (hasta 60 días atrás) y próximos a vencer (según días de aviso o hasta 15 días)
        $sql = "
            SELECT 
                pg.id_pago,
                pg.id_tipo_pago,
                pg.concepto,
                pg.fecha_de_vencimiento,
                pg.precio,
                pg.observaciones,
                pg.ultimo_aviso,
                COALESCE(NULLIF(pg.num_repeticiones, 0), 5) AS dias_aviso,
                DATEDIFF(pg.fecha_de_vencimiento, ?) AS dias_restantes,
                pl.nombre_plan,
                tp.nombre_tipo AS tipo_plan_nombre,
                es.id_sucursal,
                es.nombre_sucursal,
                es.telefono AS telefono_sucursal,
                em.id_empresa,
                em.razon_social,
                pe.id_persona,
                CONCAT(pe.nombre, ' ', pe.apellido_paterno) AS nombre_cliente,
                COALESCE(
                    NULLIF(TRIM(pg.correo_notificacion), ''),
                    NULLIF(TRIM(es.correo), ''),
                    NULLIF(TRIM(em.correo), ''),
                    NULLIF(TRIM(pe.correo), '')
                ) AS correo_cliente
            FROM pagos pg
            INNER JOIN empresa_sucursales es ON es.id_sucursal = pg.id_sucursal
            INNER JOIN empresa em ON em.id_empresa = es.id_empresa
            INNER JOIN personas pe ON pe.id_persona = em.id_persona
            LEFT JOIN planes pl ON pl.id_plan = pg.id_plan
            LEFT JOIN tipo_plan tp ON tp.id_tipo_plan = pg.id_tipo_plan
            WHERE pg.estado = 1
              AND pg.fecha_de_vencimiento IS NOT NULL
              AND DATEDIFF(pg.fecha_de_vencimiento, ?) BETWEEN -60 AND GREATEST(COALESCE(NULLIF(pg.num_repeticiones, 0), 5), 7)
            ORDER BY pg.fecha_de_vencimiento ASC
        ";

        $registros = $this->db->query($sql, [$hoy, $hoy])->getResult();

        $vencidos    = 0;
        $vencenHoy   = 0;
        $porVencer   = 0;
        $notificados = 0;
        $montoTotal  = 0.0;

        foreach ($registros as $reg) {
            $dias = (int) $reg->dias_restantes;
            $montoTotal += (float) $reg->precio;

            if ($dias < 0) {
                $vencidos++;
            } elseif ($dias === 0) {
                $vencenHoy++;
            } else {
                $porVencer++;
            }

            if (!empty($reg->ultimo_aviso) && $reg->ultimo_aviso === $hoy) {
                $notificados++;
            }
        }

        $data = [
            'titulo'      => 'Notificaciones',
            'hoy'         => $hoy,
            'registros'   => $registros,
            'kpi'         => [
                'vencidos'    => $vencidos,
                'vencen_hoy'  => $vencenHoy,
                'por_vencer'  => $porVencer,
                'notificados' => $notificados,
                'monto_total' => $montoTotal,
            ]
        ];

        session()->set('last_page', 'notificaciones');

        echo view('layouts/header');
        echo view('layouts/sidebar');
        echo view('layouts/topbar', $data);
        echo view('notificaciones/notificaciones', $data);
        echo view('layouts/footer');
    }

    /**
     * Devuelve el JSON para la campanita del topbar (total + lista de empresas por vencer)
     */
    public function alertas()
    {
        $hoy = date('Y-m-d');

        $sql = "
            SELECT 
                pg.id_pago,
                pg.id_tipo_pago,
                pg.concepto,
                pg.fecha_de_vencimiento,
                pg.precio,
                pg.ultimo_aviso,
                COALESCE(NULLIF(pg.num_repeticiones, 0), 5) AS dias_aviso,
                DATEDIFF(pg.fecha_de_vencimiento, ?) AS dias_restantes,
                pl.nombre_plan,
                tp.nombre_tipo AS tipo_plan_nombre,
                es.id_sucursal,
                es.nombre_sucursal,
                em.razon_social,
                em.id_persona,
                CONCAT(pe.nombre, ' ', pe.apellido_paterno) AS nombre_cliente
            FROM pagos pg
            INNER JOIN empresa_sucursales es ON es.id_sucursal = pg.id_sucursal
            INNER JOIN empresa em ON em.id_empresa = es.id_empresa
            INNER JOIN personas pe ON pe.id_persona = em.id_persona
            LEFT JOIN planes pl ON pl.id_plan = pg.id_plan
            LEFT JOIN tipo_plan tp ON tp.id_tipo_plan = pg.id_tipo_plan
            WHERE pg.estado = 1
              AND pg.fecha_de_vencimiento IS NOT NULL
              AND DATEDIFF(pg.fecha_de_vencimiento, ?) BETWEEN 0 AND COALESCE(NULLIF(pg.num_repeticiones, 0), 5)
            ORDER BY pg.fecha_de_vencimiento ASC
        ";

        $registros = $this->db->query($sql, [$hoy, $hoy])->getResult();

        $items = [];
        $faltaEnviarCorreoHoy = false;

        foreach ($registros as $reg) {
            if (empty($reg->ultimo_aviso) || $reg->ultimo_aviso < $hoy) {
                $faltaEnviarCorreoHoy = true;
            }

            $esPlan = ((int) $reg->id_tipo_pago === 1);
            $servicio = $esPlan
                ? ($reg->nombre_plan ?? 'Plan') . (!empty($reg->tipo_plan_nombre) ? ' (' . $reg->tipo_plan_nombre . ')' : '')
                : ($reg->concepto ?: 'Otros Pagos');

            $empresaNombre = !empty($reg->razon_social) 
                ? $reg->razon_social . ' (' . $reg->nombre_sucursal . ')' 
                : $reg->nombre_sucursal;

            $dias = (int) $reg->dias_restantes;
            if ($dias === 0) {
                $tiempoTxt = '¡Vence hoy! (' . date('d/m/Y', strtotime($reg->fecha_de_vencimiento)) . ')';
                $mensajeTxt = "El {$servicio} de {$empresaNombre} vence HOY (S/ " . number_format((float)$reg->precio, 2) . ").";
            } elseif ($dias === 1) {
                $tiempoTxt = 'Vence mañana (' . date('d/m/Y', strtotime($reg->fecha_de_vencimiento)) . ')';
                $mensajeTxt = "El {$servicio} de {$empresaNombre} vence en 1 día (S/ " . number_format((float)$reg->precio, 2) . ").";
            } else {
                $tiempoTxt = "Vence en {$dias} días (" . date('d/m/Y', strtotime($reg->fecha_de_vencimiento)) . ")";
                $mensajeTxt = "El {$servicio} de {$empresaNombre} está por vencer (S/ " . number_format((float)$reg->precio, 2) . ").";
            }

            $items[] = [
                'id_pago' => $reg->id_pago,
                'cliente' => $reg->nombre_cliente . ' · ' . ($reg->razon_social ?: $reg->nombre_sucursal),
                'titulo'  => $reg->nombre_sucursal,
                'mensaje' => $mensajeTxt,
                'tiempo'  => $tiempoTxt,
                'url'     => base_url('personasadd/' . $reg->id_persona),
            ];
        }

        return $this->response->setJSON([
            'total'                => count($items),
            'items'                => $items,
            'disparar_correos_hoy' => $faltaEnviarCorreoHoy
        ]);
    }

    /**
     * Envía los correos pendientes en segundo plano sin congelar la interfaz
     */
    public function enviarCorreosPendientes()
    {
        ignore_user_abort(true);
        set_time_limit(120);

        $notificador = new NotificadorVencimientos();
        $resultado   = $notificador->procesarRecordatorios();

        return $this->response->setJSON([
            'status'    => 'ok',
            'resultado' => $resultado
        ]);
    }
}