<?php
session_start();
date_default_timezone_set('America/Mexico_City');
header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_negocio'])) {
    echo json_encode(["success" => false, "message" => "Sesión no válida"]);
    exit;
}

require __DIR__ . "/../../../../db/conexion.php";

$idNegocio = $_SESSION['id_negocio'];
$usuarioId = $_SESSION['usuario_id'];

try {

    /* --------------------------
        VALIDAR CAMPOS OBLIGATORIOS
    --------------------------- */
    if (empty($_POST['nombre'])) {
        echo json_encode([
            "success" => false,
            "message" => "El nombre de la categoría es obligatorio."
        ]);
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $activo = isset($_POST['activo']) ? intval($_POST['activo']) : 1;

    // SIEMPRE TOMAR HORA DEL SERVIDOR
    $fecha  = date("Y-m-d H:i:s");

    /* --------------------------
        INSERTAR EN BD
    --------------------------- */

    $sql = "INSERT INTO categorias 
            (id_negocio, nombre, activo, fecha_registro)
            VALUES 
            (:negocio, :nombre, :activo, :fecha)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":negocio" => $idNegocio,
        ":nombre"  => $nombre,
        ":activo"  => $activo,
        ":fecha"   => $fecha
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Categoría creada correctamente."
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error interno al guardar categoría.",
        "error"   => $e->getMessage()
    ]);
}
