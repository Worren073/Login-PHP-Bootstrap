<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
    exit();
}

$pass = md5($password);

$consulta = "SELECT * FROM usuarios WHERE username = :username";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':username', $username, PDO::PARAM_STR);
$resultado->execute();

if ($resultado->rowCount() > 0) {
    echo json_encode(["success" => false, "message" => "El usuario ya existe."]);
    exit();
}

$consulta = "INSERT INTO usuarios (username, password, idRol) VALUES (:username, :password, 0)";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':username', $username, PDO::PARAM_STR);
$resultado->bindParam(':password', $pass, PDO::PARAM_STR);

if ($resultado->execute()) {
    echo json_encode(["success" => true, "message" => "Registro exitoso."]);
} else {
    $errorInfo = $resultado->errorInfo();
    echo json_encode(["success" => false, "message" => "Error al registrar el usuario. Detalle: " . $errorInfo[2]]);
}

$conexion = null;
?>