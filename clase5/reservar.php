<?php
require __DIR__ . '/includes/funciones.php';

/* ---------- 1. Estado inicial ---------- */
$errores = [];
$exito = false;
$datos = ['nombre' => '', 'email' => '', 'telefono' => '', 'servicio' => '', 'mensaje' => ''];
$servicios = require __DIR__ . '/data/servicios.php';
$validos = array_column($servicios, 'nombre');

/* ---------- 2. Procesar SOLO si llegó por POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2.1 Capturar + sanitizar cada campo (filter_input + trim + htmlspecialchars)
    foreach ($datos as $campo => $_) {
        $datos[$campo] = leer_campo(INPUT_POST, $campo);
    }

    // 2.2 Validar en el servidor
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'Ingresá tu nombre.';
    } elseif (mb_strlen($datos['nombre']) < 3) {
        $errores['nombre'] = 'El nombre debe tener al menos 3 caracteres.';
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'Ingresá tu correo electrónico.';
    } elseif (!email_valido($datos['email'])) {
        $errores['email'] = 'El correo no tiene un formato válido.';
    }

    if ($datos['telefono'] !== '' && !preg_match('/^[0-9 +\-]{6,20}$/', $datos['telefono'])) {
        $errores['telefono'] = 'El teléfono solo admite números, espacios, + y -.';
    }

    if ($datos['servicio'] === '') {
        $errores['servicio'] = 'Elegí un servicio.';
    } elseif (!in_array(html_entity_decode($datos['servicio'], ENT_QUOTES, 'UTF-8'), $validos, true)) {
        $errores['servicio'] = 'El servicio elegido no es válido.';
    }

    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'Contanos qué diseño buscás.';
    } elseif (mb_strlen($datos['mensaje']) > 500) {
        $errores['mensaje'] = 'El mensaje no puede superar los 500 caracteres.';
    }

    // 2.3 Sin errores = éxito (acá iría el guardado en base de datos / envío de mail)
    if (empty($errores)) {
        $exito = true;
        $guardado = $datos;                                   // para mostrar el resumen
        $datos = array_fill_keys(array_keys($datos), '');     // se vacía el formulario
    }
}

$titulo = 'Reservar turno';
$pagina_activa = 'reservar';
include __DIR__ . '/includes/header.php';
?>

<h1 class="mb-2">Reservar turno</h1>
<p class="mb-4">Completá el formulario y Tania se contacta con vos para confirmar el horario.</p>

<?php if ($exito): ?>
  <div class="alert alert-success" role="alert">
    <strong>¡Solicitud enviada!</strong> Gracias, <?= $guardado['nombre'] ?>.
    Te escribiremos a <?= $guardado['email'] ?> para confirmar tu turno de
    <em><?= $guardado['servicio'] ?></em>.
  </div>
<?php elseif (!empty($errores)): ?>
  <div class="alert alert-danger" role="alert">
    <strong>Revisá el formulario:</strong> hay <?= count($errores) ?> campo(s) con errores.
  </div>
<?php endif; ?>

<form action="reservar.php" method="post" novalidate class="form-tania">

  <div class="mb-3">
    <label for="nombre" class="form-label">Nombre y apellido</label>
    <input type="text" id="nombre" name="nombre"
           class="form-control <?= error_de($errores, 'nombre') ? 'is-invalid' : '' ?>"
           value="<?= $datos['nombre'] ?>">
    <div class="invalid-feedback"><?= error_de($errores, 'nombre') ?></div>
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label for="email" class="form-label">Correo electrónico</label>
      <input type="email" id="email" name="email"
             class="form-control <?= error_de($errores, 'email') ? 'is-invalid' : '' ?>"
             value="<?= $datos['email'] ?>">
      <div class="invalid-feedback"><?= error_de($errores, 'email') ?></div>
    </div>
    <div class="col-md-6 mb-3">
      <label for="telefono" class="form-label">Teléfono (opcional)</label>
      <input type="text" id="telefono" name="telefono"
             class="form-control <?= error_de($errores, 'telefono') ? 'is-invalid' : '' ?>"
             value="<?= $datos['telefono'] ?>">
      <div class="invalid-feedback"><?= error_de($errores, 'telefono') ?></div>
    </div>
  </div>

  <div class="mb-3">
    <label for="servicio" class="form-label">Servicio</label>
    <select id="servicio" name="servicio"
            class="form-select <?= error_de($errores, 'servicio') ? 'is-invalid' : '' ?>">
      <option value="">Elegí un servicio…</option>
      <?php foreach ($servicios as $s): ?>
        <option value="<?= limpiar($s['nombre']) ?>"
          <?= $datos['servicio'] === limpiar($s['nombre']) ? 'selected' : '' ?>>
          <?= limpiar($s['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <div class="invalid-feedback"><?= error_de($errores, 'servicio') ?></div>
  </div>

  <div class="mb-4">
    <label for="mensaje" class="form-label">Mensaje</label>
    <textarea id="mensaje" name="mensaje" rows="4"
              class="form-control <?= error_de($errores, 'mensaje') ? 'is-invalid' : '' ?>"><?= $datos['mensaje'] ?></textarea>
    <div class="invalid-feedback"><?= error_de($errores, 'mensaje') ?></div>
  </div>

  <button type="submit" class="btn-reservar btn-reservar-lg border-0">
    <i class="bi bi-calendar-event"></i> ENVIAR SOLICITUD
  </button>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
