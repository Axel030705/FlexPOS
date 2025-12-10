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
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->execute([":id" => $id]);

    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al eliminar"]);
}
