<?php
/* Cabecera común. Cada página define $titulo y $pagina_activa antes de incluirla. */
$titulo = $titulo ?? 'Tanianailsart';
$pagina_activa = $pagina_activa ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= limpiar($titulo) ?> | Tanianailsart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="css/estilos.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-tania">
  <div class="container">
    <a href="index.php" class="d-flex flex-column text-decoration-none">
      <span class="brand-nombre">Tanianailsart</span>
      <span class="brand-subtitulo">NAIL ART STUDIO</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link <?= $pagina_activa === 'inicio' ? 'active' : '' ?>" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link <?= $pagina_activa === 'buscar' ? 'active' : '' ?>" href="buscar.php">Buscar servicios</a></li>
        <li class="nav-item"><a class="nav-link <?= $pagina_activa === 'reservar' ? 'active' : '' ?>" href="reservar.php">Reservar turno</a></li>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-5">
