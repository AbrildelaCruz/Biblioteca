<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarnos de que los datos vengan por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_usuario = trim($_POST['tipo_usuario'] ?? '');
    $id_usuario   = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
    $id_libro     = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $accion       = trim($_POST['accion'] ?? 'prestar');
    
    // Configurar fechas automáticas (Hoy y la fecha límite)
    date_default_timezone_set('America/Mexico_City');
    $fecha_salida = date('Y-m-d');
    
    // Si es préstamo damos 9 días; si es apartado damos 2 días (48 horas)
    $dias_limite  = ($accion === 'apartar') ? 2 : 9;
    $fecha_esperada = date('Y-m-d', strtotime("+$dias_limite days"));

    // Validaciones iniciales de seguridad
    if (empty($tipo_usuario) || !$id_usuario || !$id_libro) {
        $_SESSION['mensaje'] = '⚠️ Error: No se seleccionó correctamente al usuario o al libro.';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: nuevo_prestamo.php");
        exit();
    }

    // 1. PASO CRÍTICO: Verificar si el libro tiene copias disponibles en la estantería
    $stmt_stock = $conexion->prepare("SELECT Ejemplares_disponibles FROM libros WHERE id_libro = ?");
    $stmt_stock->bind_param("i", $id_libro);
    $stmt_stock->execute();
    $res_stock = $stmt_stock->get_result()->fetch_assoc();
    $stmt_stock->close();

    if (!$res_stock || $res_stock['Ejemplares_disponibles'] <= 0) {
        $_SESSION['mensaje'] = '❌ No se puede procesar: Este libro ya no cuenta con existencias disponibles en la estantería.';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: nuevo_prestamo.php");
        exit();
    }

    // 2. INICIAR TRANSACCIÓN SEGURA EN MYSQL
    $conexion->begin_transaction();

    try {
        // Inicializar variables para mapear si es alumno o profesor
        $id_alumno = ($tipo_usuario === 'alumno') ? $id_usuario : null;
        $id_profesor = ($tipo_usuario === 'profesor') ? $id_usuario : null;
        $movimiento_texto = ($accion === 'apartar') ? 'Apartado' : 'Prestado';

        // Insertar la transacción en la tabla unificada de préstamos
        $stmt_ins = $conexion->prepare("INSERT INTO prestamos (id_libro, id_alumnos, id_profesores, Cantidad, fecha_salida, fecha_devolucion_esperada, tipo_movimiento, Renovacion) VALUES (?, ?, ?, 1, ?, ?, ?, 0)");
        $stmt_ins->bind_param("iiissss", $id_libro, $id_alumno, $id_profesor, $fecha_salida, $fecha_esperada, $movimiento_texto);
        $stmt_ins->execute();
        $stmt_ins->close();

        // 3. EFECTO INVENTARIO DINÁMICO: Restamos 1 a disponibles y sumamos 1 a prestados
        $stmt_upd = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles - 1, Ejemplares_prestados = Ejemplares_prestados + 1 WHERE id_libro = ?");
        $stmt_upd->bind_param("i", $id_libro);
        $stmt_upd->execute();
        $stmt_upd->close();

        // Si todo sale bien, confirmamos los cambios en MariaDB
        $conexion->commit();

        $_SESSION['mensaje'] = "🎉 ¡Movimiento registrado con éxito! El inventario ha sido actualizado automáticamente.";
        $_SESSION['tipoMensaje'] = 'exito';

    } catch (Exception $e) {
        // Si algo llega a fallar, revertimos todo para no corromper tus tablas
        $conexion->rollback();
        $_SESSION['mensaje'] = 'Error grave en el servidor: ' . $e->getMessage();
        $_SESSION['tipoMensaje'] = 'error';
    }

    // Redirección limpia al formulario express
    header("Location: nuevo_prestamo.php");
    exit();
}
?>