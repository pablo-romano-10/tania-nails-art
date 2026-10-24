<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/config/conexion.php';

if (esta_logueado()) { header('Location: dashboard.php'); exit; }

const MAX_INTENTOS = 5;       // intentos fallidos permitidos
const BLOQUEO_SEG  = 60;      // segundos de espera al superarlos

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mb_strtolower(post('email'));
    $password = post_crudo('password');

    if (!csrf_valido()) {
        $error = 'La sesión del formulario venció. Probá de nuevo.';
    } elseif (($_SESSION['bloqueado_hasta'] ?? 0) > time()) {
        $error = 'Demasiados intentos. Esperá un minuto antes de volver a probar.';
    } elseif ($email === '' || $password === '') {
        $error = 'Ingresá tu correo y tu contraseña.';
    } else {
        // 1. Buscar al usuario (consulta preparada)
        $q = $pdo->prepare('SELECT id, nombre, password, rol FROM usuarios WHERE email = :email');
        $q->execute([':email' => $email]);
        $usuario = $q->fetch();

        // 2. Verificar la contraseña con password_verify().
        //    Si el email no existe se compara contra un hash falso para que el tiempo de respuesta sea parecido.
        $hash = $usuario['password'] ?? '$2y$10$usesomesillystringforsaleOOCcDCxQXVjCfI2LLTmq3o3mOQ3W6';
        $ok = password_verify($password, $hash);

        if ($usuario && $ok) {
            // 3. Credenciales correctas: se renueva el ID de sesión (anti Session Fixation)
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = (int) $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['rol']            = $usuario['rol'];
            unset($_SESSION['intentos'], $_SESSION['bloqueado_hasta']);

            header('Location: dashboard.php');
            exit;
        }

        // 4. Falló: mensaje genérico (no revela si el email existe) y control de intentos
        $_SESSION['intentos'] = ($_SESSION['intentos'] ?? 0) + 1;
        if ($_SESSION['intentos'] >= MAX_INTENTOS) {
            $_SESSION['bloqueado_hasta'] = time() + BLOQUEO_SEG;
            $_SESSION['intentos'] = 0;
        }
        $error = 'Correo o contraseña incorrectos.';
    }
}

/* Avisos que llegan por la URL (se muestran escapados, con textos fijos) */
$avisos = [];
if (($_GET['error'] ?? '') === 'acceso_denegado') $avisos[] = ['warning', 'Tenés que iniciar sesión para ver esa página.'];
if (($_GET['registro'] ?? '') === 'ok')           $avisos[] = ['success', 'Cuenta creada correctamente. Ya podés iniciar sesión.'];
if (($_GET['logout'] ?? '') === 'ok')             $avisos[] = ['info', 'Cerraste sesión correctamente.'];

$titulo = 'Iniciar sesión';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <h1 class="mb-3">Iniciar sesión</h1>

    <?php foreach ($avisos as [$tipo, $texto]): ?>
      <div class="alert alert-<?= h($tipo) ?>"><?= h($texto) ?></div>
    <?php endforeach; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>

    <form action="login.php" method="post" novalidate class="form-tania">
      <?= csrf_campo() ?>
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" id="email" name="email" autocomplete="email" class="form-control" value="<?= h($email) ?>">
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" name="password" autocomplete="current-password" class="form-control">
      </div>
      <button type="submit" class="btn-reservar btn-reservar-lg border-0 w-100 justify-content-center">ENTRAR</button>
      <p class="text-center mt-3 mb-0">¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
