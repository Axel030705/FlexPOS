<?php
session_start();
require __DIR__ . '/../../../db/conexion.php';

$carrito = $_SESSION['carrito'] ?? [];

$respuesta = [];

if ($carrito) {
    $ids = implode(',', array_keys($carrito));
    $sql = "SELECT * FROM productos WHERE id IN ($ids)";
    $stmt = $conn->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productos as $p) {
        $id = $p['id'];
        $cantidad = $carrito[$id] ?? 1;
        $respuesta[] = [
            'id' => $id,
            'stock' => $p['stock'],
            'nombre' => $p['nombre'],
            'precio' => (float)$p['precio'],
            'imagen' => !empty($p['url_imagen']) ? '/uploads/imagenes/productos/' . $p['url_imagen'] : '/uploads/imagenes/productos/default.svg',
            'cantidad' => (int)$cantidad,
            'total' => (float)($p['precio'] * $cantidad)
        ];
    }
}

echo json_encode($respuesta);
