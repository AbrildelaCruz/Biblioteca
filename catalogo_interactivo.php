<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

// 1. OBTENER ALUMNOS Y PROFESORES PARA LOS SELECTORES DESPLEGABLES
$alumnos_res = $conexion->query("SELECT id_Alumnos, Nombre FROM alumnos ORDER BY Nombre ASC");
$profesores_res = $conexion->query("SELECT id_profesores, Nombre FROM profesores ORDER BY Nombre ASC");

// 2. CONSULTAR LOS LIBROS PARA LA SECCIÓN INTERACTIVA
$libros_res = $conexion->query("SELECT id_libro, Titulo, Autor, Editorial, Ejemplares_disponibles, Ejemplares_prestados FROM libros ORDER BY Titulo ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Interactivo - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        /* Estilos específicos para la sección interactiva de tarjetas */
        .grid-catalogo {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }
        .tarjeta-libro {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(15, 76, 129, 0.08);
            border: 1px solid #DDE6ED;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .info-libro h3 {
            color: #0F4C81;
            margin: 0 0 8px 0;
            font-size: 18px;
        }
        .info-libro p {
            margin: 4px 0;
            font-size: 14px;
            color: #475569;
        }
        .semaforo {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .disponible { background-color: #D1FAE5; color: #065F46; }
        .agotado { background-color: #FEE2E2; color: #991B1B; }
        
        .formulario-prestamo {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #E2E8F0;
        }
    </style>
</head>
<body class="body2">

    <!-- ALERTAS DEL SISTEMA -->
    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>" style="padding: 12px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="color: #0F4C81; text-align: center; margin-top: 20px;">📚 Catálogo Digital Interactivo</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 30px;">Selecciona un libro disponible para realizar un préstamo o apartado inmediato.</p>

    <!-- CUADRÍCULA DE TARJETAS VISUALES -->
    <div class="grid-catalogo">
        <?php if ($libros_res && $libros_res->num_rows > 0): ?>
            <?php while($libro = $libros_res->fetch_assoc()): 
                $es_disponible = ($libro['Ejemplares_disponibles'] > 0);
            ?>
                <div class="tarjeta-libro">
                    <div class="info-libro">
                        <h3><?php echo htmlspecialchars($libro['Titulo']); ?></h3>
                        <p><b>Autor:</b> <?php echo htmlspecialchars($libro['Autor']); ?></p>
                        <p><b>Editorial:</b> <?php echo htmlspecialchars($libro['Editorial']); ?></p>
                        <p><b>En Estantería:</b> <?php echo $libro['Ejemplares_disponibles'] ?? 0; ?> copias</p>
                        
                        <!-- SEMÁFORO VISUAL -->
                        <?php if ($es_disponible): ?>
                            <span class="semaforo disponible">🟢 Disponible</span>
                        <?php else: ?>
                            <span class="semaforo agotado">🔴 Todo Prestado / Apartado</span>
                        <?php endif; ?>
                    </div>

                    <!-- FORMULARIO DE ACCIÓN DIRECTA -->
                    <?php if ($es_disponible): ?>
                        <div class="formulario-prestamo">
                            <!-- Envia los datos al nuevo archivo procesar_movimiento.php -->
                            <form action="procesar_movimiento.php" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                                
                                <select name="tipo_usuario" required onchange="alternarUsuarios(this, <?php echo $libro['id_libro']; ?>)">
                                    <option value="">¿Quién solicita?</option>
                                    <option value="alumno">Alumno</option>
                                    <option value="profesor">Profesor</option>
                                </select>

                                <!-- Selector de Alumnos (Se muestra dinámicamente) -->
                                <select name="id_usuario" id="select_alumno_<?php echo $libro['id_libro']; ?>" style="display:none;">
                                    <option value="">Selecciona el Alumno...</option>
                                    <?php 
                                    $alumnos_res->data_seek(0);
                                    while($al = $alumnos_res->fetch_assoc()) {
                                        echo "<option value='{$al['id_Alumnos']}'>{$al['Nombre']}</option>";
                                    }
                                    ?>
                                </select>

                                <!-- Selector de Profesores (Se muestra dinámicamente) -->
                                <select name="id_usuario" id="select_profesor_<?php echo $libro['id_libro']; ?>" style="display:none;" disabled>
                                    <option value="">Selecciona el Docente...</option>
                                    <?php 
                                    $profesores_res->data_seek(0);
                                    while($prof = $profesores_res->fetch_assoc()) {
                                        echo "<option value='{$prof['id_profesores']}'>{$prof['Nombre']}</option>";
                                    }
                                    ?>
                                </select>

                                <div style="display: flex; gap: 8px; margin-top: 5px;">
                                    <button type="submit" name="accion" value="prestar" style="flex: 1; background-color: #2A9D8F; color: white; border: none; padding: 8px; border-radius: 4px; font-weight: bold; cursor: pointer;">Prestar</button>
                                    <button type="submit" name="accion" value="apartar" style="flex: 1; background-color: #E2E8F0; color: #1E293B; border: 1px solid #CBD5E1; padding: 8px; border-radius: 4px; font-weight: bold; cursor: pointer;">Apartar</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1;">No hay libros registrados en el inventario.</p>
        <?php endif; ?>
    </div>

    <div class="z1">
        <a class="z2" href="registro.html">REGRESAR</a>
    </div>

    <!-- SCRIPT INTERACTIVO PARA ALTERNAR SELECTORES -->
    <script>
    function alternarUsuarios(selector, idLibro) {
        var selectAlumno = document.getElementById('select_alumno_' + idLibro);
        var selectProfesor = document.getElementById('select_profesor_' + idLibro);
        
        if (selector.value === 'alumno') {
            selectAlumno.style.display = 'block';
            selectAlumno.disabled = false;
            selectAlumno.required = true;
            
            selectProfesor.style.display = 'none';
            selectProfesor.disabled = true;
            selectProfesor.required = false;
        } else if (selector.value === 'profesor') {
            selectProfesor.style.display = 'block';
            selectProfesor.disabled = false;
            selectProfesor.required = true;
            
            selectAlumno.style.display = 'none';
            selectAlumno.disabled = true;
            selectAlumno.required = false;
        } else {
            selectAlumno.style.display = 'none';
            selectAlumno.disabled = true;
            selectProfesor.style.display = 'none';
            selectProfesor.disabled = true;
        }
    }
    </script>
</body>
</html>