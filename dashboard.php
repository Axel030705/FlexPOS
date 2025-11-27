<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Datos de sesión
$usuarioId = $_SESSION['usuario_id'];
$nombreUsuario = $_SESSION['usuario'];
$idNegocio = $_SESSION['id_negocio'];
$planUsuario = $_SESSION['plan'];

// Conexión a la BD
require 'db/conexion.php';

// Consulta los módulos activos para este usuario
$stmt = $conn->prepare("
    SELECT m.nombre, m.archivo, m.icono
    FROM cliente_modulo cm
    INNER JOIN modulos m ON cm.id_modulo = m.id
    WHERE cm.id_cliente = :idCliente 
    AND cm.activo = 1
    AND m.nombre != 'Configuración'
");

$stmt->bindParam(":idCliente", $usuarioId);
$stmt->execute();
$modulosActivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cargar módulo específico de Configuración
$stmtConfig = $conn->prepare("
    SELECT m.nombre, m.archivo, m.icono
    FROM modulos m
    WHERE m.nombre = 'Configuración'
");
$stmtConfig->execute();
$moduloConfig = $stmtConfig->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="shortcut icon" href="assets/general/favicon.png" type="image/x-icon">

    <!--SweetAlert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="sidebar">

        <div class="container_logo">
            <img src="assets/general/logo_dashboard.svg" alt="Logo de la empresa">
        </div>

        <ul id="menu-modulos">
            <?php foreach ($modulosActivos as $modulo): ?>
                <?php
                $icono = !empty($modulo['icono']) ? $modulo['icono'] : 'default.svg';
                ?>
                <li>
                    <a href="#" data-archivo="/modules/<?= $modulo['nombre'] ?>/<?= $modulo['archivo'] ?>">
                        <img src="/assets/dashboard/<?= $icono ?>" alt="<?= $modulo['nombre'] ?> icon">
                        <?= $modulo['nombre'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="container_conf">
            <?php if ($moduloConfig): ?>
                <li>
                    <a href="#" id="modulo-config" data-archivo="/modules/<?= $moduloConfig['nombre'] ?>/<?= $moduloConfig['archivo'] ?>">
                        <img src="/assets/dashboard/<?= $moduloConfig['icono'] ?>" alt="<?= $moduloConfig['nombre'] ?> icon">
                        <?= $moduloConfig['nombre'] ?>
                    </a>
                </li>
            <?php endif; ?>

            <div class="div_perfil">
                <img src="assets/index/persona.svg" alt="Icono de usuario">
                <div class="div_perfil2">
                    <h1><?= htmlspecialchars($nombreUsuario) ?></span></h1>
                    <span><?= htmlspecialchars($idNegocio) ?></span>
                </div>
            </div>
        </div>


    </div>

    <section id="contenido-modulo"></section>


    <script src="/js/dashboard.js"></script>
</body>

</html>