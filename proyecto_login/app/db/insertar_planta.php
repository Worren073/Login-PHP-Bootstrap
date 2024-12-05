<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$nombre_comun = (isset($_POST['nombre_comun'])) ? $_POST['nombre_comun'] : '';
$nombre_cien = (isset($_POST['nombre_cien'])) ? $_POST['nombre_cien'] : '';
$fecha_siembra = (isset($_POST['fecha_siembra'])) ? $_POST['fecha_siembra'] : '';
$etapa = (isset($_POST['etapa'])) ? $_POST['etapa'] : '';
$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
$cantidad = (isset($_POST['cantidad'])) ? $_POST['cantidad'] : '';

error_log("Datos recibidos: nombre_comun=$nombre_comun, nombre_cien=$nombre_cien, fecha_siembra=$fecha_siembra, etapa=$etapa, tipo=$tipo, cantidad=$cantidad");

$consulta = "INSERT INTO plantas (nombre_comun, nombre_cien, fecha_siembra, etapa, tipo, cantidad) VALUES ('$nombre_comun', '$nombre_cien', '$fecha_siembra', '$etapa', '$tipo', '$cantidad')";        
$resultado = $conexion->prepare($consulta);

if ($resultado->execute()) {
    echo json_encode("Registro guardado");
} else {
    echo json_encode("Error al guardar el registro");
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
}

$conexion = null;
?>
