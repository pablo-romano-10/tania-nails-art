<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

/* ---------- 1. Obtener el id (por GET al entrar, por POST al guardar) ---------- */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
   ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php?status=notfound'); exit; }

/* ---------- 2. Buscar el turno (consulta preparada) ---------- */
$stmt = $pdo->prepare('SELECT * FROM turnos WHERE id = :id');
$stmt->execute([':id' => $id]);
$turno = $stmt->fetch();
if (!$turno) { header('Location: index.php?status=notfound'); exit; }

$errores = [];
$errorGeneral = '';

/* Datos iniciales: los del registro guardado */
$q = $pdo->prepare('SELECT servicio_id FROM detalle_turno WHERE turno_id = :id');
$q->execute([':id' => $id]);
$datos = [
    'usuario_id' => (string) $turno['usuario_id'],
    'estado_id'  => (string) $turno['estado_id'],
    'fecha_hora' => date('Y-m-d\TH:i', strtotime($turno['fecha_hora'])),
    'notas'      => (string) $turno['notas'],
    'servicios'  => array_map('intval', $q->fetchAll(PDO::FETCH_COLUMN)),
];

/* ---------- 3. Si se envió el formulario, validar y actualizar ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'usuario_id' => post('usuario_id'),
        'estado_id'  => post('estado_id'),
        'fecha_hora' => post('fecha_hora'),
        'notas'      => post('notas'),
        'servicios'  => servicios_enviados(),
    ];
    $errores = validar_turno($pdo, $datos);

    if (empty($errores)) {
        try {
            $pdo->beginTransaction();
            $upd = $pdo->prepare(
                'UPDATE turnos
                 SET usuario_id = :usuario, estado_id = :estado, fecha_hora = :fecha, notas = :notas
                 WHERE id = :id'
            );
            $upd->execute([
                ':usuario' => (int) $datos['usuario_id'],
                ':estado'  => (int) $datos['estado_id'],
                ':fecha'   => str_replace('T', ' ', $datos['fecha_hora']) . ':00',
                ':notas'   => $datos['notas'] !== '' ? $datos['notas'] : null,
                ':id'      => $id,
            ]);
            // Se reemplaza el detalle: se borra el anterior y se inserta el nuevo
            $del = $pdo->prepare('DELETE FROM detalle_turno WHERE turno_id = :id');
            $del->execute([':id' => $id]);
            guardar_detalle($pdo, $id, $datos['servicios']);
            $pdo->commit();

            header('Location: index.php?status=updated');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($e->getCode() === '23000') {
                $errores['fecha_hora'] = 'Ese horario ya está ocupado. Elegí otro.';
            } else {
                error_log($e->getMessage());
                $errorGeneral = 'No pudimos actualizar el turno. Intentá de nuevo.';
            }
        }
    }
}

$opciones = datos_formulario($pdo);
$titulo = 'Editar turno';
include __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Editar turno #<?= h($id) ?></h1>

<?php if ($errorGeneral): ?><div class="alert alert-danger"><?= h($errorGeneral) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="alert alert-danger">Revisá el formulario: hay campos con errores.</div><?php endif; ?>

<?php
$accion = 'editar.php';
$textoBoton = 'GUARDAR CAMBIOS';
$id_oculto = $id;   // viaja en un campo oculto para el POST
include __DIR__ . '/../includes/form_turno.php';
include __DIR__ . '/../includes/footer.php';
