<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/funciones.php';
$titulo = 'Inicio';
include __DIR__ . '/includes/header.php';
?>
<h1 class="mb-3">TP8 · Sesiones y autenticación</h1>
<?php if (esta_logueado()): ?>
  <p>Ya iniciaste sesión como <strong><?= h($_SESSION['usuario_nombre']) ?></strong>. <a href="dashboard.php">Ir a mi panel</a>.</p>
<?php else: ?>
  <p>Creá una cuenta o iniciá sesión para acceder al panel protegido.</p>
  <a href="registro.php" class="btn-reservar me-2">CREAR CUENTA</a>
  <a href="login.php" class="btn-reservar">INICIAR SESIÓN</a>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
