<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../funciones/token.php';
require __DIR__ . '/../funciones/mail.php';

use Dotenv\Dotenv;

$dotenv = parse_ini_file(__DIR__ . '/../.env');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Validar que el email no esté vacío
    if (!$email) {
        $error = "Ingresa tu correo";
    } else {
        // Generar token para recuperación
        $token = generarToken($email);
        $url = "http://localhost:8000/db/reset-password.php?token=$token";

        // Crear asunto y cuerpo del correo
        $asunto = "Recuperar contraseña FlexPOS";
        $cuerpo = "
            <h3>Recuperación de contraseña</h3>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña:</p>
            <a href='$url'>$url</a>
            <p>El enlace expira en 15 minutos.</p>
        ";

        // Enviar correo usando la función general
        if (enviarCorreoGeneral($email, $asunto, $cuerpo)) {
            $success = "Se envió un enlace de recuperación a tu correo.";
        } else {
            $error = "Error al enviar correo. Revisa la configuración SMTP.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Recuperar contraseña</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/recover_password.css">
</head>

<body>

    <div class="container">
        <h2>Recuperar contraseña</h2>
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green'>$success</p>"; ?>

        <form method="POST" action="">
            <div class="input-group">
                <span class="icon">
                    <img src="../assets/index/correo.svg" alt="icono usuario">
                </span>
                <input type="email" name="email" placeholder="Tu correo" required>
            </div>
            <button type="submit">Enviar enlace</button>
        </form>
    </div>

    <p class="footer">© 2025 <a href="">FlexPOS</a>. Todos los derechos reservados.</p>
</body>

</html>