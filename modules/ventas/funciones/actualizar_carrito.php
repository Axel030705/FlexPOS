<?php
session_start();
require __DIR__ . '/../../../db/conexion.php';

$id = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? null;

if (!$id || !$cantidad) {
    echo json_encode(['success' => false, 'msg' => 'Faltan datos']);
    exit;
}

// Consultamos el stock del producto
$sql = "SELECT stock FROM productos WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el producto
if (!$producto) {
    echo json_encode(['success' => false, 'msg' => 'Producto no encontrado']);
    exit;
}

// Validamos stock
$stockDisponible = (int)$producto['stock'];
$nuevaCantidad = (int)$cantidad;

if ($nuevaCantidad > $stockDisponible) {
    echo json_encode([
        'success' => false,
        'msg' => "Cantidad excede el stock disponible ({$stockDisponible})"
    ]);
    exit;
}

// Todo bien, actualizamos carrito
$_SESSION['carrito'][$id] = $nuevaCantidad;

echo json_encode([
    'success' => true,
    'msg' => 'Cantidad actualizada correctamente',
    'stock_restante' => $stockDisponible - $nuevaCantidad
]);
