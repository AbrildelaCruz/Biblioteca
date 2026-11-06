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
    
    <!-- 🟢 INCLUSIÓN DE CHOICES.JS PARA CONVERTIR SELECTS EN BUSCADORES -->
    <link rel="stylesheet" href="https://jsdelivr.net" />
    <script src="https://jsdelivr.net"></script>
    <style>
        /* Ajuste estético para que Choices.js combine con tu diseño compacto */
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

                                <!-- Selector de Alumnos (Encapsulado en un div contenedor para controlar Choices.js) -->
                                <div id="wrapper_alumno_<?php echo $libro['id_libro']; ?>" style="display:none;">
                                    <select name="id_usuario" id="select_alumno_<?php echo $libro['id_libro']; ?>" class="buscador-autocompletado">
                                        <option value="">Escribe el nombre del Alumno...</option>
                                        <?php
                                        $alumnos_res->data_seek(0);
                                        while ($al = $alumnos_res->fetch_assoc()) {
                                            echo "<option value='{$al['id_Alumnos']}'>{$al['Nombre']}</option>";
}
?>
<?php
Escribe el nombre del Docente...


Prestar
Apartar







No hay libros registrados en el inventario.

REGRESAR
// Almacén para guardar las instancias activas de Choices.js de cada tarjeta
const instanciasChoices = {};
document.addEventListener('DOMContentLoaded', function () {
// Inicializar Choices.js en todas las listas desplegables de Alumnos y Profesores
const selectores = document.querySelectorAll('.buscador-autocompletado');
selectores.forEach(select => {
instanciasChoices[select.id] = new Choices(select, {
searchEnabled: true,
noResultsText: 'No se encontraron coincidencias',
noChoicesText: 'No hay registros cargados',
itemSelectText: 'Clic para seleccionar',
placeholder: true,
searchPlaceholderValue: 'Escribe para buscar...'
});
});
});
// 1. MOTOR DE FILTRADO DE LIBROS EN TIEMPO REAL
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
// 2. LÓGICA DE SELECTORES DINÁMICOS CORREGIDA PARA CHOICES.JS
function alternarUsuarios(selector, idLibro) {
var wrapperAlumno = document.getElementById('wrapper_alumno_' + idLibro);
var wrapperProfesor = document.getElementById('wrapper_profesor_' + idLibro);
var selectAlumno = document.getElementById('select_alumno_' + idLibro);
var selectProfesor = document.getElementById('select_profesor_' + idLibro);
if (selector.value === 'alumno') {
// Mostrar Alumno
wrapperAlumno.style.display = 'block';
selectAlumno.disabled = false;
selectAlumno.required = true;
if (instanciasChoices['select_alumno_' + idLibro]) instanciasChoices['select_alumno_' + idLibro].enable();
// Ocultar y resetear Profesor
wrapperProfesor.style.display = 'none';
selectProfesor.disabled = true;
selectProfesor.required = false;
if (instanciasChoices['select_profesor_' + idLibro]) {
instanciasChoices['select_profesor_' + idLibro].setChoiceByValue('');
instanciasChoices['select_profesor_' + idLibro].disable();
}
} else if (selector.value === 'profesor') {
// Mostrar Profesor
wrapperProfesor.style.display = 'block';
selectProfesor.disabled = false;
selectProfesor.required = true;
if (instanciasChoices['select_profesor_' + idLibro]) instanciasChoices['select_profesor_' + idLibro].enable();
// Ocultar y resetear Alumno
wrapperAlumno.style.display = 'none';
selectAlumno.disabled = true;
selectAlumno.required = false;
if (instanciasChoices['select_alumno_' + idLibro]) {
instanciasChoices['select_alumno_' + idLibro].setChoiceByValue('');
instanciasChoices['select_alumno_' + idLibro].disable();
}
} else {
// Ocultar ambos si no se ha seleccionado tipo de usuario
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
?>