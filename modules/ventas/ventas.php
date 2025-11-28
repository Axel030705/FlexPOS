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

<div class="modulo-ventas">
    <div class="main">
        <div class="div1">
            <div class="header">
                <div class="menu-title">
                <img src="../../assets/ventas/menu.svg" class="imagen_menu" alt="icono menu">
                <h1>Ventas</h1>
                </div>
                <div class="search-container">
                    <div class="search-icon">
                        <img src="/assets/ventas/busqueda.svg" alt="Buscar">
                    </div>
                    <input type="text" id="buscadorProductos" placeholder="Buscar por nombre o código...">
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

            <div class="header">
                <h1>Venta Actual</h1>

                <div class="searchC-container">
                    <div class="searchC-icon">
                        <img src="/assets/index/persona.svg" alt="Buscar">
                    </div>
                    <input type="text" placeholder="Telefono...">
                </div>
            </div>

            <div class="container_pedido">
                <div class="productos_pedido" id="productosPedido">
                    <!-- Productos añadidos al pedido se mostrarán aquí -->
                </div>
            </div>

            <div class="resumen_pedido">
                <div class="detalle_totales">
                    <div class="fila_subtotal">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>

                    <div class="fila_impuestos">
                        <span>Impuestos (16%):</span>
                        <span id="impuestos">$0.00</span>
                    </div>

                    <div class="fila_total">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>

                    <div class="container_btns">
                        <button id="btnFinalizar" class="btn_finalizar">Finalizar Venta</button>
                        <button id="btnVaciar" class="btn_vaciar">Vaciar Carrito</button>
                    </div>

                </div>
            </div>
        </div>