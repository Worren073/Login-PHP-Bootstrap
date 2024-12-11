<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$nombre_comun = (isset($_POST['nombre_comun'])) ? $_POST['nombre_comun'] : '';
$nombre_cien = (isset($_POST['nombre_cien'])) ? $_POST['nombre_cien'] : '';
$fecha_siembra = (isset($_POST['fecha_siembra'])) ? $_POST['fecha_siembra'] : '';
$etapa = (isset($_POST['etapa'])) ? $_POST['etapa'] : '';
$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
$cantidad = (isset($_POST['cantidad'])) ? $_POST['cantidad'] : '';

$consulta = "UPDATE plantas SET nombre_comun=?, nombre_cien=?, fecha_siembra=?, etapa=?, tipo=?, cantidad=? WHERE id=?";
$resultado = $conexion->prepare($consulta);

if ($resultado->execute([$nombre_comun, $nombre_cien, $fecha_siembra, $etapa, $tipo, $cantidad, $id])) {
    echo json_encode("Registro actualizado");
} else {
    echo json_encode("Error al actualizar el registro");
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
}

$conexion = null;
?>
