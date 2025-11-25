<?php
require __DIR__ . '/../../db/conexion.php';
// Aquí puedes procesar datos desde la base si lo necesitas
// Ejemplo: $resultado = $conn->query("SELECT COUNT(*) FROM ventas WHERE ...");
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

        </div>
        <div class="div2">

        </div>
    </div>
</div>