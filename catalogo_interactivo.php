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
    <!-- Tu archivo de estilos externo unificado -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body class="body2">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <!-- ALERTAS DEL SISTEMA -->
    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 12px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2 style="color: #0F4C81; text-align: center; margin-top: 20px;">📚 Catálogo Digital Interactivo</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">Busca un libro por su título y procésalo al
        instante.</p>

    <!-- BARRA DE BÚSQUEDA DINÁMICA -->
    <div class="contenedor-busqueda">
        <input type="text" id="buscador" class="input-busqueda"
            placeholder="🔍 Escribe el título del libro para filtrar...">
    </div>

    <!-- CUADRÍCULA DE TARJETAS VISUALES -->
    <div class="grid-catalogo" id="lista-libros">
        <div id="mensaje-vacio">❌ No se encontraron libros que coincidan con la búsqueda.</div>

        <?php if ($libros_res && $libros_res->num_rows > 0): ?>
            <?php while ($libro = $libros_res->fetch_assoc()):
                $es_disponible = ($libro['Ejemplares_disponibles'] > 0);
                ?>
                <div class="tarjeta-libro">
                    <div class="info-libro">
                        <h3 class="titulo-libro"><?php echo htmlspecialchars($libro['Titulo']); ?></h3>
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
                            <form action="procesar_movimiento.php" method="POST"
                                style="display: flex; flex-direction: column; gap: 8px;">
                                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">

                                <select name="tipo_usuario" required
                                    onchange="alternarUsuarios(this, <?php echo $libro['id_libro']; ?>)">
                                    <option value="">¿Quién solicita?</option>
                                    <option value="alumno">Alumno</option>
                                    <option value="profesor">Profesor</option>
                                </select>

                                <!-- Selector de Alumnos -->
                                <select name="id_usuario" id="select_alumno_<?php echo $libro['id_libro']; ?>"
                                    style="display:none;">
                                    <option value="">Selecciona el Alumno...</option>
                                    <?php
                                    $alumnos_res->data_seek(0);
                                    while ($al = $alumnos_res->fetch_assoc()) {
                                        echo "<option value='{$al['id_Alumnos']}'>{$al['Nombre']}</option>";
                                    }
                                    ?>
                                </select>

                                <!-- Selector de Profesores -->
                                <select name="id_usuario" id="select_profesor_<?php echo $libro['id_libro']; ?>"
                                    style="display:none;" disabled>
                                    <option value="">Selecciona el Docente...</option>
                                    <?php
                                    $profesores_res->data_seek(0);
                                    while ($prof = $profesores_res->fetch_assoc()) {
                                        echo "<option value='{$prof['id_profesores']}'>{$prof['Nombre']}</option>";
                                    }
                                    ?>
                                </select>

                                <div style="display: flex; gap: 8px; margin-top: 5px;">
                                    <button type="submit" name="accion" value="prestar"
                                        style="flex: 1; background-color: #2A9D8F; color: white; border: none; padding: 8px; border-radius: 4px; font-weight: bold; cursor: pointer;">Prestar</button>
                                    <button type="submit" name="accion" value="apartar"
                                        style="flex: 1; background-color: #E2E8F0; color: #1E293B; border: 1px solid #CBD5E1; padding: 8px; border-radius: 4px; font-weight: bold; cursor: pointer;">Apartar</button>
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
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <!-- SCRIPTS INTERACTIVOS (Alternar selectores y Motor de búsqueda) -->
    <script>
        // 1. MOTOR DE FILTRADO EN TIEMPO REAL
        document.getElementById('buscador').addEventListener('input', function () {
            var filtro = this.value.toLowerCase().trim();
            var tarjetas = document.getElementsByClassName('tarjeta-libro');
            var mensajeVacio = document.getElementById('mensaje-vacio');
            var encontrados = 0;

            for (var i = 0; i < tarjetas.length; i++) {
                var tituloElemento = tarjetas[i].querySelector('.titulo-libro');
                if (tituloElemento) {
                    var textoTitulo = tituloElemento.textContent.toLowerCase();

                    if (textoTitulo.includes(filtro)) {
                        tarjetas[i].style.display = 'flex';
                        encontrados++;
                    } else {
                        tarjetas[i].style.display = 'none';
                    }
                }
            }

            if (encontrados === 0 && filtro !== "") {
                mensajeVacio.style.display = 'block';
            } else {
                mensajeVacio.style.display = 'none';
            }
        });

        // 2. LÓGICA DE SELECTORES DINÁMICOS
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