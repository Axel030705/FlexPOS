<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo "Sesión no iniciada";
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$nombreUsuario = $_SESSION['usuario'];
$idNegocio = $_SESSION['id_negocio'];
$planUsuario = $_SESSION['plan'];


require __DIR__ . '/../../db/conexion.php';

// Obtener categorías activas del negocio desde la base de datos
$sql = "SELECT * FROM categorias WHERE id_negocio = :id_negocio AND activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_negocio', $idNegocio, PDO::PARAM_INT);
$stmt->execute();


$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="modulo-ventas">
    <div class="main">
        <div class="div1">
            <div class="header">
                <h1>Ventas</h1>
                <div class="search-container">
                    <div class="search-icon">
                        <img src="/assets/ventas/busqueda.svg" alt="Buscar">
                    </div>
                    <input type="text" placeholder="Buscar por nombre o código...">
                </div>
            </div>

            <div class="container_main_p">
                <div class="container_filtros" id="sliderCategorias">
                    <button data-cat="0" class="active">Todos</button>

                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <button data-cat="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-cat">No hay categorías registradas.</p>
                    <?php endif; ?>
                </div>

                <div class="container_productos" id="productosContainer">
                    <!-- Los productos filtrados se cargarán aquí mediante AJAX -->
                </div>
                
            </div>
        </div>



        <div class="div2">

        </div>
    </div>
</div>