<?php
$rol_requerido = 'admin';                       // solo administradores
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/config/conexion.php';

$aviso = '';

/* Cambio de rol (POST + CSRF). Un admin no puede modificar su propio rol. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $rol = post('rol');
    if (!csrf_valido()) {
        $aviso = ['danger', 'La sesión del formulario venció. Probá de nuevo.'];
    } elseif (!$id || !in_array($rol, ['admin', 'usuario'], true)) {
        $aviso = ['danger', 'Datos inválidos.'];
    } elseif ($id === (int) $_SESSION['usuario_id']) {
        $aviso = ['warning', 'No podés cambiar tu propio rol.'];
    } else {
        $u = $pdo->prepare('UPDATE usuarios SET rol = :rol WHERE id = :id');
        $u->execute([':rol' => $rol, ':id' => $id]);
        $aviso = $u->rowCount() ? ['success', 'Rol actualizado.'] : ['info', 'No hubo cambios.'];
    }
}

$usuarios = $pdo->query('SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY created_at DESC, id DESC')->fetchAll();

$titulo = 'Administración';
include __DIR__ . '/includes/header.php';
?>

<h1 class="mb-1">Administración de usuarios</h1>
<p class="mb-4">Solo visible para administradores. Total: <?= count($usuarios) ?> usuario(s).</p>

<?php if ($aviso): ?><div class="alert alert-<?= h($aviso[0]) ?>"><?= h($aviso[1]) ?></div><?php endif; ?>

<div class="table-responsive">
  <table class="table table-striped align-middle bg-white">
    <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Alta</th><th>Rol</th></tr></thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><?= h($u['id']) ?></td>
          <td><?= h($u['nombre']) ?></td>
          <td><?= h($u['email']) ?></td>
          <td><?= h(date('d/m/Y', strtotime($u['created_at']))) ?></td>
          <td>
            <?php if ((int) $u['id'] === (int) $_SESSION['usuario_id']): ?>
              <span class="badge text-bg-warning"><?= h($u['rol']) ?> (vos)</span>
            <?php else: ?>
              <form action="admin_panel.php" method="post" class="d-flex gap-2">
                <?= csrf_campo() ?>
                <input type="hidden" name="id" value="<?= h($u['id']) ?>">
                <select name="rol" class="form-select form-select-sm w-auto">
                  <option value="usuario" <?= $u['rol'] === 'usuario' ? 'selected' : '' ?>>usuario</option>
                  <option value="admin"   <?= $u['rol'] === 'admin'   ? 'selected' : '' ?>>admin</option>
                </select>
                <button class="btn btn-sm btn-outline-dark" type="submit">Guardar</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
