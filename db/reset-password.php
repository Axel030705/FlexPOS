<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../funciones/token.php';

use Dotenv\Dotenv;

// Cargar variables de entorno desde la raíz
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Verificar token
$token = $_GET['token'] ?? '';
$email = verificarToken($token);

if (!$email) {
    die("Token inválido o expirado.");
}

// Conexión a la base de datos
$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$password || !$password2) {
        $error = "Completa ambos campos";
    } elseif ($password !== $password2) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } else {
        // Guardar contraseña encriptada
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = :hash WHERE correo = :correo");
        $stmt->execute(['hash' => $hash, 'correo' => $email]);

        $success = "Contraseña cambiada correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña</title>
    <link rel="stylesheet" href="../css/reset_password.css">
</head>
<body>
    <div class="container">
        <h2>Ingresa nueva contraseña</h2>

        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <?php if(isset($success)) echo "<p style='color:green'>$success</p>"; ?>

        <form method="POST">
            <!-- Contraseña -->
            <div class="input-group">
                <span class="icon">
                    <img src="../assets/index/candado.svg" alt="candado">
                </span>
                <input type="password" id="password1" name="password" placeholder="Ingrese su contraseña" required>
                <span class="toggle-pass" onclick="togglePassword('password1')">
                    <img src="../assets/index/ojo.svg" alt="ver contraseña">
                </span>
            </div>

            <!-- Repetir contraseña -->
            <div class="input-group">
                <span class="icon">
                    <img src="../assets/index/candado.svg" alt="candado">
                </span>
                <input type="password" id="password2" name="password2" placeholder="Repite la contraseña" required>
                <span class="toggle-pass" onclick="togglePassword('password2')">
                    <img src="../assets/index/ojo.svg" alt="ver contraseña">
                </span>
            </div>

            <button type="submit">Cambiar contraseña</button>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const password = document.getElementById(inputId);
            password.type = password.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
