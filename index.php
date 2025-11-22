<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexPOS - Login</title>
    <link rel="shortcut icon" href="assets/general/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">

    <!--SweetAlert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <h1>Bienvenido</h1>
        <p>Ingrese sus credenciales para acceder al sistema</p>

        <form id="loginForm" method="POST">

            <!-- Campo Usuario -->
            <div class="input-group">
                <span class="icon">
                    <img src="assets/index/persona.svg" alt="icono usuario">
                </span>
                <input type="text" placeholder="Ingrese su usuario" id="usuario" name="usuario" required>
            </div>

            <!-- Campo Contraseña -->
            <div class="input-group">
                <span class="icon">
                    <img src="assets/index/candado.svg" alt="icono candado">
                </span>

                <input type="password" placeholder="Ingrese su contraseña" id="password" name="password" required>

                <span class="toggle-pass" onclick="togglePassword()">
                    <img src="assets/index/ojo.svg" alt="ver contraseña">
                </span>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>


        <p class="link_password" onclick="window.location='/funciones/recover_password.php';">
            ¿Olvidaste tu contraseña?
        </p>
    </div>


    <p class="footer">© 2025 <a href="">FlexPOS</a>. Todos los derechos reservados.</p>

    <script src="js/index.js"></script>
</body>

</html>