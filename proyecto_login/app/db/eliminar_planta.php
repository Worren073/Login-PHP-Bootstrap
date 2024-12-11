<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = (isset($_POST['id'])) ? $_POST['id'] : '';

error_log("ID para eliminar: id=$id");

$consulta = "DELETE FROM plantas WHERE id=?";
$resultado = $conexion->prepare($consulta);

if ($resultado->execute([$id])) {
    echo json_encode("Registro eliminado");
} else {
    echo json_encode("Error al eliminar el registro");
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
}

$conexion = null;
?>
