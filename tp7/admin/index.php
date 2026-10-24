<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

/* ---------- READ: listado de turnos (consulta preparada con filtro opcional) ---------- */
$estado = filter_input(INPUT_GET, 'estado', FILTER_VALIDATE_INT) ?: null;

$sql = "SELECT t.id, t.fecha_hora, t.notas,
               CONCAT(u.nombre, ' ', u.apellido) AS clienta,
               e.nombre AS estado,
               GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR ', ') AS servicios,
               COALESCE(SUM(d.precio_aplicado), 0) AS total
        FROM turnos t
        INNER JOIN usuarios u       ON u.id = t.usuario_id
        INNER JOIN estados_turno e  ON e.id = t.estado_id
        LEFT JOIN detalle_turno d   ON d.turno_id = t.id
        LEFT JOIN servicios s       ON s.id = d.servicio_id
        WHERE (:estado IS NULL OR t.estado_id = :estado2)
        GROUP BY t.id, t.fecha_hora, t.notas, u.nombre, u.apellido, e.nombre
        ORDER BY t.fecha_hora DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':estado' => $estado, ':estado2' => $estado]);
$turnos = $stmt->fetchAll();

$estados = $pdo->query('SELECT id, nombre FROM estados_turno ORDER BY id')->fetchAll();

/* ---------- Mensajes de resultado (vienen por GET desde crear/editar/eliminar) ---------- */
$mensajes = [
    'created' => ['success', 'Turno creado correctamente.'],
    'updated' => ['success', 'Turno actualizado correctamente.'],
    'deleted' => ['success', 'Turno eliminado correctamente.'],
    'notfound' => ['warning', 'El turno que buscabas no existe.'],
    'error'   => ['danger', 'No se pudo completar la operación.'],
];
$status = $_GET['status'] ?? '';
$aviso = $mensajes[$status] ?? null;

$titulo = 'Turnos';
include __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-1">Turnos</h1>
<p class="mb-4">Agenda del estudio: alta, modificación y baja de turnos.</p>

<?php if ($aviso): ?>
  <div class="alert alert-<?= h($aviso[0]) ?>" role="alert"><?= h($aviso[1]) ?></div>
<?php endif; ?>

<form method="get" class="row g-2 mb-4 align-items-end">
  <div class="col-sm-5 col-md-3">
    <label for="estado" class="form-label">Filtrar por estado</label>
    <select id="estado" name="estado" class="form-select" onchange="this.form.submit()">
      <option value="">Todos</option>
      <?php foreach ($estados as $e): ?>
        <option value="<?= h($e['id']) ?>" <?= $estado === (int) $e['id'] ? 'selected' : '' ?>><?= h(ucfirst($e['nombre'])) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</form>

<?php if (empty($turnos)): ?>
  <div class="alert alert-info">No hay turnos para mostrar. <a href="crear.php">Cargá el primero</a>.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped align-middle bg-white">
      <thead>
        <tr><th>ID</th><th>Fecha y hora</th><th>Clienta</th><th>Servicios</th><th>Estado</th><th>Total</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        <?php foreach ($turnos as $t): ?>
          <tr>
            <td><?= h($t['id']) ?></td>
            <td><?= h(date('d/m/Y H:i', strtotime($t['fecha_hora']))) ?></td>
            <td><?= h($t['clienta']) ?></td>
            <td><?= h($t['servicios']) ?></td>
            <td><span class="badge text-bg-secondary"><?= h(ucfirst($t['estado'])) ?></span></td>
            <td>$<?= h(number_format($t['total'], 0, ',', '.')) ?></td>
            <td class="text-nowrap">
              <a href="editar.php?id=<?= h($t['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
              <!-- El borrado va por POST: un enlace GET podría dispararse sin querer -->
              <form action="eliminar.php" method="post" class="d-inline"
                    onsubmit="return confirm('¿Confirma eliminar este turno?')">
                <input type="hidden" name="id" value="<?= h($t['id']) ?>">
                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
