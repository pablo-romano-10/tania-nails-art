<?php
/* Formulario reutilizado por crear.php y editar.php.
   Espera: $datos, $errores, $opciones, $accion, $textoBoton (y opcional $id_oculto) */
?>
<form action="<?= h($accion) ?>" method="post" novalidate class="form-tania">
  <?php if (isset($id_oculto)): ?>
    <input type="hidden" name="id" value="<?= h($id_oculto) ?>">
  <?php endif; ?>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label for="usuario_id" class="form-label">Clienta</label>
      <select id="usuario_id" name="usuario_id" class="form-select <?= isset($errores['usuario_id']) ? 'is-invalid' : '' ?>">
        <option value="">Elegí una clienta…</option>
        <?php foreach ($opciones['usuarios'] as $u): ?>
          <option value="<?= h($u['id']) ?>" <?= (string) $datos['usuario_id'] === (string) $u['id'] ? 'selected' : '' ?>>
            <?= h($u['apellido'] . ', ' . $u['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback"><?= h($errores['usuario_id'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-3">
      <label for="estado_id" class="form-label">Estado</label>
      <select id="estado_id" name="estado_id" class="form-select <?= isset($errores['estado_id']) ? 'is-invalid' : '' ?>">
        <option value="">Elegí un estado…</option>
        <?php foreach ($opciones['estados'] as $e): ?>
          <option value="<?= h($e['id']) ?>" <?= (string) $datos['estado_id'] === (string) $e['id'] ? 'selected' : '' ?>>
            <?= h(ucfirst($e['nombre'])) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback"><?= h($errores['estado_id'] ?? '') ?></div>
    </div>
  </div>

  <div class="mb-3">
    <label for="fecha_hora" class="form-label">Fecha y hora</label>
    <input type="datetime-local" id="fecha_hora" name="fecha_hora"
           class="form-control <?= isset($errores['fecha_hora']) ? 'is-invalid' : '' ?>"
           value="<?= h($datos['fecha_hora']) ?>">
    <div class="invalid-feedback"><?= h($errores['fecha_hora'] ?? '') ?></div>
  </div>

  <div class="mb-3">
    <span class="form-label d-block">Servicios</span>
    <?php foreach ($opciones['servicios'] as $s): ?>
      <div class="form-check">
        <input class="form-check-input <?= isset($errores['servicios']) ? 'is-invalid' : '' ?>" type="checkbox"
               name="servicios[]" id="serv<?= h($s['id']) ?>" value="<?= h($s['id']) ?>"
               <?= in_array((int) $s['id'], $datos['servicios'], true) ? 'checked' : '' ?>>
        <label class="form-check-label" for="serv<?= h($s['id']) ?>">
          <?= h($s['nombre']) ?> <span class="text-muted">($<?= h(number_format($s['precio'], 0, ',', '.')) ?>)</span>
        </label>
      </div>
    <?php endforeach; ?>
    <?php if (isset($errores['servicios'])): ?>
      <div class="text-danger small mt-1"><?= h($errores['servicios']) ?></div>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label for="notas" class="form-label">Notas (opcional)</label>
    <textarea id="notas" name="notas" rows="3"
              class="form-control <?= isset($errores['notas']) ? 'is-invalid' : '' ?>"><?= h($datos['notas']) ?></textarea>
    <div class="invalid-feedback"><?= h($errores['notas'] ?? '') ?></div>
  </div>

  <button type="submit" class="btn-reservar btn-reservar-lg border-0"><?= h($textoBoton) ?></button>
  <a href="index.php" class="btn btn-link text-decoration-none ms-2">Cancelar</a>
</form>
