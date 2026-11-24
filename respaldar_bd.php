<?php
//resapldo de bd
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$nombreBD = "biblioteca-bd";
$fecha = date("Y-m-d_H-i-s");
$nombreArchivo = "respaldo_{$nombreBD}_{$fecha}.sql";

// Encabezados para descargar el archivo .sql directamente
header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

$salida = "-- Respaldo de Base de Datos: {$nombreBD}\n";
$salida .= "-- Fecha de generación: " . date("d/m/Y H:i:s") . "\n";
$salida .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

$tablas = [];
$resultadoTablas = $conexion->query("SHOW TABLES");
while ($fila = $resultadoTablas->fetch_row()) {
    $tablas[] = $fila[0];
}

foreach ($tablas as $tabla) {
    $salida .= "-- --------------------------------------------------------\n";
    $salida .= "-- Estructura de tabla para `{$tabla}`\n";
    $salida .= "-- --------------------------------------------------------\n";
    $salida .= "DROP TABLE IF EXISTS `{$tabla}`;\n";
    
    $resCreate = $conexion->query("SHOW CREATE TABLE `{$tabla}`");
    $filaCreate = $resCreate->fetch_row();
    $salida .= $filaCreate[1] . ";\n\n";

    $resDatos = $conexion->query("SELECT * FROM `{$tabla}`");
    $numColumnas = $resDatos->field_count;

    if ($resDatos->num_rows > 0) {
        $salida .= "-- Volcado de datos para la tabla `{$tabla}`\n";
        while ($filaDato = $resDatos->fetch_row()) {
            $salida .= "INSERT INTO `{$tabla}` VALUES(";
            for ($i = 0; $i < $numColumnas; $i++) {
                if (isset($filaDato[$i])) {
                    $valorLimpio = $conexion->real_escape_string($filaDato[$i]);
                    $valorLimpio = str_replace("\n", "\\n", $valorLimpio);
                    $salida .= '"' . $valorLimpio . '"';
                } else {
                    $salida .= 'NULL';
                }
                if ($i < ($numColumnas - 1)) {
                    $salida .= ', ';
                }
            }
            $salida .= ");\n";
        }
        $salida .= "\n";
    }
}

$salida .= "SET FOREIGN_KEY_CHECKS=1;\n";

echo $salida;
$conexion->close();
exit;
?>