<?php
require __DIR__ . '/includes/funciones.php';
$titulo = 'Inicio';
$pagina_activa = 'inicio';
include __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">Clase 5 · Formularios y seguridad web</h1>
<p>Dos formularios procesados del lado del servidor con PHP:</p>
<div class="row g-4">
  <div class="col-md-6"><div class="tarjeta-servicio">
    <h3>Reservar turno (POST)</h3>
    <p>Formulario con validación, mensajes de error y persistencia de datos.</p>
    <a href="reservar.php" class="btn-reservar">IR AL FORMULARIO</a>
  </div></div>
  <div class="col-md-6"><div class="tarjeta-servicio">
    <h3>Buscar servicios (GET)</h3>
    <p>Buscador con filtros por nombre y categoría que viajan por la URL.</p>
    <a href="buscar.php" class="btn-reservar">IR AL BUSCADOR</a>
  </div></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
