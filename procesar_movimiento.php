<?php
require_once 'conexion.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_usuario = trim($_POST['tipo_usuario'] ?? '');
    $id_libro     = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $accion       = trim($_POST['accion'] ?? 'prestar');
    $id_usuario   = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT)
                    ?: (($tipo_usuario === 'alumno') ? filter_input(INPUT_POST, 'id_alumno', FILTER_VALIDATE_INT) : filter_input(INPUT_POST, 'id_profesor', FILTER_VALIDATE_INT));

    date_default_timezone_set('America/Mexico_City');
    $fecha_salida = date('Y-m-d');
    $dias_limite  = ($accion === 'apartar') ? 2 : 9;
    $fecha_esperada = date('Y-m-d', strtotime("+$dias_limite days"));

    if (empty($tipo_usuario) || !$id_usuario || !$id_libro) {
        $_SESSION['mensaje'] = '⚠️ Selecciona usuario y libro antes de registrar.';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: nuevo_prestamo.php");
        exit();
    }

    $stmt_stock = $conexion->prepare("SELECT Ejemplares_disponibles FROM libros WHERE id_libro = ?");
    $stmt_stock->bind_param("i", $id_libro);
    $stmt_stock->execute();
    $res_stock = $stmt_stock->get_result()->fetch_assoc();
    $stmt_stock->close();

    if (!$res_stock || $res_stock['Ejemplares_disponibles'] <= 0) {
        $_SESSION['mensaje'] = '❌ Sin existencias disponibles.';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: catalogo_interactivo.php");
        exit();
    }

    $conexion->begin_transaction();
    try {
        $id_alumno = ($tipo_usuario === 'alumno') ? $id_usuario : null;
        $id_profesor = ($tipo_usuario === 'profesor') ? $id_usuario : null;
        $movimiento_texto = ($accion === 'apartar') ? 'Apartado' : 'Prestado';

        $stmt_ins = $conexion->prepare("INSERT INTO prestamos (id_libro, id_alumnos, id_profesores, Cantidad, fecha_salida, fecha_devolucion_esperada, tipo_movimiento, Renovacion) VALUES (?, ?, ?, 1, ?, ?, ?, 0)");
        $stmt_ins->bind_param("iiisss", $id_libro, $id_alumno, $id_profesor, $fecha_salida, $fecha_esperada, $movimiento_texto);
        $stmt_ins->execute();
        $stmt_ins->close();

        $stmt_upd = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles - 1, Ejemplares_prestados = Ejemplares_prestados + 1 WHERE id_libro = ?");
        $stmt_upd->bind_param("i", $id_libro);
        $stmt_upd->execute();
        $stmt_upd->close();

        $conexion->commit();
        $_SESSION['mensaje'] = "🎉 ¡Movimiento registrado con éxito!";
        $_SESSION['tipoMensaje'] = 'exito';
    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
        $_SESSION['tipoMensaje'] = 'error';
    }
    header("Location: prestamos.php");
    exit();
}
?>