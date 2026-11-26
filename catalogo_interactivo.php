<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$mensaje = $_SESSION['mensaje'] ?? '';
$tipoMensaje = $_SESSION['tipoMensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipoMensaje']);

$libros_res = $conexion->query("SELECT id_libro, Titulo, Autor, Categoria, Editorial, ISBN, Ejemplares_disponibles, Ejemplares_prestados FROM libros ORDER BY Titulo ASC");

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
    <title>Catálogo - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2 scroll-catalogo">

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

    <h2 style="color: #0F4C81; text-align: center; margin-top: 20px;">📚 Catálogo </h2>
    <p style="text-align: center; color: #475569; margin-bottom: 15px;"> Busca por categoría o por libro, título o/y autor.</p>

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
                $idL = $libro['id_libro'];
                $es_disponible = ($libro['Ejemplares_disponibles'] > 0);
                $cat_libro = $libro['Categoria'] ?? 'Novela y Narrativa';
                $isbn_limpio = preg_replace('/[^0-9X]/i', '', $libro['ISBN'] ?? '');
                $url_portada = !empty($isbn_limpio) 
                    ? "https://openlibrary.org{$isbn_limpio}-M.jpg?default=false" 
                    : "";
            ?>
            <div class="tarjeta-libro" data-categoria="<?php echo htmlspecialchars($cat_libro); ?>">
                <div class="fila-tarjeta-contenido">
                    <img 
                        src="<?php echo $url_portada; ?>" 
                        alt="Portada" 
                        class="portada-libro"
                        data-titulo="<?php echo htmlspecialchars($libro['Titulo']); ?>"
                        data-autor="<?php echo htmlspecialchars($libro['Autor']); ?>"
                        onerror="buscarEnGoogleBooks(this)"
                    >

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
                </div>

               <?php if ($es_disponible): ?>
                    <div class="formulario-prestamo">
                        <form action="procesar_movimiento.php" method="POST" onsubmit="return validarUsuarioTarjeta(<?php echo $idL; ?>)" class="form-tarjeta-prestamo">
                            <input type="hidden" name="id_libro" value="<?php echo $idL; ?>">
                            <input type="hidden" name="id_alumno" id="hidden_al_<?php echo $idL; ?>" disabled>
                            <input type="hidden" name="id_profesor" id="hidden_prof_<?php echo $idL; ?>" disabled>

                            <select name="tipo_usuario" id="rol_<?php echo $idL; ?>" required onchange="prepararBuscadorTarjeta(<?php echo $idL; ?>)" class="select-rol-tarjeta">
                                <option value="">¿Quién solicita?</option>
                                <option value="alumno">Alumno</option>
                                <option value="profesor">Profesor</option>
                            </select>

                            <!-- Barra de búsqueda rápida (aparece solo al elegir rol) -->
                             <!-- Barra de búsqueda rápida (aparece solo al elegir rol) -->
                            <div class="buscador-usuario-tarjeta" id="wrap_user_<?php echo $idL; ?>" style="display: none;">
                                <div class="fila-input-busqueda">
                                    <input type="text" id="input_user_<?php echo $idL; ?>" class="input-user-catalogo"
                                           placeholder="Escribe nombre o matrícula..." autocomplete="off"
                                           oninput="buscarUsuarioTarjeta(<?php echo $idL; ?>)">
                                    <button type="button" id="btn_limpiar_<?php echo $idL; ?>" class="btn-limpiar-user" onclick="limpiarUsuarioTarjeta(<?php echo $idL; ?>)" style="display: none;" title="Cambiar usuario">✕</button>
                                </div>
                                <div class="sugerencias-user-catalogo" id="sug_user_<?php echo $idL; ?>"></div>
                            </div>

                            <div class="botones-tarjeta-accion">
                                <button type="submit" name="accion" value="prestar" class="btn-tarjeta-prestar">Prestar</button>
                                <button type="submit" name="accion" value="apartar" class="btn-tarjeta-apartar">Apartar</button>
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

    <!-- CONTENEDOR DEL PAGINADOR -->
    <div class="paginador" id="paginador-interactivo" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 30px; padding: 0 20px;"></div>

    <div class="z1">
        <a class="z2" href="registro.php">REGRESAR</a>
    </div>

    <script>
        let categoriaSeleccionada = '';
        let paginaActual = 1;
        const librosPorPagina = 6;
        let tarjetasFiltradas = [];
        const temporizadoresBusqueda = {};

        // ==========================================
        // 1. BUSCADOR DE USUARIOS POR TARJETA
        // ==========================================
        function prepararBuscadorTarjeta(idLibro) {
            const rol = document.getElementById('rol_' + idLibro).value;
            const wrap = document.getElementById('wrap_user_' + idLibro);
            const input = document.getElementById('input_user_' + idLibro);

            limpiarUsuarioTarjeta(idLibro);

            if (rol === 'alumno' || rol === 'profesor') {
                wrap.style.display = 'block';
                input.placeholder = (rol === 'alumno')
                    ? 'Escribe matrícula (SUL-26-...) o nombre...'
                    : 'Escribe matrícula (DOC-26-...) o nombre...';
                input.focus();
            } else {
                wrap.style.display = 'none';
            }
        }

        function buscarUsuarioTarjeta(idLibro) {
            clearTimeout(temporizadoresBusqueda[idLibro]);
            const rol = document.getElementById('rol_' + idLibro).value;
            const q = document.getElementById('input_user_' + idLibro).value.trim();
            const cajaSug = document.getElementById('sug_user_' + idLibro);

            if (rol === '' || q === '') {
                cajaSug.style.display = 'none';
                cajaSug.innerHTML = '';
                return;
            }

            temporizadoresBusqueda[idLibro] = setTimeout(() => {
                fetch('buscar_usuario.php?tipo=' + encodeURIComponent(rol) + '&q=' + encodeURIComponent(q))
                    .then(res => res.json())
                    .then(datos => {
                        cajaSug.innerHTML = '';
                        if (datos.length > 0) {
                            datos.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'item-sug-user';
                                const extra = (rol === 'alumno') ? ` • Grupo: ${item.grupo}` : '';
                                div.innerHTML = `<strong>[${item.matricula}]</strong> ${item.nombre}<small style="color:#64748B;">${extra}</small>`;
                                div.onclick = () => seleccionarUsuarioTarjeta(idLibro, rol, item);
                                cajaSug.appendChild(div);
                            });
                        } else {
                            cajaSug.innerHTML = `<div class="item-sug-user sin-resultados">❌ No se encontró ningún ${rol}.</div>`;
                        }
                        cajaSug.style.display = 'block';
                    })
                    .catch(err => console.error('Error buscando usuario:', err));
            }, 220);
        }

        function seleccionarUsuarioTarjeta(idLibro, rol, item) {
            const input = document.getElementById('input_user_' + idLibro);
            input.value = `[${item.matricula}] ${item.nombre}`;
            input.readOnly = true;

            document.getElementById('btn_limpiar_' + idLibro).style.display = 'inline-block';
            document.getElementById('sug_user_' + idLibro).style.display = 'none';

            const hiddenAl = document.getElementById('hidden_al_' + idLibro);
            const hiddenProf = document.getElementById('hidden_prof_' + idLibro);

            if (rol === 'alumno') {
                hiddenAl.value = item.id;
                hiddenAl.disabled = false;
                hiddenProf.value = '';
                hiddenProf.disabled = true;
            } else {
                hiddenProf.value = item.id;
                hiddenProf.disabled = false;
                hiddenAl.value = '';
                hiddenAl.disabled = true;
            }
        }

        function limpiarUsuarioTarjeta(idLibro) {
            const input = document.getElementById('input_user_' + idLibro);
            input.value = '';
            input.readOnly = false;
            document.getElementById('btn_limpiar_' + idLibro).style.display = 'none';
            document.getElementById('sug_user_' + idLibro).style.display = 'none';
            document.getElementById('hidden_al_' + idLibro).disabled = true;
            document.getElementById('hidden_prof_' + idLibro).disabled = true;
        }

        function validarUsuarioTarjeta(idLibro) {
            const hiddenAl = document.getElementById('hidden_al_' + idLibro);
            const hiddenProf = document.getElementById('hidden_prof_' + idLibro);
            if (hiddenAl.disabled && hiddenProf.disabled) {
                alert('⚠️ Por favor busca y selecciona un Alumno o Profesor de la lista antes de continuar.');
                return false;
            }
            return true;
        }

        // ==========================================
        // 2. RESPALDO DE PORTADAS (GOOGLE BOOKS / SVG)
        // ==========================================
        function buscarEnGoogleBooks(img) {
            img.onerror = null;
            const titulo = img.getAttribute('data-titulo') || '';
            const autor = img.getAttribute('data-autor') || '';
            const svgFallback = "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(
                '<svg xmlns="http://w3.org" width="85" height="125" viewBox="0 0 85 125">' +
                '<rect width="85" height="125" fill="#EFF6FF"/>' +
                '<text x="42" y="65" font-size="28" text-anchor="middle">📘</text>' +
                '</svg>'
            );

            if (!titulo) {
                img.src = svgFallback;
                return;
            }

            fetch('https://googleapis.com' + encodeURIComponent('intitle:' + titulo + ' inauthor:' + autor) + '&maxResults=1')
                .then(r => r.json())
                .then(data => {
                    const thumb = data.items?.[0]?.volumeInfo?.imageLinks?.thumbnail;
                    img.src = thumb ? thumb.replace('http://', 'https://') : svgFallback;
                })
                .catch(() => { img.src = svgFallback; });
        }

        // ==========================================
        // 3. FILTRADO POR CATEGORÍA, TEXTO Y PAGINADOR
        // ==========================================
        function seleccionarCategoria(categoria, boton) {
            categoriaSeleccionada = categoria;
            document.querySelectorAll('.btn-cat').forEach(btn => btn.classList.remove('activa'));
            boton.classList.add('activa');
            filtrarCatalogo();
        }

        function filtrarCatalogo() {
            const filtro = document.getElementById('buscar-libro').value.toLowerCase().trim();
            const todasLasTarjetas = Array.from(document.getElementsByClassName('tarjeta-libro'));
            const mensajeVacio = document.getElementById('mensaje-vacio');
            
            tarjetasFiltradas = [];

            todasLasTarjetas.forEach(tarjeta => {
                const tituloElemento = tarjeta.querySelector('.titulo-libro');
                const autorElemento = tarjeta.querySelector('.autor-libro');
                const catTarjeta = tarjeta.getAttribute('data-categoria');

                const textoTitulo = tituloElemento ? tituloElemento.textContent.toLowerCase() : '';
                const textoAutor = autorElemento ? autorElemento.textContent.toLowerCase() : '';

                const coincideTexto = (textoTitulo.includes(filtro) || textoAutor.includes(filtro));
                const coincideCat = (categoriaSeleccionada === '' || catTarjeta === categoriaSeleccionada);

                if (coincideTexto && coincideCat) {
                    tarjetasFiltradas.push(tarjeta);
                }
                tarjeta.style.setProperty('display', 'none', 'important');
                 });

            if (tarjetasFiltradas.length === 0) {
                mensajeVacio.style.setProperty('display', 'block', 'important');
            } else {
                mensajeVacio.style.setProperty('display', 'none', 'important');
            }

            paginaActual = 1;
            renderizarPagina();
        }

        function renderizarPagina() {
            const totalPaginas = Math.ceil(tarjetasFiltradas.length / librosPorPagina);
            const inicio = (paginaActual - 1) * librosPorPagina;
            const fin = inicio + librosPorPagina;

            document.querySelectorAll('.tarjeta-libro').forEach(t => {
                t.style.setProperty('display', 'none', 'important');
            });

            tarjetasFiltradas.slice(inicio, fin).forEach(tarjeta => {
                tarjeta.style.setProperty('display', 'block', 'important');
            });

            generarBotonesPaginador(totalPaginas);
        }

        function generarBotonesPaginador(totalPaginas) {
            const contenedor = document.getElementById('paginador-interactivo');
            contenedor.innerHTML = '';

            if (totalPaginas <= 1) return;

            const btnAnterior = document.createElement('a');
            btnAnterior.href = '#';
            btnAnterior.textContent = '« Anterior';
            if (paginaActual === 1) btnAnterior.classList.add('deshabilitado');
            btnAnterior.onclick = (e) => {
                e.preventDefault();
                if (paginaActual > 1) {
                    paginaActual--;
                    renderizarPagina();
                    desplazarAlCatalogo();
                }
            };
            contenedor.appendChild(btnAnterior);

            for (let i = 1; i <= totalPaginas; i++) {
                const btnNum = document.createElement('a');
                btnNum.href = '#';
                btnNum.textContent = i;
                if (i === paginaActual) btnNum.classList.add('activa');
                btnNum.onclick = (e) => {
                    e.preventDefault();
                    paginaActual = i;
                    renderizarPagina();
                    desplazarAlCatalogo();
                };
                contenedor.appendChild(btnNum);
            }

            const btnSiguiente = document.createElement('a');
            btnSiguiente.href = '#';
            btnSiguiente.textContent = 'Siguiente »';
            if (paginaActual === totalPaginas) btnSiguiente.classList.add('deshabilitado');
            btnSiguiente.onclick = (e) => {
                e.preventDefault();
                if (paginaActual < totalPaginas) {
                    paginaActual++;
                    renderizarPagina();
                    desplazarAlCatalogo();
                }
            };
            contenedor.appendChild(btnSiguiente);
        }

        function desplazarAlCatalogo() {
            const buscador = document.getElementById('buscar-libro');
            if (buscador) {
                buscador.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', filtrarCatalogo);
        } else {
            filtrarCatalogo();
        }
    </script>
</body>
</html>