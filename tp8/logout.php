<?php
require_once __DIR__ . '/includes/sesion.php';

/* Solo se cierra sesión por POST con token CSRF (un link externo no puede desloguearte). */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valido()) {
    header('Location: index.php');
    exit;
}

// 1. Vaciar las variables de sesión
$_SESSION = [];

// 2. Eliminar la cookie de sesión del navegador
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?: 'Lax',
    ]);
}

// 3. Destruir la sesión en el servidor
session_destroy();

header('Location: login.php?logout=ok');
exit;
