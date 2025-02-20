<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = $_POST['id'];

$consulta = "DELETE FROM humus WHERE id=:id";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':id', $id, PDO::PARAM_INT);

if ($resultado->execute()) {
    $data = array('message' => 'Registro eliminado con éxito', 'success' => true);
} else {
    $data = array('message' => 'Error al eliminar el registro', 'success' => false);
}

echo json_encode($data);
?>
