<?php
session_start();

$id = $_POST['id_producto'] ?? null;
if ($id && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

echo json_encode(['success' => true]);
