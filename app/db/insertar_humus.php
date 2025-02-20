<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$tipo_h = (isset($_POST['tipo_h'])) ? $_POST['tipo_h'] : '';
$cantidad_h = (isset($_POST['cantidad_h'])) ? $_POST['cantidad_h'] : '';

error_log("Datos recibidos: tipo_h=$tipo_h, cantida_h$cantidad_h");

$consulta = "INSERT INTO humus (tipo_h, cantidad_h) VALUES ('$tipo_h', '$cantidad_h')";
$resultado = $conexion->prepare($consulta);

if ($resultado->execute()) {
    echo json_encode("Registro guardado");
} else {
    echo json_encode("Error al guardar el registro");
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
}

$conexion = null;
?>