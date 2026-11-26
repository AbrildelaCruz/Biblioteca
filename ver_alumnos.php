<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alumnos Registrados - Biblioteca Sullivan</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="logobiblioteca.png">
</head>
<body class="body2">

    <h2 style="text-align: center; color: #0F4C81; margin-top: 30px;">👥 Alumnos Registrados en el Sistema</h2>

    <div class="contenedor-busqueda" style="width: 90%; max-width: 550px; margin: 20px auto; text-align: center;">
        <input type="text" id="buscador-alumnos" class="input-busqueda" 
               placeholder="🔍 Buscar por matrícula (ej. SUL-26-001), nombre, familia o grupo..." 
               style="width: 100%; padding: 12px 20px; font-size: 15px; border: 2px solid #DDE6ED; border-radius: 30px; outline: none; box-sizing: border-box; box-shadow: 0 4px 6px rgba(15, 76, 129, 0.03);">
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <a href="registrar_usuario.php" style="background-color: #2A9D8F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">➕ Agregar Nuevo Usuario</a>
    </div>

    <div class="tabla-responsiva" style="max-width: 1250px;">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th style="text-align: center;">#</th>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Madre Encargada</th>
                    <th>Familia</th>
                    <th style="text-align: center;">Piso</th>
                    <th style="text-align: center;">Grupo</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody id="tbody-alumnos">
                <?php
                $consulta = "SELECT * FROM alumnos ORDER BY id_Alumnos DESC";
                $resultado = $conexion->query($consulta);

                if ($resultado && $resultado->num_rows > 0) {
                    $contador_visual = $resultado->num_rows;
                    while ($fila = $resultado->fetch_assoc()) {
                        $id_actual = (int) $fila['id_Alumnos'];
                        $mat = htmlspecialchars($fila['Matricula'] ?? 'Sin matrícula');
                        $nombre_completo = trim(implode(' ', array_filter([
                            $fila['Nombre'] ?? '',
                            $fila['Apellido_Paterno'] ?? '',
                            $fila['Apellido_Materno'] ?? ''
                        ])));

                        echo "<tr>";
                        echo "<td style='font-weight: bold; color: #0F4C81; text-align: center;'>" . $contador_visual . "</td>";
                        echo "<td><span style='padding: 4px 10px; background: #D1FAE5; color: #065F46; border-radius: 12px; font-weight: bold; font-size: 12px; white-space: nowrap;'>" . $mat . "</span></td>";
                        echo "<td><b>" . htmlspecialchars($nombre_completo) . "</b></td>";
                        echo "<td>" . htmlspecialchars($fila['Madre_encargada'] ?: '-') . "</td>";
                        echo "<td>" . htmlspecialchars($fila['Familia'] ?: '-') . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($fila['Piso'] ?: '-') . "</td>";
                        echo "<td style='text-align: center; font-weight: 600;'>" . htmlspecialchars($fila['Grupo'] ?: '-') . "</td>";
                        echo "<td style='text-align: center;'>";
                        echo "<a href='registrar_usuario.php?editar_rol=alumno&editar_id=" . $id_actual . "' class='btn-editar'>✏️ Editar</a> ";
                        echo "<a href='registrar_usuario.php?eliminar_rol=alumno&eliminar_id=" . $id_actual . "' class='btn-eliminar' onclick='return confirm(\"¿Estás segura de que deseas eliminar permanentemente a este alumno?\");'>🗑️ Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                        $contador_visual--;
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align: center;'>No hay alumnos registrados todavía.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="z1">
        <a class="z2" href="registrar_usuario.php">REGRESAR</a>
    </div>

    <script>
    const inputAlumnos = document.getElementById('buscador-alumnos');
    const tbodyAlumnos = document.getElementById('tbody-alumnos');
    const contenidoOriginalAlumnos = tbodyAlumnos.innerHTML;
    let tiempoEsperaAlumnos = null;

    inputAlumnos.addEventListener('input', function () {
        clearTimeout(tiempoEsperaAlumnos);
        const texto = this.value.trim();

        if (texto === '') {
            tbodyAlumnos.innerHTML = contenidoOriginalAlumnos;
            return;
        }

        tiempoEsperaAlumnos = setTimeout(() => {
            fetch('buscar_alumnos_global.php?query=' + encodeURIComponent(texto))
                .then(response => response.text())
                .then(html => {
                    tbodyAlumnos.innerHTML = html;
                })
                .catch(error => console.error('Error buscando alumnos:', error));
        }, 250);
    });
    </script>
</body>
</html>