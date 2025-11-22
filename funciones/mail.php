<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; 
use Dotenv\Dotenv;

// Cargar variables del .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function enviarCorreoGeneral($destinatario, $asunto, $cuerpoHTML, $remitenteNombre = 'FlexPOS') {
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = 'ssl'; // o 'tls' según tu servidor
        $mail->Port = $_ENV['SMTP_PORT'];

        // Remitente
        $mail->setFrom($_ENV['SMTP_USER'], $remitenteNombre);

        // Destinatario
        $mail->addAddress($destinatario);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHTML;

        // Enviar
        $mail->send();
        return true;

    } catch (Exception $e) {
        // Opcional: registrar $e->getMessage() en un log
        return false;
    }
}
