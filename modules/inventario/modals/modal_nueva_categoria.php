<?php
session_start();

require __DIR__ . "/../../../db/conexion.php";

$usuarioId = $_SESSION['usuario_id'];
$idNegocio = $_SESSION['id_negocio'];
?>

<div class="modal fondo-modal">
    <div class="modal-box">

        <button class="cerrar-modal">✖</button>
        <h2>Nueva Categoría</h2>

        <form id="formNuevaCategoria">

            <!-- Datos ocultos -->
            <input type="hidden" name="id_negocio" value="<?= $idNegocio ?>">
            <input type="hidden" name="id_usuario" value="<?= $usuarioId ?>">
            <input type="hidden" name="fecha_registro" value="<?= date("Y-m-d H:i:s") ?>">

            <div class="form-grid">

                <div class="form-group full">
                    <label>Nombre de la categoría</label>
                    <input type="text" name="nombre" placeholder="Ej. Bebidas, Snacks, Limpieza" required>
                </div>

                <!-- <div class="form-group">
                    <label>Activo</label>
                    <select name="activo" required>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div> -->

                <input type="hidden" name="activo" value="1">

            </div>

            <button type="submit" class="btn-guardar">Guardar categoría</button>

        </form>

    </div>
</div>
