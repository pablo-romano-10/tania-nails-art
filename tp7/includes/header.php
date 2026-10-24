<?php
/* Cabecera común. Cada página define $titulo antes de incluirla. $base = ruta hacia la raíz del módulo. */
$titulo = $titulo ?? 'Administración';
$base   = $base ?? '..';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($titulo) ?> | Tanianailsart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="<?= $base ?>/css/estilos.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-tania">
  <div class="container">
    <a href="<?= $base ?>/admin/index.php" class="d-flex flex-column text-decoration-none">
      <span class="brand-nombre">Tanianailsart</span>
      <span class="brand-subtitulo">PANEL DE TURNOS</span>
    </a>
    <a href="<?= $base ?>/admin/crear.php" class="btn-reservar"><i class="bi bi-plus-lg"></i> NUEVO TURNO</a>
  </div>
</nav>
<main class="container py-5">
