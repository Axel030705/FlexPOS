<?php
session_start();

$idProducto = $_POST['id_producto'] ?? null;
$cantidad = 1; // Por defecto agregamos 1

if (!$idProducto) {
    echo json_encode(['success' => false, 'msg' => 'Producto no especificado']);
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Si el producto ya está en el carrito, aumentamos la cantidad
if (isset($_SESSION['carrito'][$idProducto])) {
    $_SESSION['carrito'][$idProducto] += $cantidad;
} else {
    $_SESSION['carrito'][$idProducto] = $cantidad;
}

echo json_encode([
    'success' => true,
    'carrito' => $_SESSION['carrito']
]);
