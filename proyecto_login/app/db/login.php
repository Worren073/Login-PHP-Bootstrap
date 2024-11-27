<?php
session_start();
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->conectar();

// Recepción de los datos enviados mediante POST desde el JS
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

$pass = md5($password);

$consulta = "SELECT usuarios.idRol AS idRol, roles.descripcion AS rol FROM usuarios JOIN roles ON usuarios.idRol = roles.id WHERE username='$username' AND password='$pass'";
$resultado = $conexion->prepare($consulta);
$resultado->execute();

if($resultado->rowCount() >= 1){
    $data = $resultado->fetchALL(PDO::FETCH_ASSOC);
    print_r($data); // Añade esta línea para depurar
    $_SESSION["s_username"] = $username;
    $_SESSION["s_idRol"] = $data[0]["idRol"];
    $_SESSION["s_rol_descripcion"] = $data[0]["rol"];
} else {
    $_SESSION["s_username"] = null;
    $data = null;
}
print json_encode($data);
$conexion = null;
?>
