<?php
session_start();
header("Content-Type: application/json");

if (!isset($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "ID no recibido"]);
    exit;
}

require __DIR__ . "/../../../db/conexion.php";

$id = intval($_POST['id']);

try {
    $sql = $conn->prepare("SELECT * FROM productos WHERE id = :id LIMIT 1");
    $sql->execute([":id" => $id]);
    $producto = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo json_encode(["success" => false, "message" => "Producto no encontrado"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "producto" => $producto
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error interno",
        "error" => $e->getMessage()
    ]);
}
