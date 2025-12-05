<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['id_negocio'])) {
    echo json_encode(["success" => false, "message" => "Sesión no válida"]);
    exit;
}

require __DIR__ . '/../../../../db/conexion.php';

$idNegocio = $_SESSION['id_negocio'];
$usuarioId = $_SESSION['usuario_id'];

try {

    // Validación campos obligatorios
    $camposObligatorios = ['nombre', 'precio', 'costo', 'stock', 'id_categoria'];
    foreach ($camposObligatorios as $campo) {
        if (empty($_POST[$campo])) {
            echo json_encode(["success" => false, "message" => "El campo $campo es obligatorio"]);
            exit;
        }
    }

    $nombre       = trim($_POST['nombre']);
    $codigo       = trim($_POST['codigo'] ?? "");
    $descripcion  = trim($_POST['descripcion'] ?? "");
    $precio       = floatval($_POST['precio']);
    $costo        = floatval($_POST['costo']);
    $stock        = intval($_POST['stock']);
    $idCategoria  = intval($_POST['id_categoria']);
    $idProveedor  = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;
    $activo       = isset($_POST['activo']) ? intval($_POST['activo']) : 1;
    $fecha        = date("Y-m-d H:i:s");

    /* --------------------------
        PROCESAR IMAGEN
    --------------------------- */
    $rutaImagenFinal = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        $dirUploads = __DIR__ . "/../../../uploads/imagenes/productos/";
        if (!file_exists($dirUploads)) {
            mkdir($dirUploads, 0777, true);
        }

        $tmp = $_FILES['imagen']['tmp_name'];
        $nombreOriginal = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Extensiones permitidas
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $permitidas)) {
            echo json_encode(["success" => false, "message" => "Formato de imagen no válido (solo JPG, PNG, WEBP)"]);
            exit;
        }

        // Nuevo nombre seguro
        $nuevoNombre = "prod_" . $idNegocio . "_" . time() . "." . $ext;
        $rutaServidor = $dirUploads . $nuevoNombre;

        if (!move_uploaded_file($tmp, $rutaServidor)) {
            echo json_encode(["success" => false, "message" => "No se pudo guardar la imagen"]);
            exit;
        }

        // Ruta que se guarda en BD
        $rutaImagenFinal = "/uploads/imagenes/productos/" . $nuevoNombre;
    }

    /* --------------------------
        INSERTAR EN BD
    --------------------------- */

    $sql = "INSERT INTO productos 
            (id_negocio, id_categoria, nombre, codigo, descripcion, precio, costo, stock, id_proveedor, activo, url_imagen, fecha_registro)
            VALUES 
            (:neg, :cat, :nombre, :codigo, :descripcion, :precio, :costo, :stock, :prov, :activo, :ruta, :fecha)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":neg"      => $idNegocio,
        ":cat"      => $idCategoria,
        ":nombre"   => $nombre,
        ":codigo"   => $codigo,
        ":descripcion" => $descripcion,
        ":precio"   => $precio,
        ":costo"    => $costo,
        ":stock"    => $stock,
        ":prov"     => $idProveedor,
        ":activo"   => $activo,
        ":ruta"     => $rutaImagenFinal,
        ":fecha"    => $fecha
    ]);

    echo json_encode(["success" => true, "message" => "Producto creado correctamente"]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error interno",
        "error" => $e->getMessage()
    ]);
}
