<?php
header('Content-Type: application/json');

include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$nombre_comun = $_POST['nombre_comun'];
$nombre_cien = $_POST['nombre_cien'];
$fecha_siembra = $_POST['fecha_siembra'];
$etapa = $_POST['etapa'];
$tipo = $_POST['tipo'];
$cantidad = $_POST['cantidad'];

$consulta = "INSERT INTO plantas (nombre_comun, nombre_cien, fecha_siembra, etapa, tipo, cantidad, fecha_registro) VALUES(:nombre_comun, :nombre_cien, :fecha_siembra, :etapa, :tipo, :cantidad, NOW())";
$resultado = $conexion->prepare($consulta);
$resultado->execute(array(":nombre_comun" => $nombre_comun, ":nombre_cien" => $nombre_cien, ":fecha_siembra" => $fecha_siembra, ":etapa" => $etapa, ":tipo" => $tipo, ":cantidad" => $cantidad));

if ($resultado) {
    echo json_encode(['success' => true, 'message' => 'Planta insertada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al insertar la planta.']);
}

$conexion = null;
?>
