<?php
date_default_timezone_set('America/Mexico_City');
echo "Hora (PHP): " . date("Y-m-d H:i:s");

$contraseña = "1111";

// Generar hash usando el algoritmo por defecto (actualmente bcrypt)
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

echo "Hash generado: " . $hash;