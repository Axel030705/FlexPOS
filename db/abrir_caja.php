<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header("Content-Type: application/json");

try {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(["status" => "error", "msg" => "Sesión expirada"]);
        exit;
    }

    $dotenv = parse_ini_file(__DIR__ . '/../.env');

    $pdo = new PDO(
        "mysql:host={$dotenv['DB_HOST']};dbname={$dotenv['DB_NAME']};charset={$dotenv['DB_CHARSET']}",
        $dotenv['DB_USER'],
        $dotenv['DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $id_usuario = $_SESSION['usuario_id'];
    $fondo = isset($_POST['fondo_inicial']) ? floatval($_POST['fondo_inicial']) : 0;

    if ($fondo < 0) {
        echo json_encode(["status" => "error", "msg" => "El fondo debe ser un número válido"]);
        exit;
    }

    // Validar si ya existe una caja abierta
    $check = $pdo->prepare("
        SELECT id_apertura 
        FROM aperturas_caja 
        WHERE id_usuario = :id_usuario 
        AND estado = 'abierta'
        LIMIT 1
    ");
    $check->execute(["id_usuario" => $id_usuario]);

    if ($check->rowCount() > 0) {
        echo json_encode(["status" => "error", "msg" => "Ya tienes una caja abierta"]);
        exit;
    }

    // Registrar apertura
    $stmt = $pdo->prepare("
        INSERT INTO aperturas_caja (id_usuario, fondo_inicial, fecha_apertura, estado)
        VALUES (:id_usuario, :fondo, NOW(), 'abierta')
    ");

    $stmt->execute([
        "id_usuario" => $id_usuario,
        "fondo" => $fondo
    ]);

    $id_apertura = $pdo->lastInsertId();

    $_SESSION['id_apertura'] = $id_apertura;

    echo json_encode([
        "status" => "ok",
        "id_apertura" => $id_apertura
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "msg" => $e->getMessage()
    ]);
}
