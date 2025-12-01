<?php
require __DIR__ . '/../../../db/conexion.php';

session_start();
$idNegocio = $_SESSION['id_negocio'] ?? null;

header('Content-Type: application/json; charset=utf-8');

if (!$idNegocio) {
    echo json_encode(["success" => false, "message" => "No se ha iniciado sesión correctamente."]);
    exit;
}

if (!isset($_POST['accion'], $_POST['id'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$accion = $_POST['accion'];
$id = intval($_POST['id']); // Seguridad

if ($accion === 'toggleProducto') {
    try {
        // 1️⃣ Obtener estado actual del producto
        $stmt = $conn->prepare("SELECT activo FROM productos WHERE id = :id AND id_negocio = :idNegocio LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':idNegocio', $idNegocio, PDO::PARAM_INT);
        $stmt->execute();

        $estadoActual = $stmt->fetchColumn();

        if ($estadoActual === false) {
            echo json_encode(["success" => false, "message" => "Producto no encontrado o sin permisos"]);
            exit;
        }

        // 2️⃣ Toggle → Si está 1 (visible) lo pasamos a 0 (oculto); si está 0 lo pasamos a 1
        $nuevoEstado = $estadoActual == 1 ? 0 : 1;

        // 3️⃣ Actualizar
        $stmt = $conn->prepare("UPDATE productos SET activo = :nuevoEstado WHERE id = :id AND id_negocio = :idNegocio");
        $stmt->bindParam(':nuevoEstado', $nuevoEstado, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':idNegocio', $idNegocio, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "nuevoEstado" => $nuevoEstado
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo actualizar el producto"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la BD: " . $e->getMessage()]);
    }

    exit;
}

echo json_encode(["success" => false, "message" => "Acción inválida"]);
exit;
