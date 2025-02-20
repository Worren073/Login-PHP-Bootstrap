<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = $_POST['id'];

$consulta = "DELETE FROM abono WHERE id = ?";
$resultado = $conexion->prepare($consulta);
$resultado->execute([$id]);

echo json_encode(['success' => true]); // Enviar una respuesta JSON
?>
