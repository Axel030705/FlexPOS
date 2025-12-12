<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexPOS - Abrir Caja</title>
    <link rel="shortcut icon" href="assets/general/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">

    <!--SweetAlert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <h1>Apertura de Caja</h1>
        <p>Ingresa el fondo inicial para comenzar tus operaciones</p>

        <form id="cajaForm" method="POST">

            <!-- Fondo Inicial -->
            <div class="input-group">
                <span class="icon">
                    <img src="assets/index/caja.svg" alt="icono dinero">
                </span>

                <input type="number"
                    placeholder="Fondo inicial"
                    id="fondo_inicial"
                    name="fondo_inicial"
                    min="0"
                    step="1"
                    required>
            </div>

            <button type="submit">Abrir Caja</button>
        </form>
    </div>

    <p class="footer">© 2025 <a href="">FlexPOS</a>. Todos los derechos reservados.</p>

    <script src="js/abrir_caja.js"></script>
</body>

</html>