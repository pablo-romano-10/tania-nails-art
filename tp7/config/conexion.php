<?php
/* ============================================================
   CONEXIÓN PDO A MYSQL (con manejo de excepciones)
   Base creada en el TP6 con schema.sql
   ============================================================ */
$host    = 'localhost';
$db      = 'tanianailsart';
$user    = 'root';
$pass    = '';            // WampServer / XAMPP: root sin contraseña por defecto
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // los errores lanzan excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // filas como arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statements reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Se registra el detalle técnico y al usuario se le muestra un mensaje genérico
    error_log('Error de conexión: ' . $e->getMessage());
    http_response_code(500);
    die('Error crítico de conexión con la base de datos. Verificá que MySQL esté iniciado y que exista la base "' . htmlspecialchars($db) . '".');
}
