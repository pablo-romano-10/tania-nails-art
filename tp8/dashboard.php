<?php
require_once __DIR__ . '/includes/auth_guard.php';

$sinPermiso = ($_GET['error'] ?? '') === 'sin_permiso';
$titulo = 'Mi panel';
include __DIR__ . '/includes/header.php';
?>

<h1 class="mb-1">Hola, <?= h($_SESSION['usuario_nombre']) ?></h1>
<p class="mb-4">Rol actual: <span class="badge text-bg-secondary"><?= h($_SESSION['rol']) ?></span></p>

<?php if ($sinPermiso): ?>
  <div class="alert alert-danger">No tenés permiso para entrar a esa sección.</div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-md-6"><div class="tarjeta-servicio">
    <h3>Mi perfil</h3>
    <p>Consultá los datos de tu cuenta.</p>
    <a href="perfil.php" class="btn-reservar">VER PERFIL</a>
  </div></div>
  <?php if (es_admin()): ?>
  <div class="col-md-6"><div class="tarjeta-servicio">
    <h3>Administración</h3>
    <p>Gestioná los usuarios y sus roles.</p>
    <a href="admin_panel.php" class="btn-reservar">IR AL PANEL</a>
  </div></div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
