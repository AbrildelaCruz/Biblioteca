<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$alumnos_res = $conexion->query("SELECT id_Alumnos, Matricula, Nombre FROM alumnos ORDER BY Nombre ASC");
$profesores_res = $conexion->query("SELECT id_profesores, Matricula, Nombre FROM profesores ORDER BY Nombre ASC");
$libros_res = $conexion->query("SELECT id_libro, Titulo, Autor, Categoria, Editorial, Ejemplares_disponibles, Ejemplares_prestados FROM libros ORDER BY Titulo ASC");

$categorias_disponibles = [
    'Novela y Narrativa',
    'Cuento y Relato',
    'Poesía',
    'Teatro y Dramaturgia',
    'Historia y Biografía',
    'Filosofía y Ciencia',
    'Aventuras y Juvenil'
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo Interactivo - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">

    <link rel="stylesheet" href="https://jsdelivr.net" />
    <script src="https://jsdelivr.net"></script>
    
    <style>
        .choices { margin-bottom: 0px; font-size: 13px; color: #1e293b; }
        .choices__inner { background-color: white; border-radius: 4px; border: 1px solid #CBD5E1; padding: 4px; min-height: 36px; }
        .choices__list--dropdown { z-index: 1000 !important; }
        .barra-categorias {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            max-width: 950px;
            margin: 0 auto 20px auto;
            padding: 0 15px;
        }
        .btn-cat {
            padding: 7px 14px;
            border-radius: 20px;
            border: 1px solid #CBD5E1;
            background-color: #FFFFFF;
            color: #0F4C81;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cat.activa, .btn-cat:hover {
            background-color: #0F4C81;
            color: #FFFFFF;
            border-color: #0F4C81;
        }
    </style>
</head>

<body class="body2 scroll-catalogo">

    <div class="contenedor-burbujas">
        <div class="burbuja"></div>
        <div class="burbuja"></div>
        <div class="burbuja"></div>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="alerta <?php echo $tipoMensaje; ?>"
            style="padding: 12px; margin: 15px auto; max-width: 850px; text-align: center; border-radius: 5px; font-weight: bold; background-color: <?php echo $tipoMensaje === 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipoMensaje === 'exito' ? '#155724' : '#721c24'; ?>;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- PANEL DE ANALÍTICA Y TENDENCIAS -->
    <div class="contenedor-tendencias">
        <h3 style="margin: 0; color: #0F4C81; text-align: center; font-size: 18px; text-transform: uppercase; letter-spacing: 0.5px;">
            📈 Libros Más Leídos 
        </h3>
        
        <div class="seccion-bloques-tendencias">
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
                        echo "<div class='item-tendencia'><span>📚 ".htmlspecialchars($row['Titulo'])."</span><span class='badge-conteo'>".$row['Total']." préstamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin movimientos esta semana.</p>";
                }
                ?>
            </div>

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
                        echo "<div class='item-tendencia'><span>📖 ".htmlspecialchars($row['Titulo'])."</span><span class='badge-conteo' style='background-color:#0F4C81;'>".$row['Total']." préstamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin lecturas registradas.</p>";
                }
                ?>
            </div>

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
                        echo "<div class='item-tendencia'><span>👑 <b>".htmlspecialchars($row['Titulo'])."</b></span><span class='badge-conteo' style='background-color:#4a148c;'>".$row['Total']." préstamos</span></div>";
                    }
                } else {
                    echo "<p style='font-style:italic; font-size:12px; color:#64748b;'>Sin registros este mes.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <h2 style="color: #0F4C81; text-align: center; margin-top: 20px;">📚 Catálogo Digital Interactivo</h2>
    <p style="text-align: center; color: #475569; margin-bottom: 15px;">Filtra por categoría o busca un libro por título o autor.</p>

    <!-- BOTONES DE CATEGORÍAS -->
    <div class="barra-categorias">
        <button type="button" class="btn-cat activa" onclick="seleccionarCategoria('', this)">📂 Todos</button>
        <?php foreach ($categorias_disponibles as $cat): ?>
            <button type="button" class="btn-cat" onclick="seleccionarCategoria('<?php echo $cat; ?>', this)">
                <?php echo $cat; ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="contenedor-busqueda">
        <input type="text" id="buscar-libro" class="input-busqueda" placeholder="Buscar por título o autor..." onkeyup="filtrarCatalogo()">
        <div id="mensaje-vacio" style="display: none; color: #991B1B; background-color: #FEE2E2; padding: 10px; margin-top: 10px; border-radius: 8px; font-weight: bold; font-size: 14px;">
            ❌ No se encontraron libros que coincidan con la búsqueda.
        </div>
    </div>

    <!-- REJILLA DE LIBROS -->
    <div class="grid-catalogo">
        <?php if ($libros_res && $libros_res->num_rows > 0): ?>
            <?php while ($libro = $libros_res->fetch_assoc()):
                $es_disponible = ($libro['Ejemplares_disponibles'] > 0);
                $cat_libro = $libro['Categoria'] ?? 'Novela y Narrativa';
                ?>
                <div class="tarjeta-libro" data-categoria="<?php echo htmlspecialchars($cat_libro); ?>">
                    <div class="info-libro">
                        <span style="display: inline-block; padding: 2px 8px; background: #EFF6FF; color: #1E40AF; border-radius: 10px; font-size: 11px; font-weight: bold; margin-bottom: 6px;">
                            <?php echo htmlspecialchars($cat_libro); ?>
                        </span>
                        <h3 class="titulo-libro"><?php echo htmlspecialchars($libro['Titulo']); ?></h3>
                        <p class="autor-libro"><b>Autor:</b> <?php echo htmlspecialchars($libro['Autor']); ?></p>
                        <p><b>Editorial:</b> <?php echo htmlspecialchars($libro['Editorial']); ?></p>
                        <p><b>En Estantería:</b> <?php echo $libro['Ejemplares_disponibles'] ?? 0; ?> copias</p>

                        <?php if ($es_disponible): ?>
                            <span class="semaforo disponible">🟢 Disponible</span>
                        <?php else: ?>
                            <span class="semaforo agotado">🔴 Todo Prestado / Apartado</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($es_disponible): ?>
                        <div class="formulario-prestamo">
                            <form action="procesar_movimiento.php" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
                                <input type="hidden" name="id_libro" value="<?php echo $libro['id_libro']; ?>">
                                <select name="tipo_usuario" required onchange="alternarUsuarios(this, <?php echo $libro['id_libro']; ?>)">
                                    <option value="">¿Quién solicita?</option>
                                    <option value="alumno">Alumno</option>
                                    <option value="profesor">Profesor</option>
                                </select>

                                <div id="wrapper_alumno_<?php echo $libro['id_libro']; ?>" style="display:none;">
                                    <select name="id_alumno" id="select_alumno_<?php echo $libro['id_libro']; ?>" class="buscador-autocompletado" disabled>
                                        <option value="">Busca por Matrícula o Nombre...</option>
                                        <?php
                                        $alumnos_res->data_seek(0);
                                        while ($al = $alumnos_res->fetch_assoc()) {
                                            $mat = htmlspecialchars($al['Matricula'] ?? '');
                                            $nom = htmlspecialchars($al['Nombre']);
                                            echo "<option value='{$al['id_Alumnos']}'>[{$mat}] {$nom}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div id="wrapper_profesor_<?php echo $libro['id_libro']; ?>" style="display:none;">
                                    <select name="id_profesor" id="select_profesor_<?php echo $libro['id_libro']; ?>" class="buscador-autocompletado" disabled>
                                        <option value="">Escribe el nombre del Docente...</option>
                                        <?php
$profesores_res->data_seek(0);
while ($prof = $profesores_res->fetch_assoc()) {
    $mat_p = htmlspecialchars($prof['Matricula'] ?? '');
    $nom_p = htmlspecialchars($prof['Nombre']);
    echo "<option value='{$prof['id_profesores']}'>[{$mat_p}] {$nom_p}</option>";
}
?>
                                    </select>
                                </div>

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

    <script>
        const instanciasChoices = {};
        let categoriaSeleccionada = '';

        function inicializarBuscadores() {
            const selectores = document.querySelectorAll('.buscador-autocompletado');
            selectores.forEach(select => {
                if (!instanciasChoices[select.id]) {
                    instanciasChoices[select.id] = new Choices(select, {
                        searchEnabled: true,
                        noResultsText: 'No se encontraron coincidencias',
                        noChoicesText: 'No hay registros cargados',
                        itemSelectText: 'Clic para seleccionar',
                        placeholder: true,
                        searchPlaceholderValue: 'Escribe matrícula o nombre...',
                        shouldSort: false 
                    });
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', inicializarBuscadores);
        } else {
            inicializarBuscadores();
        }

        function seleccionarCategoria(categoria, boton) {
            categoriaSeleccionada = categoria;
            document.querySelectorAll('.btn-cat').forEach(btn => btn.classList.remove('activa'));
            boton.classList.add('activa');
            filtrarCatalogo();
        }

        function filtrarCatalogo() {
            var filtro = document.getElementById('buscar-libro').value.toLowerCase().trim();
            var tarjetas = document.getElementsByClassName('tarjeta-libro');
            var mensajeVacio = document.getElementById('mensaje-vacio');
            var encontrados = 0;

            for (var i = 0; i < tarjetas.length; i++) {
                var tituloElemento = tarjetas[i].querySelector('.titulo-libro');
                var autorElemento = tarjetas[i].querySelector('.autor-libro');
                var catTarjeta = tarjetas[i].getAttribute('data-categoria');

                var textoTitulo = tituloElemento ? tituloElemento.textContent.toLowerCase() : '';
                var textoAutor = autorElemento ? autorElemento.textContent.toLowerCase() : '';

                var coincideTexto = (textoTitulo.includes(filtro) || textoAutor.includes(filtro));
                var coincideCat = (categoriaSeleccionada === '' || catTarjeta === categoriaSeleccionada);

                if (coincideTexto && coincideCat) {
                    tarjetas[i].style.setProperty('display', 'block', 'important');
                    encontrados++;
                } else {
                    tarjetas[i].style.setProperty('display', 'none', 'important');
                }
            }

            if (encontrados === 0) {
                mensajeVacio.style.setProperty('display', 'block', 'important');
            } else {
                mensajeVacio.style.setProperty('display', 'none', 'important');
            }
        }

        function alternarUsuarios(selector, idLibro) {
            var wrapperAlumno = document.getElementById('wrapper_alumno_' + idLibro);
            var wrapperProfesor = document.getElementById('wrapper_profesor_' + idLibro);
            var selectAlumno = document.getElementById('select_alumno_' + idLibro);
            var selectProfesor = document.getElementById('select_profesor_' + idLibro);

            if (selector.value === 'alumno') {
                wrapperAlumno.style.display = 'block';
                selectAlumno.disabled = false;
                selectAlumno.required = true;
                if (instanciasChoices['select_alumno_' + idLibro]) instanciasChoices['select_alumno_' + idLibro].enable();

                wrapperProfesor.style.display = 'none';
                selectProfesor.disabled = true;
                selectProfesor.required = false;
                if (instanciasChoices['select_profesor_' + idLibro]) {
                    instanciasChoices['select_profesor_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_profesor_' + idLibro].disable();
                }

            } else if (selector.value === 'profesor') {
                wrapperProfesor.style.display = 'block';
                selectProfesor.disabled = false;
                selectProfesor.required = true;
                if (instanciasChoices['select_profesor_' + idLibro]) instanciasChoices['select_profesor_' + idLibro].enable();

                wrapperAlumno.style.display = 'none';
                selectAlumno.disabled = true;
                selectAlumno.required = false;
                if (instanciasChoices['select_alumno_' + idLibro]) {
                    instanciasChoices['select_alumno_' + idLibro].setChoiceByValue('');
                    instanciasChoices['select_alumno_' + idLibro].disable();
                }

            } else {
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

</body>
</html>