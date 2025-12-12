<?php
session_start();
header("Content-Type: application/json");

try {
    $dotenv = parse_ini_file(__DIR__ . '/../.env');

    $dsn = "mysql:host={$dotenv['DB_HOST']};dbname={$dotenv['DB_NAME']};charset={$dotenv['DB_CHARSET']}";
    $pdo = new PDO($dsn, $dotenv['DB_USER'], $dotenv['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($usuario == "" || $password == "") {
        echo json_encode(["status" => "error", "msg" => "Campos vacíos"]);
        exit;
    }

    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "msg" => "Formato de usuario inválido"]);
        exit;
    }

    // Obtener usuario + rol
    $stmt = $pdo->prepare("
        SELECT id, nombre, password, activo, plan, id_negocio, id_rol
        FROM usuarios
        WHERE correo = :usuario
        LIMIT 1
    ");
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode(["status" => "error", "msg" => "Usuario no encontrado"]);
        exit;
    }

    if ($userData['activo'] != 1) {
        echo json_encode(["status" => "error", "msg" => "Tu cuenta está inactiva. Contacta a tu proveedor."]);
        exit;
    }

    if (!password_verify($password, $userData['password'])) {
        echo json_encode(["status" => "error", "msg" => "Contraseña incorrecta"]);
        exit;
    }

    // Guardamos todos los datos en la sesión
    $_SESSION['usuario_id'] = $userData['id'];
    $_SESSION['usuario'] = $userData['nombre'];
    $_SESSION['id_rol'] = $userData['id_rol'];
    $_SESSION['plan'] = $userData['plan'];
    $_SESSION['id_negocio'] = $userData['id_negocio'];

    // ==========================================
    // 🔍 VALIDACIÓN DE CAJA ABIERTA SI ES CAJERO
    // ==========================================
    if ($userData['id_rol'] == 2) { // Cajero
        $stmtCaja = $pdo->prepare("
            SELECT id_apertura
            FROM aperturas_caja
            WHERE id_usuario = :id_usuario
            AND fecha_cierre IS NULL
            LIMIT 1
        ");
        $stmtCaja->execute([':id_usuario' => $userData['id']]);

        if ($stmtCaja->rowCount() > 0) {
            // Ya tiene caja abierta → Mandarlo al dashboard normal
            echo json_encode([
                "status" => "ok",
                "id_rol" => 2,
                "tiene_caja" => true
            ]);
            exit;
        } else {
            // No tiene caja abierta → Mandarlo a abrir_caja.php
            echo json_encode([
                "status" => "ok",
                "id_rol" => 2,
                "tiene_caja" => false
            ]);
            exit;
        }
    }

    // ==========================================
    // 🔍 SI ES ADMIN (id_rol 1)
    // ==========================================
    echo json_encode([
        "status" => "ok",
        "id_rol" => 1
    ]);
    

} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(["status" => "error", "msg" => "Error interno. Intenta más tarde."]);
}
