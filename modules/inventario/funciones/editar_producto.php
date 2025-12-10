<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_negocio'])) {
    echo json_encode(["success" => false, "message" => "Sesión no válida"]);
    exit;
}

require __DIR__ . "/../../../db/conexion.php";

$idNegocio = $_SESSION['id_negocio'];
$usuarioId = $_SESSION['usuario_id'];

try {

    if (empty($_POST['id_producto'])) {
        echo json_encode(["success" => false, "message" => "ID del producto no recibido"]);
        exit;
    }

    $idProducto  = intval($_POST['id_producto']);
    $nombre      = trim($_POST['nombre']);
    $codigo      = trim($_POST['codigo'] ?? "");
    $descripcion = trim($_POST['descripcion'] ?? "");
    $precio      = floatval($_POST['precio']);
    $costo       = floatval($_POST['costo']);
    $stock       = intval($_POST['stock']);
    $idCategoria = intval($_POST['id_categoria']);
    $idProveedor = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;
    $activo      = intval($_POST['activo']);

    /* ==========================
        PROCESAR IMAGEN (opcional)
    ========================== */
    $rutaImagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $dirUploads = __DIR__ . "/../../../uploads/imagenes/productos/";
        if (!file_exists($dirUploads)) {
            mkdir($dirUploads, 0777, true);
        }

        $tmp = $_FILES['imagen']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $permitidas)) {
            echo json_encode(["success" => false, "message" => "Formato de imagen no válido"]);
            exit;
        }

        $nuevoNombre = "prod_" . $idNegocio . "_" . time() . "." . $ext;
        $rutaFisica = $dirUploads . $nuevoNombre;

        move_uploaded_file($tmp, $rutaFisica);

        $rutaImagen = "/uploads/imagenes/productos/" . $nuevoNombre;
    }

    /* ==========================
        UPDATE EN BASE DE DATOS
    ========================== */

    $sql = "UPDATE productos SET
                nombre = :nombre,
                codigo = :codigo,
                descripcion = :descripcion,
                precio = :precio,
                costo = :costo,
                stock = :stock,
                id_categoria = :categoria,
                id_proveedor = :prov,
                activo = :activo"
        . ($rutaImagen ? ", url_imagen = :img" : "") . "
            WHERE id = :id AND id_negocio = :negocio";

    $stmt = $conn->prepare($sql);

    $params = [
        ":nombre" => $nombre,
        ":codigo" => $codigo,
        ":descripcion" => $descripcion,
        ":precio" => $precio,
        ":costo" => $costo,
        ":stock" => $stock,
        ":categoria" => $idCategoria,
        ":prov" => $idProveedor,
        ":activo" => $activo,
        ":id" => $idProducto,
        ":negocio" => $idNegocio
    ];

    if ($rutaImagen) $params[":img"] = $rutaImagen;

    $stmt->execute($params);

    echo json_encode(["success" => true, "message" => "Producto actualizado correctamente"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error interno", "error" => $e->getMessage()]);
}
