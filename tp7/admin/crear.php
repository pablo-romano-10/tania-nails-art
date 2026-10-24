<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

$errores = [];
$errorGeneral = '';
$datos = ['usuario_id' => '', 'estado_id' => '', 'fecha_hora' => '', 'notas' => '', 'servicios' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Captura y limpieza
    $datos = [
        'usuario_id' => post('usuario_id'),
        'estado_id'  => post('estado_id'),
        'fecha_hora' => post('fecha_hora'),
        'notas'      => post('notas'),
        'servicios'  => servicios_enviados(),
    ];

    // 2. Validación en servidor
    $errores = validar_turno($pdo, $datos);

    // 3. Inserción con consultas preparadas (turno + detalle en una transacción)
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                'INSERT INTO turnos (usuario_id, estado_id, fecha_hora, notas)
                 VALUES (:usuario, :estado, :fecha, :notas)'
            );
            $stmt->execute([
                ':usuario' => (int) $datos['usuario_id'],
                ':estado'  => (int) $datos['estado_id'],
                ':fecha'   => str_replace('T', ' ', $datos['fecha_hora']) . ':00',
                ':notas'   => $datos['notas'] !== '' ? $datos['notas'] : null,
            ]);
            guardar_detalle($pdo, (int) $pdo->lastInsertId(), $datos['servicios']);
            $pdo->commit();

            header('Location: index.php?status=created');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($e->getCode() === '23000') {   // violación de UNIQUE (fecha_hora)
                $errores['fecha_hora'] = 'Ese horario ya está ocupado. Elegí otro.';
            } else {
                error_log($e->getMessage());
                $errorGeneral = 'No pudimos guardar el turno. Intentá de nuevo.';
            }
        }
    }
}

$opciones = datos_formulario($pdo);
$titulo = 'Nuevo turno';
include __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Nuevo turno</h1>

<?php if ($errorGeneral): ?><div class="alert alert-danger"><?= h($errorGeneral) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="alert alert-danger">Revisá el formulario: hay campos con errores.</div><?php endif; ?>

<?php
$accion = 'crear.php';
$textoBoton = 'GUARDAR TURNO';
include __DIR__ . '/../includes/form_turno.php';
include __DIR__ . '/../includes/footer.php';
