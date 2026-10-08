<?php

namespace App\Controllers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailController extends BaseController
{
    private function getMailConfig(): \Config\ExternalServices
    {
        return config('ExternalServices');
    }

    private function hasRequiredMailConfig(\Config\ExternalServices $config): bool
    {
        return $config->smtpHost !== ''
            && $config->smtpUsername !== ''
            && $config->smtpPassword !== ''
            && $config->smtpFromEmail !== ''
            && $config->smtpToEmail !== '';
    }

    public function enviar()
    {
        require ROOTPATH . 'vendor/autoload.php';

        // Antispam: máximo 5 mensajes cada 10 minutos por IP
        $throttler = service('throttler');
        if ($throttler->check('contacto-' . $this->request->getIPAddress(), 5, 10 * MINUTE) === false) {
            return redirect()->back()->with('error', 'Demasiados mensajes seguidos. Espera unos minutos e inténtalo de nuevo.');
        }

        // Validación mínima antes de intentar enviar
        $rules = [
            'nombre'  => 'required|min_length[2]|max_length[120]',
            'correo'  => 'required|valid_email|max_length[160]',
            'mensaje' => 'permit_empty|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Revisa tu nombre y escribe un correo válido.');
        }

        $mail = new PHPMailer(true);
        $config = $this->getMailConfig();

        if (! $this->hasRequiredMailConfig($config)) {
            return redirect()->back()->with('error', '❌ El servicio de correo no está configurado correctamente.');
        }

        try {
            
            $mail->isSMTP();
            $mail->Host       = $config->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $config->smtpUsername;
            $mail->Password   = $config->smtpPassword;
            $mail->SMTPSecure = $config->smtpEncryption === 'tls'
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $config->smtpPort;

            //  Remitente y destinatario
            $mail->setFrom($config->smtpFromEmail, $config->smtpFromName);
            $mail->addAddress($config->smtpToEmail, $config->smtpToName);

            // Contenido
            $mail->isHTML(true);
$mail->CharSet = 'UTF-8'; // Asegura que los emojis y tildes se vean bien
$mail->Subject = $this->request->getPost('asunto') ?? 'Nuevo contacto desde la web';

// Obtenemos los datos y limpiamos espacios en blanco
$nombre   = htmlspecialchars($this->request->getPost('nombre'));
$correo   = htmlspecialchars($this->request->getPost('correo'));
$empresa  = htmlspecialchars($this->request->getPost('empresa'));
$telefono = htmlspecialchars($this->request->getPost('telefono'));
$mensaje  = nl2br(htmlspecialchars($this->request->getPost('mensaje'))); 

$mail->Body = "
    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <h2 style='color: #007bff;'>Nuevo contacto desde la web ❤️❤️</h2>
        <hr>
        <p><b>Nombre:</b> {$nombre}</p>
        <p><b>Correo:</b> <a href='mailto:{$correo}'>{$correo}</a></p>
        <p><b>Empresa:</b> " . ($empresa ?: 'No especificada') . "</p>
        <p><b>Teléfono:</b> {$telefono}</p>
        <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #007bff;'>
            <b>Mensaje:</b><br>
            {$mensaje}
        </div>
        <hr>
        <footer style='font-size: 0.8em; color: #777;'>
            Enviado desde el sistema de contacto de tu sitio web.
        </footer>
    </div>
";

            $mail->send();
            return redirect()->back()->with('success', '✅ Tu mensaje fue enviado correctamente.');
        } catch (Exception $e) {
            log_message('error', 'EmailController::enviar falló: {error}', ['error' => $mail->ErrorInfo ?: $e->getMessage()]);

            return redirect()->back()->with('error', '❌ No se pudo enviar el mensaje en este momento.');
        }
    }
}
