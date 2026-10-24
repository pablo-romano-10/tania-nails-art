<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/config/conexion.php';

$q = $pdo->prepare('SELECT nombre, email, rol, created_at FROM usuarios WHERE id = :id');
$q->execute([':id' => $_SESSION['usuario_id']]);
$u = $q->fetch();

// Si la cuenta ya no existe, se cierra la sesión por seguridad
if (!$u) { session_destroy(); header('Location: login.php?error=acceso_denegado'); exit; }

$titulo = 'Mi perfil';
include __DIR__ . '/includes/header.php';
?>

<h1 class="mb-4">Mi perfil</h1>
<div class="tarjeta-servicio">
  <dl class="row mb-0">
    <dt class="col-sm-3">Nombre</dt><dd class="col-sm-9"><?= h($u['nombre']) ?></dd>
    <dt class="col-sm-3">Correo</dt><dd class="col-sm-9"><?= h($u['email']) ?></dd>
    <dt class="col-sm-3">Rol</dt><dd class="col-sm-9"><?= h($u['rol']) ?></dd>
    <dt class="col-sm-3">Alta</dt><dd class="col-sm-9"><?= h(date('d/m/Y H:i', strtotime($u['created_at']))) ?></dd>
  </dl>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
