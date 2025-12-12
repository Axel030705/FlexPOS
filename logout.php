<?php
session_start();
require_once "db/conexion.php";

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si el usuario tiene caja abierta
$sql = "SELECT id_apertura 
        FROM aperturas_caja 
        WHERE id_usuario = :id_usuario AND fecha_cierre IS NULL 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);

// Tiene caja abierta → Mostrar SweetAlert y bloquear cierre
if ($stmt->rowCount() > 0) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Caja abierta',
            text: 'No puedes cerrar sesión mientras tengas una caja abierta.',
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = '../dashboard.php';
        });
    </script>";
    exit;
}

//No hay caja abierta → cerrar sesión
session_destroy();
header('Location: index.php');
exit;
?>
