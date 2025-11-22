<?php
// Cambia esta contraseña por la que quieras encriptar
$password = "1111";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña original: " . $password . "<br>";
echo "Hash generado: " . $hashedPassword;
?>
