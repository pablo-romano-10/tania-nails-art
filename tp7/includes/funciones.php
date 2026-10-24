<?php
/* Funciones reutilizables */

/** Escapa un valor para imprimirlo en HTML (previene XSS). */
function h($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Lee un campo de $_POST como texto, sin espacios sobrantes. */
function post(string $campo): string
{
    $valor = filter_input(INPUT_POST, $campo, FILTER_UNSAFE_RAW);
    return is_string($valor) ? trim($valor) : '';
}

/** Valida que un string sea una fecha/hora real tipo 2026-11-05T10:00 */
function fecha_hora_valida(string $valor): bool
{
    $d = DateTime::createFromFormat('Y-m-d\TH:i', $valor);
    return $d && $d->format('Y-m-d\TH:i') === $valor;
}

/** Valida todos los campos del formulario de turnos. Devuelve array de errores. */
function validar_turno(PDO $pdo, array $d): array
{
    $errores = [];

    if ($d['usuario_id'] === '' || !ctype_digit($d['usuario_id'])) {
        $errores['usuario_id'] = 'Elegí la clienta.';
    } else {
        $q = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE id = :id');
        $q->execute([':id' => $d['usuario_id']]);
        if (!$q->fetchColumn()) $errores['usuario_id'] = 'La clienta elegida no existe.';
    }

    if ($d['estado_id'] === '' || !ctype_digit($d['estado_id'])) {
        $errores['estado_id'] = 'Elegí el estado.';
    } else {
        $q = $pdo->prepare('SELECT COUNT(*) FROM estados_turno WHERE id = :id');
        $q->execute([':id' => $d['estado_id']]);
        if (!$q->fetchColumn()) $errores['estado_id'] = 'El estado elegido no existe.';
    }

    if ($d['fecha_hora'] === '') {
        $errores['fecha_hora'] = 'Elegí la fecha y la hora.';
    } elseif (!fecha_hora_valida($d['fecha_hora'])) {
        $errores['fecha_hora'] = 'La fecha y hora no tienen un formato válido.';
    }

    if (mb_strlen($d['notas']) > 500) {
        $errores['notas'] = 'Las notas no pueden superar los 500 caracteres.';
    }

    if (empty($d['servicios'])) {
        $errores['servicios'] = 'Elegí al menos un servicio.';
    } else {
        // Todos los servicios enviados tienen que existir
        $marcas = implode(',', array_fill(0, count($d['servicios']), '?'));
        $q = $pdo->prepare("SELECT COUNT(*) FROM servicios WHERE id IN ($marcas)");
        $q->execute($d['servicios']);
        if ((int) $q->fetchColumn() !== count($d['servicios'])) {
            $errores['servicios'] = 'Algún servicio elegido no existe.';
        }
    }

    return $errores;
}

/** Lee los servicios tildados como array de enteros únicos. */
function servicios_enviados(): array
{
    $valores = filter_input(INPUT_POST, 'servicios', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY) ?: [];
    $ids = [];
    foreach ($valores as $v) {
        if (is_string($v) && ctype_digit($v)) $ids[] = (int) $v;
    }
    return array_values(array_unique($ids));
}

/** Datos para los <select> y checkboxes del formulario. */
function datos_formulario(PDO $pdo): array
{
    return [
        'usuarios'  => $pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE activo = 1 ORDER BY apellido, nombre")->fetchAll(),
        'estados'   => $pdo->query("SELECT id, nombre FROM estados_turno ORDER BY id")->fetchAll(),
        'servicios' => $pdo->query("SELECT id, nombre, precio FROM servicios WHERE activo = 1 ORDER BY nombre")->fetchAll(),
    ];
}

/** Guarda los servicios de un turno (dentro de una transacción ya abierta). */
function guardar_detalle(PDO $pdo, int $turno_id, array $servicios): void
{
    $q = $pdo->prepare(
        'INSERT INTO detalle_turno (turno_id, servicio_id, precio_aplicado)
         SELECT :turno, id, precio FROM servicios WHERE id = :servicio'
    );
    foreach ($servicios as $servicio_id) {
        $q->execute([':turno' => $turno_id, ':servicio' => $servicio_id]);
    }
}
