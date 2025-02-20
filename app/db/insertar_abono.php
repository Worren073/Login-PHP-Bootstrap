<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$kil_os = (isset($_POST['kil_os'])) ? $_POST['kil_os'] : '';
$fech_a = (isset($_POST['fech_a'])) ? $_POST['fech_a'] : '';

error_log("Datos recibidos: kil_os=$kil_os, fech_a=$fech_a");

$consulta = "INSERT INTO abono (kil_os, fech_a) VALUES ('$kil_os', '$fech_a')";
$resultado = $conexion->prepare($consulta);

if ($resultado->execute()) {
    echo json_encode("Registro guardado");
} else {
    echo json_encode("Error al guardar el registro");
    error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
  }
}

$conexion = null;
?>