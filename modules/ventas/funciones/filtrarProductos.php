<?php
require __DIR__ . '/../../../db/conexion.php';

session_start();
$idNegocio = $_SESSION['id_negocio'] ?? null;
$categoria = $_POST['categoria'] ?? null;

if (!$idNegocio) {
    echo "<p>Error: No se ha iniciado sesión correctamente.</p>";
    exit;
}

try {
    if ($categoria === '0' || $categoria === null) { // Si es "Todos"
        $sql = "SELECT * FROM productos 
                WHERE id_negocio = :id_negocio AND activo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_negocio', $idNegocio, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM productos 
                WHERE id_negocio = :id_negocio AND id_categoria = :categoria AND activo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_negocio', $idNegocio, PDO::PARAM_INT);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
    }

    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        foreach ($productos as $producto) {
            // Ruta de imagen
            $imagen = !empty($producto['url_imagen'])
                ? '/uploads/imagenes/productos/' . htmlspecialchars($producto['url_imagen'])
                : '/uploads/imagenes/productos/default.svg'; // Imagen por defecto si no tiene

            echo '<div class="producto">';
            echo '  <div class="img-producto">';
            echo '      <img src="' . $imagen . '" alt="' . htmlspecialchars($producto['nombre']) . '">';
            echo '  </div>';
            echo '<div class="texto-producto">';
            echo '  <h1>' . htmlspecialchars($producto['nombre']) . '</h1>';
            echo '  <p>$' . number_format($producto['precio'], 2) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>No hay productos en esta categoría.</p>";
    }

} catch (PDOException $e) {
    echo "<p>Error en la consulta: " . $e->getMessage() . "</p>";
}
?>
