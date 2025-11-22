<?php
header("Content-Type: application/json");

// Cargar .env
$dotenv = parse_ini_file(__DIR__ . '/../.env');

$host = $dotenv['DB_HOST'];
$dbname = $dotenv['DB_NAME'];
$user = $dotenv['DB_USER'];
$pass = $dotenv['DB_PASS'];
$charset = $dotenv['DB_CHARSET'];


$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos desde fetch()
    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($usuario == "" || $password == "") {
        echo json_encode(["status" => "error", "msg" => "Campos vacíos"]);
        exit;
    }

    // Consulta del usuario
    $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE correo = :usuario LIMIT 1");
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode(["status" => "error", "msg" => "Usuario no encontrado"]);
        exit;
    }

    // Verificar contraseña
    if (password_verify($password, $userData['password'])) {

        // Iniciar sesión (opcional)
        session_start();
        $_SESSION['usuario_id'] = $userData['id'];
        $_SESSION['usuario'] = $userData['nombre'];

        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "error", "msg" => "Contraseña incorrecta"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "msg" => "Error en la conexión: " . $e->getMessage()]);
}
