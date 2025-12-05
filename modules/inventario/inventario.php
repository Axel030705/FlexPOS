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
$sql = "
    SELECT c.*
    FROM categorias c
    INNER JOIN productos p ON p.id_categoria = c.id
    WHERE c.id_negocio = :id_negocio
      AND c.activo = 1
      AND p.activo = 1
    GROUP BY c.id
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_negocio', $idNegocio, PDO::PARAM_INT);
$stmt->execute();



$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="modulo-inventario">
    <div class="main">
        <div class="div1">
            <div class="header">
                <div class="menu-title">
                    <img src="../../assets/inventario/menu.svg" class="imagen_menu" alt="icono menu">
                    <h1>Inventario</h1>
                </div>

                <div class="container_acciones">

                    <div class="container_btns">
                        <button class="btn-add" data-negocio="<?= $idNegocio ?>">
                            <img src="/assets/inventario/add.svg" alt="icono">
                            Nuevo producto
                        </button>

                        <button class="btn-add" data-negocio="<?= $idNegocio ?>">
                            <img src="/assets/inventario/add.svg" alt="icono">
                            Nueva categoría
                        </button>
                    </div>

                    <div class="search-container">
                        <div class="search-icon">
                            <img src="/assets/inventario/busqueda.svg" alt="Buscar">
                        </div>
                        <input type="text" id="buscadorProductos" placeholder="Buscar por nombre...">
                    </div>
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

                    <!-- Categorías extra por defecto para inventario -->
                    <button data-cat="stock_bajo">Stock bajo</button>
                    <button data-cat="sin_stock">Sin stock</button>
                    <button data-cat="ocultos">Ocultos</button>
                    <button data-cat="añadir_producto">Agregar producto</button>
                    <button data-cat="añadir_categoria">Agregar categoria</button>

                </div>

                <div class="container_productos" id="productosContainer">
                    <!-- Tabla de productos filtrados -->
                    <div class="tabla-productos-filtrados">
                        <table id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Costo</th>
                                    <th>Venta</th>
                                    <th>Proveedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filas generadas por AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>


    </div>