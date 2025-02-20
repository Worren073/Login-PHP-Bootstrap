<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = isset($_POST['id']) ? $_POST['id'] : '';
$nombre_client = isset($_POST['nombre_client']) ? $_POST['nombre_client'] : '';
$apellido_client = isset($_POST['apellido_client']) ? $_POST['apellido_client'] : '';
$documento = isset($_POST['documento']) ? $_POST['documento'] : '';
$num_telefono = isset($_POST['num_telefono']) ? $_POST['num_telefono'] : '';
$correo = isset($_POST['correo']) ? $_POST['correo'] : '';

error_log("Datos recibidos: ID=$id, Nombre Cliente=$nombre_client, Apellido Cliente=$apellido_client, Documento=$documento, Numero Telefónico=$num_telefono, Correo=$correo");

$consulta = "UPDATE clientes SET nombre_client=?, apellido_client=?, documento=?, num_telefono=?, correo=? WHERE id=?";
$resultado = $conexion->prepare($consulta);

if ($resultado === false) {
    error_log("Error al preparar la consulta: " . implode(":", $conexion->errorInfo()));
}

if ($resultado->execute([$nombre_client, $apellido_client, $documento, $num_telefono, $correo, $id])) {
    echo json_encode(["status" => "success", "message" => "Registro actualizado"]);
} else {
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
    echo json_encode(["status" => "error", "message" => "No se pudo actualizar el registro"]);
}

$conexion = null;
?>
