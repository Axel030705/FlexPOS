<?php
session_start();

require __DIR__ . "/../../../db/conexion.php";

$usuarioId = $_SESSION['usuario_id'];
$idNegocio = $_SESSION['id_negocio'];
$planUsuario = $_SESSION['plan'];

/* Cargar categorías */
$sqlCat = $conn->prepare("SELECT id, nombre FROM categorias WHERE id_negocio = :neg AND activo = 1");
$sqlCat->execute(['neg' => $idNegocio]);
$categorias = $sqlCat->fetchAll(PDO::FETCH_ASSOC);

/* Cargar proveedores */
$sqlProv = $conn->prepare("SELECT id, nombre FROM proveedores WHERE id_negocio = :neg AND activo = 1");
$sqlProv->execute(['neg' => $idNegocio]);
$proveedores = $sqlProv->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="modal fondo-modal">
    <div class="modal-box">

        <button class="cerrar-modal">✖</button>
        <h2>Editar Producto</h2>

        <form id="formEditarProducto" enctype="multipart/form-data">

            <input type="hidden" name="id_producto">
            <input type="hidden" name="id_negocio" value="<?= $idNegocio ?>">
            <input type="hidden" name="id_usuario" value="<?= $usuarioId ?>">
            <input type="hidden" name="fecha_registro" value="<?= date("Y-m-d H:i:s") ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label>Nombre del producto</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="form-group">
                    <label>Código / SKU</label>
                    <input type="text" name="codigo">
                </div>

                <div class="form-group">
                    <label>Categoría</label>
                    <select name="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Proveedor</label>
                    <select name="id_proveedor">
                        <option value="">Sin proveedor</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Costo ($)</label>
                    <input type="number" name="costo" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Precio venta ($)</label>
                    <input type="number" name="precio" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Stock inicial</label>
                    <input type="number" name="stock" required>
                </div>

                <div class="form-group">
                    <label>Activo</label>
                    <select name="activo">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3"></textarea>
                </div>

                <!-- CAMPO PARA SUBIR IMAGEN -->
                <div class="form-group full">
                    <label>Imagen del producto</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>

                <div id="previewImagen" style="margin-bottom:10px;"></div>
            </div>

            <button type="submit" class="btn-guardar">Guardar cambios</button>

        </form>

    </div>
</div>