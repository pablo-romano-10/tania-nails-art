<?php
/* Conexión PDO con manejo de excepciones. Base: tanianailsart_auth (ver sql/usuarios.sql) */
$host    = 'localhost';
$db      = 'tanianailsart_auth';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log('Error de conexión: ' . $e->getMessage());
    http_response_code(500);
    die('Error crítico de conexión con la base de datos. Verificá que MySQL esté iniciado y que exista la base "' . htmlspecialchars($db) . '".');
}
