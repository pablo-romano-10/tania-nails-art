<?php
require __DIR__ . '/includes/funciones.php';

$servicios = require __DIR__ . '/data/servicios.php';
$categorias = ['' => 'Todas', 'esculpidas' => 'Esculpidas', 'gel' => 'Gel', 'diseno' => 'Diseño'];

/* ---------- 1. Capturar por GET (sanitizado) ---------- */
$q = leer_campo(INPUT_GET, 'q');
$categoria = leer_campo(INPUT_GET, 'categoria');
$buscando = isset($_GET['q']) || isset($_GET['categoria']);
$errores = [];
$resultados = [];

/* ---------- 2. Validar ---------- */
if ($buscando) {
    if (mb_strlen($q) > 50) {
        $errores['q'] = 'La búsqueda no puede superar los 50 caracteres.';
    }
    if (!array_key_exists($categoria, $categorias)) {
        $errores['categoria'] = 'La categoría elegida no es válida.';
    }

    /* ---------- 3. Filtrar ---------- */
    if (empty($errores)) {
        $termino = mb_strtolower(html_entity_decode($q, ENT_QUOTES, 'UTF-8'));
        foreach ($servicios as $s) {
            $coincideTexto = $termino === '' || str_contains(mb_strtolower($s['nombre'] . ' ' . $s['desc']), $termino);
            $coincideCat = $categoria === '' || $s['categoria'] === $categoria;
            if ($coincideTexto && $coincideCat) $resultados[] = $s;
        }
    }
}

$titulo = 'Buscar servicios';
$pagina_activa = 'buscar';
include __DIR__ . '/includes/header.php';
?>

<h1 class="mb-2">Buscar servicios</h1>
<p class="mb-4">Encontrá el servicio ideal por nombre o categoría.</p>

<form action="buscar.php" method="get" class="row g-3 mb-4 form-tania" novalidate>
  <div class="col-md-6">
    <label for="q" class="form-label">¿Qué buscás?</label>
    <input type="text" id="q" name="q" placeholder="Ej: esculpidas"
           class="form-control <?= error_de($errores, 'q') ? 'is-invalid' : '' ?>"
           value="<?= $q ?>">
    <div class="invalid-feedback"><?= error_de($errores, 'q') ?></div>
  </div>
  <div class="col-md-4">
    <label for="categoria" class="form-label">Categoría</label>
    <select id="categoria" name="categoria"
            class="form-select <?= error_de($errores, 'categoria') ? 'is-invalid' : '' ?>">
      <?php foreach ($categorias as $valor => $texto): ?>
        <option value="<?= $valor ?>" <?= $categoria === $valor ? 'selected' : '' ?>><?= $texto ?></option>
      <?php endforeach; ?>
    </select>
    <div class="invalid-feedback"><?= error_de($errores, 'categoria') ?></div>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <button type="submit" class="btn-reservar border-0 w-100 justify-content-center">
      <i class="bi bi-search"></i> BUSCAR
    </button>
  </div>
</form>

<?php if ($buscando && empty($errores)): ?>
  <?php if ($q !== ''): ?>
    <p class="mb-3">Resultados para «<strong><?= $q ?></strong>»: <?= count($resultados) ?></p>
  <?php endif; ?>

  <?php if (empty($resultados)): ?>
    <div class="alert alert-warning">No encontramos servicios con esos filtros.</div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($resultados as $s): ?>
        <div class="col-md-4">
          <div class="tarjeta-servicio">
            <h3><?= limpiar($s['nombre']) ?></h3>
            <p><?= limpiar($s['desc']) ?></p>
            <p class="fw-bold mb-0">$<?= number_format($s['precio'], 0, ',', '.') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php elseif (!empty($errores)): ?>
  <div class="alert alert-danger">Revisá los filtros de búsqueda.</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
