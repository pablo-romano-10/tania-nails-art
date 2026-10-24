<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

/* Solo se acepta POST: así un link o un buscador no puede borrar datos. */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php?status=notfound'); exit; }

try {
    // El detalle se borra solo por ON DELETE CASCADE (definido en schema.sql)
    $stmt = $pdo->prepare('DELETE FROM turnos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $estado = $stmt->rowCount() > 0 ? 'deleted' : 'notfound';
} catch (PDOException $e) {
    error_log($e->getMessage());
    $estado = 'error';
}

header('Location: index.php?status=' . $estado);
exit;
