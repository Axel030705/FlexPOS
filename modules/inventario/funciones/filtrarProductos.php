<?php
require __DIR__ . '/../../../db/conexion.php';

session_start();
$idNegocio = $_SESSION['id_negocio'] ?? null;
$categoria = $_POST['categoria'] ?? null;

header('Content-Type: application/json; charset=utf-8');

if (!$idNegocio) {
    echo json_encode(["error" => "No se ha iniciado sesión correctamente."]);
    exit;
}

try {
    // Base SQL
    $sql = "SELECT p.*, 
        (SELECT nombre FROM proveedores WHERE id = p.id_proveedor LIMIT 1) AS proveedor
        FROM productos p
        WHERE p.id_negocio = :id_negocio";

    // Filtrado por categoría
    if ($categoria === 'stock_bajo') {
        $sql .= " AND p.activo = 1 AND p.stock > 0 AND p.stock <= 10";
    } elseif ($categoria === 'sin_stock') {
        $sql .= " AND p.activo = 1 AND p.stock = 0";
    } elseif ($categoria === 'ocultos') {
        $sql .= " AND p.activo = 0";
    } elseif ($categoria !== '0' && $categoria !== null) {
        $sql .= " AND p.activo = 1 AND p.id_categoria = :categoria";
    } else {
        // Si es "Todos" (0) → solo productos activos
        $sql .= " AND p.activo = 1";
    }



    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_negocio', $idNegocio, PDO::PARAM_INT);

    if (
        $categoria !== '0' && $categoria !== 'stock_bajo' &&
        $categoria !== 'sin_stock' && $categoria !== 'ocultos'
    ) {
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
    }



    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay productos
    if (!$productos) {
        echo json_encode([]);
        exit;
    }

    // Formatear productos
    foreach ($productos as &$producto) {
        $producto['imagen'] = !empty($producto['imagen']) ? $producto['imagen'] : "/uploads/imagenes/productos/default.svg";

        $producto['proveedor'] = !empty($producto['proveedor']) ? $producto['proveedor'] : "No se asignó un proveedor";

        $producto['costo'] = number_format((float)$producto['costo'], 2, '.', '');
        $producto['precio'] = number_format((float)$producto['precio'], 2, '.', '');

        $producto['nombre'] = htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8');
        $producto['codigo'] = htmlspecialchars($producto['codigo'] ?? '', ENT_QUOTES, 'UTF-8');

        // Stock mínimo fijo para lógica de alertas
        $producto['stock_min'] = 10;
    }



    echo json_encode($productos, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
