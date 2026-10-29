<?php

namespace App\Libraries;

use Config\ExternalServices;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificadorVencimientos
{
    protected $db;
    protected ExternalServices $mailConfig;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->mailConfig = config('ExternalServices');
    }

    public function procesarRecordatorios(): array
    {
        require_once ROOTPATH . 'vendor/autoload.php';

        $hoy = date('Y-m-d');
        $resultados = ['enviados' => 0, 'errores' => 0, 'detalles' => []];

        if (empty($this->mailConfig->smtpHost) || empty($this->mailConfig->smtpUsername) || empty($this->mailConfig->smtpPassword)) {
            return ['enviados' => 0, 'errores' => 1, 'detalles' => ['SMTP no configurado en .env']];
        }

        // Consulta SQL que une pagos con sucursal (incluyendo es.telefono), empresa, persona y plan
        $sql = "
            SELECT 
                pg.id_pago,
                pg.id_tipo_pago,
                pg.concepto,
                pg.fecha_de_vencimiento,
                COALESCE(pg.num_repeticiones, 5) AS dias_aviso,
                pg.precio,
                pg.observaciones,
                DATEDIFF(pg.fecha_de_vencimiento, ?) AS dias_restantes,
                pl.nombre_plan,
                tp.nombre_tipo AS tipo_plan_nombre,
                es.nombre_sucursal,
                es.telefono AS telefono_cliente,
                em.razon_social,
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
              AND (pg.ultimo_aviso IS NULL OR pg.ultimo_aviso < ?)
              AND DATEDIFF(pg.fecha_de_vencimiento, ?) BETWEEN 0 AND COALESCE(pg.num_repeticiones, 5)
        ";

        $registros = $this->db->query($sql, [$hoy, $hoy, $hoy])->getResult();

        foreach ($registros as $reg) {
            $esPlan = ($reg->id_tipo_pago == 1);
            $nombreServicio = $esPlan 
                ? ($reg->nombre_plan ?? 'Plan de Servicio') . (!empty($reg->tipo_plan_nombre) ? " ({$reg->tipo_plan_nombre})" : '')
                : ($reg->concepto ?? 'Otros Pagos / Servicio adicional');

            // 1. Envía el correo (Cliente + Admin)
            $enviadoCorreoOk = $this->enviarCorreosAviso($reg, $nombreServicio);

            // 2. Envía el WhatsApp (Sucursal + Admin)
            $enviadoWhatsAppOk = $this->enviarWhatsAppAviso($reg, $nombreServicio);

            if ($enviadoCorreoOk || $enviadoWhatsAppOk) {
                // Marcamos que hoy ya se le envió su recordatorio
                $this->db->table('pagos')
                    ->where('id_pago', $reg->id_pago)
                    ->update(['ultimo_aviso' => $hoy]);

                $resultados['enviados']++;
                $resultados['detalles'][] = "Recordatorio enviado (Correo/WhatsApp) para ID Pago #{$reg->id_pago} ({$reg->nombre_sucursal})";
            } else {
                $resultados['errores']++;
            }
        }

        return $resultados;
    }

    /**
     * Envía el correo al Cliente (si tiene correo válido) y al Administrador.
     */
    private function enviarCorreosAviso(object $reg, string $nombreServicio): bool
    {
        $diasTxt = ($reg->dias_restantes == 0) 
            ? "¡VENCE HOY!" 
            : "vence en {$reg->dias_restantes} día(s)";

        $fechaFormateada = date('d/m/Y', strtotime($reg->fecha_de_vencimiento));
        $montoFormateado = number_format((float)$reg->precio, 2);

        try {
            // 1. ENVIAR CORREO AL CLIENTE
            if (!empty($reg->correo_cliente) && filter_var($reg->correo_cliente, FILTER_VALIDATE_EMAIL)) {
                $mailCliente = $this->crearInstanciaMailer();
                $mailCliente->addAddress($reg->correo_cliente, $reg->nombre_cliente ?: $reg->nombre_sucursal);
                $mailCliente->Subject = "⏰ Recordatorio de vencimiento: {$nombreServicio} - DSG Perú";
                
                $mailCliente->Body = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                        <title>Recordatorio de Vencimiento</title>
                    </head>
                    <body style='margin: 0; padding: 0; background-color: #f4f7f6; font-family: Arial, Helvetica, sans-serif;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout: fixed; background-color: #f4f7f6; padding: 20px 0;'>
                            <tr>
                                <td align='center'>
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' style='background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                                        <tr>
                                            <td align='center' style='background: linear-gradient(135deg, #0056b3 0%, #007bff 100%); padding: 30px 20px;'>
                                                <h1 style='color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;'>DSG PERÚ TECHNOLOGY</h1>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 35px 30px 20px 30px;'>
                                                <p style='color: #1a1a1a; font-size: 16px; margin: 0 0 10px 0;'>
                                                    Hola <b style='color: #007bff;'>{$reg->nombre_cliente}</b>,
                                                </p>
                                                <p style='color: #4a5568; font-size: 15px; line-height: 1.5; margin: 0 0 25px 0;'>
                                                    ¡Seguimos juntos! Tu servicio de la sucursal <b>{$reg->nombre_sucursal}</b> <span style='color: #e53e3e; font-weight: bold;'>{$diasTxt}</span>. Revisa los detalles de tu cuenta:
                                                </p>
                                                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom: 25px;'>
                                                    <tr>
                                                        <td width='48%' style='border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; vertical-align: top; background-color: #ffffff;'>
                                                            <p style='color: #718096; font-size: 12px; text-transform: uppercase; margin: 0 0 5px 0; font-weight: bold;'>Monto a pagar:</p>
                                                            <p style='color: #1a202c; font-size: 24px; font-weight: bold; margin: 0 0 15px 0;'>S/ {$montoFormateado}</p>
                                                            <p style='color: #718096; font-size: 12px; margin: 0 0 15px 0;'>Concepto: {$nombreServicio}</p>
                                                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                                                <tr>
                                                                    <td align='center' style='background-color: #007bff; border-radius: 6px;'>
                                                                        <a href='#' target='_blank' style='color: #ffffff; padding: 10px 15px; display: block; font-size: 14px; font-weight: bold; text-decoration: none;'>Renovar ahora</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td width='4%'></td>
                                                        <td width='48%' style='border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; vertical-align: top; background-color: #ffffff;'>
                                                            <p style='color: #718096; font-size: 12px; text-transform: uppercase; margin: 0 0 5px 0; font-weight: bold;'>Último día de pago:</p>
                                                            <p style='color: #1a202c; font-size: 22px; font-weight: bold; margin: 0 0 10px 0;'>📅 {$fechaFormateada}</p>
                                                            <hr style='border: none; border-top: 1px solid #edf2f7; margin: 12px 0;'>
                                                            <p style='color: #4a5568; font-size: 13px; margin: 0 0 4px 0;'><b>Empresa:</b> {$reg->razon_social}</p>
                                                            <p style='color: #4a5568; font-size: 13px; margin: 0;'><b>Sucursal:</b> {$reg->nombre_sucursal}</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #ebf8ff; border-radius: 8px; margin-bottom: 25px;'>
                                                    <tr>
                                                        <td style='padding: 15px 20px; color: #2b6cb0; font-size: 13px; line-height: 1.4;'>
                                                            💡 <b>Nota importante:</b> Por favor, comunícate con nosotros o realiza tu transferencia para mantener tu servicio activo sin interrupciones.
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #e2e8f0;'>
                                                <p style='color: #718096; font-size: 12px; margin: 0 0 5px 0;'>DSG Perú Technology · Pucallpa, Perú · Soporte 24/7</p>
                                                <p style='color: #a0aec0; font-size: 11px; margin: 0;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                ";
                $mailCliente->send();
            }

            // 2. ENVIAR CORREO AL ADMIN
            if (!empty($this->mailConfig->smtpToEmail)) {
                $mailAdmin = $this->crearInstanciaMailer();
                $mailAdmin->addAddress($this->mailConfig->smtpToEmail, $this->mailConfig->smtpToName);
                $mailAdmin->Subject = "🔔 [Alerta Admin] Plan/Pago por vencer: {$reg->nombre_sucursal} ({$diasTxt})";
                $mailAdmin->Body    = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #f59e0b; border-radius: 8px; overflow: hidden;'>
                        <div style='background: #f59e0b; color: #ffffff; padding: 16px 24px;'>
                            <h2 style='margin: 0; font-size: 20px;'>⚠️ Alerta de Vencimiento de Cliente</h2>
                        </div>
                        <div style='padding: 24px; color: #333333; line-height: 1.6;'>
                            <p>El siguiente registro se encuentra dentro de sus días de aviso (<b>{$diasTxt}</b>):</p>
                            <ul>
                                <li><b>Cliente:</b> {$reg->nombre_cliente}</li>
                                <li><b>Empresa / Sucursal:</b> {$reg->razon_social} - {$reg->nombre_sucursal}</li>
                                <li><b>Concepto / Plan:</b> {$nombreServicio}</li>
                                <li><b>Fecha de Vencimiento:</b> {$fechaFormateada}</li>
                                <li><b>Monto:</b> S/ {$montoFormateado}</li>
                                <li><b>Teléfono Sucursal:</b> " . ($reg->telefono_cliente ?: 'No registrado') . "</li>
                                <li><b>Correo notificado al cliente:</b> " . ($reg->correo_cliente ?: 'No tiene correo registrado') . "</li>
                                <li><b>Observaciones:</b> " . ($reg->observaciones ?: 'Ninguna') . "</li>
                            </ul>
                        </div>
                    </div>
                ";
                $mailAdmin->send();
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Error enviando aviso de vencimiento ID ' . $reg->id_pago . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía el aviso por WhatsApp al teléfono de empresa_sucursales y al Administrador.
     */
    private function enviarWhatsAppAviso(object $reg, string $nombreServicio): bool
    {
        $apiUrl     = env('WHATSAPP_API_URL', '');
        $apiToken   = env('WHATSAPP_TOKEN', '');
        $adminPhone = env('WHATSAPP_ADMIN_PHONE', '');

        if (empty($apiUrl) || empty($apiToken)) {
            return false;
        }

        $diasTxt = ($reg->dias_restantes == 0) ? "¡VENCE HOY!" : "vence en *{$reg->dias_restantes} día(s)*";
        $fechaFormateada = date('d/m/Y', strtotime($reg->fecha_de_vencimiento));
        $montoFormateado = number_format((float)$reg->precio, 2);

        // 1. Mensaje para el CLIENTE (usando telefono de empresa_sucursales)
        $telCliente = $this->formatearTelefonoPeru($reg->telefono_cliente ?? '');
        if ($telCliente) {
            $mensajeCliente = "👋 Hola *{$reg->nombre_cliente}* (Sucursal: *{$reg->nombre_sucursal}*).\n\n"
                . "Te saludamos de *DSG Perú Technology* para recordarte que tu servicio {$diasTxt}:\n\n"
                . "📌 *Servicio/Concepto:* {$nombreServicio}\n"
                . "🏢 *Empresa:* {$reg->razon_social}\n"
                . "📅 *Fecha de vencimiento:* {$fechaFormateada}\n"
                . "💰 *Monto:* S/ {$montoFormateado}\n\n"
                . "Por favor, contáctanos para realizar la renovación y evitar cortes en el servicio.";

            $this->dispararPeticionWhatsApp($apiUrl, $apiToken, $telCliente, $mensajeCliente);
        }

        // 2. Mensaje de alerta para el ADMINISTRADOR
        $telAdmin = $this->formatearTelefonoPeru($adminPhone);
        if (!empty($telAdmin)) {
            $mensajeAdmin = "🔔 *ALERTA ADMIN - VENCIMIENTO*\n\n"
                . "El siguiente registro {$diasTxt}:\n"
                . "• *Sucursal:* {$reg->nombre_sucursal} ({$reg->razon_social})\n"
                . "• *Cliente:* {$reg->nombre_cliente}\n"
                . "• *Servicio:* {$nombreServicio}\n"
                . "• *Vence:* {$fechaFormateada}\n"
                . "• *Monto:* S/ {$montoFormateado}\n"
                . "• *Tel. Sucursal:* " . ($telCliente ?: 'No registrado');

            $this->dispararPeticionWhatsApp($apiUrl, $apiToken, $telAdmin, $mensajeAdmin);
        }

        return true;
    }

    /**
     * Dispara la petición POST hacia el servidor local de Node.js (localhost:3000/enviar)
     */
    private function dispararPeticionWhatsApp(string $url, string $token, string $telefono, string $mensaje): void
    {
        try {
            $client = \Config\Services::curlrequest();
            $client->post($url, [
                'form_params' => [
                    'token' => $token,
                    'to'    => '+' . $telefono,
                    'body'  => $mensaje,
                ],
                'http_errors' => false,
                'timeout'     => 10,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error WhatsApp API: ' . $e->getMessage());
        }
    }

    /**
     * Convierte números de 9 dígitos de Perú (ej. 987654321) a 51987654321
     */
    private function formatearTelefonoPeru(?string $telefono): string
    {
        $limpio = preg_replace('/\D/', '', (string)$telefono);

        if (empty($limpio)) {
            return '';
        }

        if (strlen($limpio) === 9 && str_starts_with($limpio, '9')) {
            return '51' . $limpio;
        }

        if (strlen($limpio) === 11 && str_starts_with($limpio, '51')) {
            return $limpio;
        }

        return '';
    }

    private function crearInstanciaMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $this->mailConfig->smtpHost;
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->mailConfig->smtpUsername;
        $mail->Password   = $this->mailConfig->smtpPassword;
        $mail->SMTPSecure = $this->mailConfig->smtpEncryption === 'tls'
            ? PHPMailer::ENCRYPTION_STARTTLS
            : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = (int)$this->mailConfig->smtpPort;
        $mail->CharSet    = 'UTF-8';
        $mail->isHTML(true);
        $mail->setFrom($this->mailConfig->smtpFromEmail, $this->mailConfig->smtpFromName);

        return $mail;
    }
}