<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexPOS - Login</title>
    <link rel="shortcut icon" href="assets/general/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <div class="container">
        <h1>Bienvenido</h1>
        <p>Ingrese sus credenciales para acceder al sistema</p>

        <form action="">

            <!-- Campo Usuario -->
            <div class="input-group">
                <span class="icon">
                    <img src="assets/index/persona.svg" alt="icono usuario">
                </span>
                <input type="text" placeholder="Ingrese su usuario" required>
            </div>

            <!-- Campo Contraseña -->
            <div class="input-group">
                <span class="icon">
                    <img src="assets/index/candado.svg" alt="icono candado">
                </span>

                <input type="password" placeholder="Ingrese su contraseña" id="password" required>

                <span class="toggle-pass" onclick="togglePassword()">
                    <img src="assets/index/ojo.svg" alt="ver contraseña">
                </span>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>

        <a href="#">¿Olvidaste tu contraseña?</a>
    </div>


    <p class="footer">© 2025 FlexPOS. Todos los derechos reservados.</p>
</body>

</html>