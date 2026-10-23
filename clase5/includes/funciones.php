<?php
/* ============================================================
   FUNCIONES DE SEGURIDAD Y VALIDACIÓN (reutilizables)
   ============================================================ */

/**
 * Sanitiza un texto para MOSTRARLO en pantalla (previene XSS):
 * trim() saca espacios, htmlspecialchars() convierte < > " ' & en
 * entidades HTML, así un <script> se ve como texto y no se ejecuta.
 */
function limpiar(?string $valor): string
{
    return htmlspecialchars(trim($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Lee un campo de $_POST o $_GET con filter_input() y lo devuelve
 * ya sanitizado. Si el campo no vino, devuelve ''.
 */
function leer_campo(int $tipo, string $nombre): string
{
    $valor = filter_input($tipo, $nombre, FILTER_UNSAFE_RAW);
    return limpiar(is_string($valor) ? $valor : '');
}

/** Devuelve true si el email tiene un formato válido. */
function email_valido(string $email): bool
{
    // Se valida el valor SIN entidades HTML (por eso se decodifica antes)
    $plano = html_entity_decode($email, ENT_QUOTES, 'UTF-8');
    return filter_var($plano, FILTER_VALIDATE_EMAIL) !== false;
}

/** Devuelve el mensaje de error de un campo o '' si no tiene. */
function error_de(array $errores, string $campo): string
{
    return $errores[$campo] ?? '';
}
