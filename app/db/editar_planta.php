<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = isset($_POST['id']) ? $_POST['id'] : '';
$nombre_comun = isset($_POST['nombre_comun']) ? $_POST['nombre_comun'] : '';
$nombre_cien = isset($_POST['nombre_cien']) ? $_POST['nombre_cien'] : '';
$fecha_siembra = isset($_POST['fecha_siembra']) ? $_POST['fecha_siembra'] : '';
$etapa = isset($_POST['etapa']) ? $_POST['etapa'] : '';
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : '';

error_log("Datos recibidos: ID=$id, Nombre común=$nombre_comun, Nombre científico=$nombre_cien, Fecha=$fecha_siembra, Etapa=$etapa, Tipo=$tipo, Cantidad=$cantidad");

$consulta = "UPDATE plantas SET nombre_comun=?, nombre_cien=?, fecha_siembra=?, etapa=?, tipo=?, cantidad=? WHERE id=?";
$resultado = $conexion->prepare($consulta);

if ($resultado === false) {
    error_log("Error al preparar la consulta: " . implode(":", $conexion->errorInfo()));
}

if ($resultado->execute([$nombre_comun, $nombre_cien, $fecha_siembra, $etapa, $tipo, $cantidad, $id])) {
    echo json_encode(["status" => "success", "message" => "Registro actualizado"]);
} else {
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
    echo json_encode(["status" => "error", "message" => "No se pudo actualizar el registro"]);
}

$conexion = null;
?>
