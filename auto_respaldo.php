<?php
session_start();
if (!isset($_SESSION['admin_autenticado']) || $_SESSION['admin_autenticado'] !== true) {
    http_response_code(403);
    exit();
}
require_once 'conexion.php';

$intervalo_segundos = 300; // 5 minutos
$carpeta_respaldos = __DIR__ . '/respaldos_bd';
$archivo_control = $carpeta_respaldos . '/ultimo_respaldo.txt';

// 1. Crear carpeta protegida contra descargas web
if (!is_dir($carpeta_respaldos)) {
    mkdir($carpeta_respaldos, 0755, true);
}
$htaccess = $carpeta_respaldos . '/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Deny from all\n");
}

// 2. Verificar si ya pasaron 5 minutos
$ahora = time();
$ultimo = file_exists($archivo_control) ? (int)file_get_contents($archivo_control) : 0;

if (($ahora - $ultimo) < $intervalo_segundos) {
    exit();
}

file_put_contents($archivo_control, (string)$ahora);

// 3. Generar archivo .sql completo
date_default_timezone_set('America/Mexico_City');
$nombreBD = "biblioteca-bd";
$fecha = date("Y-m-d_H-i-s");
$rutaArchivo = $carpeta_respaldos . "/respaldo_{$nombreBD}_{$fecha}.sql";

$salida = "-- Respaldo Automatico de Base de Datos: {$nombreBD}\n";
$salida .= "-- Fecha de generacion: " . date("d/m/Y H:i:s") . "\n";
$salida .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

$tablas = [];
$resultadoTablas = $conexion->query("SHOW TABLES");
while ($fila = $resultadoTablas->fetch_row()) {
    $tablas[] = $fila[0];
}

foreach ($tablas as $tabla) {
    $salida .= "DROP TABLE IF EXISTS `{$tabla}`;\n";
    $resCreate = $conexion->query("SHOW CREATE TABLE `{$tabla}`");
    $filaCreate = $resCreate->fetch_row();
    $salida .= $filaCreate[1] . ";\n\n";

    $resDatos = $conexion->query("SELECT * FROM `{$tabla}`");
    $numColumnas = $resDatos->field_count;

    if ($resDatos->num_rows > 0) {
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
file_put_contents($rutaArchivo, $salida);

// 4. Conservar solo los últimos 10 respaldos para no llenar el disco
$archivos = glob($carpeta_respaldos . '/respaldo_biblioteca-bd_*.sql');
if ($archivos && count($archivos) > 10) {
    sort($archivos);
    $a_eliminar = array_slice($archivos, 0, count($archivos) - 10);
    foreach ($a_eliminar as $viejo) {
        @unlink($viejo);
    }
}
$conexion->close();
?>