<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno desde .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores con excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // resultados como array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // usar prepared statements reales
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
