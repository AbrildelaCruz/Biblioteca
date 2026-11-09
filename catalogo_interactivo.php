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
    <title>Catálogo - Biblioteca Sullivan</title>
    <!-- Tu archivo de estilos externo unificado -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
    
    <!-- 🟢 INCLUSIÓN DE LIBRERÍA CHOICES.JS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/11.2.4/choices.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/11.2.4/choices.min.js"></script>
    <style>
        /* Ajustes para el buscador autocompletado en tus tarjetas */
        .choices { margin-bottom: 0px; font-size: 13px; color: #1e293b; }
        .choices__inner { background-color: white; border-radius: 4px; border: 1px solid #CBD5E1; padding: 4px; min-height: 36px; }
        .choices__list--dropdown { z-index: 1000 !important; }
    </style>
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

    <!-- 📊 NUEVO PANEL DE ANALÍTICA Y TENDENCIAS DE LECTURA -->
    <div class="contenedor-tendencias">
        <h3 style="margin: 0; color: #0F4C81; text-align: center; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px;">
            📈 Libros Más Leídos (Monitoreo de Tendencias)
        </h3>
        
        <div class="seccion-bloques-tendencias">
            
            <!-- 🗓️ BLOQUE 1: LOS MÁS LEÍDOS DE LA SEMANA -->
            <div class="bloque-tiempo">
                <h4>🔥 Esta Semana</h4>
                <?php
                $sql_semana = "SELECT l.Titulo, COUNT(p.id_prestamo) AS Total 
                               FROM prestamos p 
                               JOIN libros l ON p.id_libro = l.id_libro 
                               WHERE p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                               GROUP BY p.id_libro 
                               ORDER BY Total DESC LIMIT 3";
                $res_semana = $conexion->query($sql_semana);
                if ($res_semana && $res_semana->num_rows > 0) {
                    while($row = $res_semana->fetch_assoc()) {
                        echo "<div class='item-tendencia'><span>📚 ".htmlspecialchars($row['Titulo'])."</span><span class='badge-conteo'>".$row['Total']." prestamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin movimientos esta semana.</p>";
                }
                ?>
            </div>

            <!-- 🗓️ BLOQUE 2: TENDENCIAS DE LAS ÚLTIMAS 2 SEMANAS -->
            <div class="bloque-tiempo">
                <h4>📊 Últimas 2 Semanas</h4>
                <?php
                $sql_quincena = "SELECT l.Titulo, COUNT(p.id_prestamo) AS Total 
                                 FROM prestamos p 
                                 JOIN libros l ON p.id_libro = l.id_libro 
                                 WHERE p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                                 GROUP BY p.id_libro 
                                 ORDER BY Total DESC LIMIT 3";
                $res_quincena = $conexion->query($sql_quincena);
                if ($res_quincena && $res_quincena->num_rows > 0) {
                    while($row = $res_quincena->fetch_assoc()) {
                        echo "<div class='item-tendencia'><span>📖 ".htmlspecialchars($row['Titulo'])."</span><span class='badge-conteo' style='background-color:#0F4C81;'>".$row['Total']." prestamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin lecturas registradas.</p>";
                }
                ?>
            </div>

            <!-- 🗓️ BLOQUE 3: LO MÁS SOLICITADO DEL MES -->
            <div class="bloque-tiempo">
                <h4>🏆 Histórico del Mes</h4>
                <?php
                $sql_mes = "SELECT l.Titulo, COUNT(p.id_prestamo) AS Total 
                            FROM prestamos p 
                            JOIN libros l ON p.id_libro = l.id_libro 
                            WHERE p.fecha_salida >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                            GROUP BY p.id_libro 
                            ORDER BY Total DESC LIMIT 3";
                $res_mes = $conexion->query($sql_mes);
                if ($res_mes && $res_mes->num_rows > 0) {
                    while($row = $res_mes->fetch_assoc()) {
                        echo "<div class='item-tendencia'><span>👑 <b>".htmlspecialchars($row['Titulo'])."</b></span><span class='badge-conteo' style='background-color:#4a148c;'>".$row['Total']." prestamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin registros este mes.</p>";
                }
                ?>
            </div>

        </div>
    </div>

    <h2 style="color: #0F4C81; text-align: center; margin-top: 20px;">📚 Catálogo Digital Interactivo</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 20px;">Busca un libro por su título y procésalo al instante.</p>

    <div class="contenedor-busqueda">
    <input type="text" id="buscar-libro" class="input-busqueda" placeholder="Buscar por título, autor o materia..." onkeyup="filtrarCatalogo()">
    
    <!-- El letrero de error ahora se mostrará aquí de manera elegante, no como una ficha abajo -->
    <div id="mensaje-vacio" style="display: none; color: #991B1B; background-color: #FEE2E2; padding: 10px; margin-top: 10px; border-radius: 8px; font-weight: bold; font-size: 14px;">
        ❌ No se encontraron libros que coincidan con la búsqueda.
    </div>
</div>

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
<div id="wrapper_alumno_<?php echo $libro['id_libro']; ?>" style="display:none;">
    <select name="id_alumno" id="select_alumno_<?php echo $libro['id_libro']; ?>" class="buscador-autocompletado">
        <option value="">Escribe el nombre del Alumno...</option>
        <?php
        $alumnos_res->data_seek(0);
        while ($al = $alumnos_res->fetch_assoc()) {
            echo "<option value='{$al['id_Alumnos']}'>{$al['Nombre']}</option>";
        }
        ?>
    </select>
</div>

<!-- Selector de Profesores -->
<div id="wrapper_profesor_<?php echo $libro['id_libro']; ?>" style="display:none;">
    <select name="id_profesor" id="select_profesor_<?php echo $libro['id_libro']; ?>" class="buscador-autocompletado" disabled>
        <option value="">Escribe el nombre del Docente...</option>
        <?php
        $profesores_res->data_seek(0);
        while ($prof = $profesores_res->fetch_assoc()) {
            echo "<option value='{$prof['id_profesores']}'>{$prof['Nombre']}</option>";
        }
        ?>
    </select>
</div>
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
     <!-- 🟢 SCRIPTS INTERACTIVOS (Buscadores Autocompletados y Motor de Filtrado) -->
    <script>
        // 1. ALMACÉN GLOBAL PARA CHOICES.JS
const instanciasChoices = {};

function inicializarBuscadores() {
    const selectores = document.querySelectorAll('.buscador-autocompletado');
    selectores.forEach(select => {
        // Evitamos duplicar inicializaciones erróneas
        if (!instanciasChoices[select.id]) {
            instanciasChoices[select.id] = new Choices(select, {
                searchEnabled: true,
                noResultsText: 'No se encontraron coincidencias',
                noChoicesText: 'No hay registros cargados',
                itemSelectText: 'Clic para seleccionar',
                placeholder: true,
                searchPlaceholderValue: 'Escribe para buscar...',
                shouldSort: false // Mantiene el orden alfabético original de tu SQL
            });
        }
    });
}

// Forzamos el arranque en cuanto el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarBuscadores);
} else {
    inicializarBuscadores();
}
        // 2. MOTOR DE FILTRADO DE TARJETAS EN TIEMPO REAL (Tu buscador principal de libros)
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

        // 3. LÓGICA DE SELECTORES DINÁMICOS ADAPTADA A CHOICES.JS
        // Esta función controla qué buscador se muestra (Alumno o Profesor) según la elección
        function alternarUsuarios(selector, idLibro) {
            var wrapperAlumno = document.getElementById('wrapper_alumno_' + idLibro);
            var wrapperProfesor = document.getElementById('wrapper_profesor_' + idLibro);
            var selectAlumno = document.getElementById('select_alumno_' + idLibro);
            var selectProfesor = document.getElementById('select_profesor_' + idLibro);

            if (selector.value === 'alumno') {
                // Muestra el buscador de Alumnos
                wrapperAlumno.style.display = 'block';
                selectAlumno.disabled = false;
                selectAlumno.required = true;
                if (instanciasChoices['select_alumno_' + idLibro]) instanciasChoices['select_alumno_' + idLibro].enable();

                // Oculta y limpia el buscador de Profesores
                wrapperProfesor.style.display = 'none';
                selectProfesor.disabled = true;
                selectProfesor.required = false;
                if (instanciasChoices['select_profesor_' + idLibro]) {
                    instanciasChoices['select_profesor_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_profesor_' + idLibro].disable();
                }

            } else if (selector.value === 'profesor') {
                // Muestra el buscador de Profesores
                wrapperProfesor.style.display = 'block';
                selectProfesor.disabled = false;
                selectProfesor.required = true;
                if (instanciasChoices['select_profesor_' + idLibro]) instanciasChoices['select_profesor_' + idLibro].enable();

                // Oculta y limpia el buscador de Alumnos
                wrapperAlumno.style.display = 'none';
                selectAlumno.disabled = true;
                selectAlumno.required = false;
                if (instanciasChoices['select_alumno_' + idLibro]) {
                    instanciasChoices['select_alumno_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_alumno_' + idLibro].disable();
                }

            } else {
                // Si selecciona la opción por defecto "¿Quién solicita?", oculta y limpia ambos
                wrapperAlumno.style.display = 'none';
                selectAlumno.disabled = true;
                if (instanciasChoices['select_alumno_' + idLibro]) {
                    instanciasChoices['select_alumno_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_alumno_' + idLibro].disable();
                }

                wrapperProfesor.style.display = 'none';
                selectProfesor.disabled = true;
                if (instanciasChoices['select_profesor_' + idLibro]) {
                    instanciasChoices['select_profesor_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_profesor_' + idLibro].disable();
                }
            }
        }
    </script>
     <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>
<script>
function filtrarCatalogo() {
    const input = document.getElementById('buscar-libro');
    const textoBuscado = input.value.toLowerCase().trim();
    
    const tarjetas = document.querySelectorAll('.tarjeta-libro');
    const mensajeVacio = document.getElementById('mensaje-vacio');
    let coincidencias = 0;

    tarjetas.forEach(tarjeta => {
        const infoLibro = tarjeta.querySelector('.info-libro');
        const contenidoTarjeta = infoLibro ? infoLibro.textContent.toLowerCase() : '';

        // Forzamos estilos seguros directamente en cada tarjeta para no romper el CSS general
        tarjeta.style.setProperty('max-width', '340px', 'important');
        tarjeta.style.setProperty('margin', '15px', 'important');
        tarjeta.style.setProperty('vertical-align', 'top', 'important');

        if (textoBuscado === "") {
            // Si el buscador está vacío, las acomoda una al lado de la otra en horizontal
            tarjeta.style.setProperty('display', 'inline-block', 'important');
            coincidencias++;
        } else if (contenidoTarjeta.includes(textoBuscado)) {
            // Si hay coincidencia, se queda visible en horizontal
            tarjeta.style.setProperty('display', 'inline-block', 'important');
            coincidencias++;
        } else {
            // Si no coincide, se oculta por completo
            tarjeta.style.setProperty('display', 'none', 'important');
        }
    });

    // Control del mensaje de error abajo del buscador
    if (mensajeVacio) {
        if (coincidencias === 0 && textoBuscado !== '') {
            mensajeVacio.style.setProperty('display', 'block', 'important');
        } else {
            mensajeVacio.style.setProperty('display', 'none', 'important');
        }
    }
}

// Ejecutamos la función una vez al cargar la página para acomodar los cuadros desde el inicio
window.onload = function() {
    filtrarCatalogo();
};
</script>

</body>
</html>