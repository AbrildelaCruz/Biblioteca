<?php
require_once 'conexion.php';

// Capturamos el texto que escribe el usuario
$filtro = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($filtro !== '') {
    // Si el usuario escribió algo, buscamos en TODA la base de datos sin límite de paginador
    // Ajusta los campos "Nombre", "Matricula", etc., según tus columnas reales
    $sql = "SELECT * FROM alumnos WHERE Nombre LIKE ? OR id_Alumnos LIKE ? ORDER BY Nombre ASC LIMIT 50";
    $stmt = $conexion->prepare($sql);
    $term = "%" . $filtro . "%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Si el buscador está vacío, volvemos a cargar las primeras 10 filas por defecto
    // Esto respeta tu diseño original cuando el usuario no está buscando nada
    $result = $conexion->query("SELECT * FROM alumnos ORDER BY Nombre ASC LIMIT 10");
}

// Renderizamos directamente las filas de la tabla (las etiquetas <tr> y <td>)
if ($result && $result->num_rows > 0) {
    while ($alumno = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($alumno['id_Alumnos']) . "</td>";
        echo "<td>" . htmlspecialchars($alumno['Nombre']) . "</td>";
        // ⚠️ NOTA: Copia aquí exactamente las mismas columnas que tienes en tu archivo 'ver_alumnos.php'
        // Por ejemplo, si tienes teléfono, correo, o botones de "Editar", agrégalos aquí igualitos.
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' style='text-align:center; font-style:italic; color:#64748b;'>❌ No se encontraron alumnos con ese nombre en toda la base de datos.</td></tr>";
}
?>