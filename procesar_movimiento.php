<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_libro = filter_input(INPUT_POST, 'id_libro', FILTER_VALIDATE_INT);
    $tipo_usuario = $_POST['tipo_usuario'] ?? ''; // 'alumno' o 'profesor'
    $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
    $accion = $_POST['accion'] ?? ''; // 'prestar' o 'apartar'

    if (!$id_libro || !$id_usuario || !in_array($accion, ['prestar', 'apartar'])) {
        $_SESSION['mensaje'] = 'Faltan datos obligatorios para procesar el movimiento.';
        $_SESSION['tipoMensaje'] = 'error';
        header("Location: ver_catalogo.php");
        exit();
    }

    $id_alumnos = ($tipo_usuario === 'alumno') ? $id_usuario : null;
    $id_profesores = ($tipo_usuario === 'profesor') ? $id_usuario : null;

    try {
        // 1. Validar existencias del libro
        $stmt_lib = $conexion->prepare("SELECT Titulo, Ejemplares_disponibles FROM libros WHERE id_libro = ?");
        $stmt_lib->bind_param("i", $id_libro);
        $stmt_lib->execute();
        $resultado_lib = $stmt_lib->get_result();
        $libro = $resultado_lib->fetch_assoc();
        $stmt_lib->close();

        if (!$libro) {
            throw new Exception("El libro seleccionado no existe.");
        }

        if ($libro['Ejemplares_disponibles'] <= 0) {
            $_SESSION['mensaje'] = "No hay ejemplares disponibles de '{$libro['Titulo']}' en este momento.";
            $_SESSION['tipoMensaje'] = 'error';
            header("Location: ver_catalogo.php");
            exit();
        }

        // 2. Definir plazos y el tipo de movimiento exacto
        $fecha_salida = date('Y-m-d');
        if ($accion === 'apartar') {
            $fecha_esperada = date('Y-m-d', strtotime('+2 days'));
            $tipo_movimiento = 'Apartado'; 
            $alerta_texto = "🟡 NOTIFICACIÓN: El libro '{$libro['Titulo']}' ha sido APARTADO. Guardar hasta el $fecha_esperada.";
        } else {
            $fecha_esperada = date('Y-m-d', strtotime('+9 days'));
            $tipo_movimiento = 'Prestado';
            $alerta_texto = "🔴 NOTIFICACIÓN: Préstamo registrado para '{$libro['Titulo']}'. Fecha límite: $fecha_esperada.";
        }

        // 3. Insertar en tu tabla 'prestamos' incluyendo la nueva columna
        $stmt_ins = $conexion->prepare("INSERT INTO prestamos (id_alumnos, id_profesores, id_libro, Cantidad, tipo_movimiento, fecha_salida, fecha_devolucion_esperada, Renovacion) VALUES (?, ?, ?, 1, ?, ?, ?, 'No')");
        $stmt_ins->bind_param("iiissss", $id_alumnos, $id_profesores, $id_libro, $tipo_movimiento, $fecha_salida, $fecha_esperada);
        
        if ($stmt_ins->execute()) {
            // 4. Actualizar contadores en la tabla 'libros'
            $stmt_upd = $conexion->prepare("UPDATE libros SET Ejemplares_disponibles = Ejemplares_disponibles - 1, Ejemplares_prestados = Ejemplares_prestados + 1 WHERE id_libro = ?");
            $stmt_upd->bind_param("i", $id_libro);
            $stmt_upd->execute();
            $stmt_upd->close();

            $_SESSION['mensaje'] = $alerta_texto;
            $_SESSION['tipoMensaje'] = 'exito';
        } else {
            throw new Exception("Error al insertar el movimiento.");
        }
        $stmt_ins->close();

    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
        $_SESSION['tipoMensaje'] = 'error';
    }

    header("Location: catalogo_interactivo.php");
    exit();
}
?>