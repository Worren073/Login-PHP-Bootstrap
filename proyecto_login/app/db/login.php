<?php

session_start();

include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

#recepcion de datos

$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

$pass = md5($password); #Encriptacion de clave enviada por el usuario 

$consulta = "SELECT * FROM usuarios WHERE username='$username' AND password='$pass'";
$resultado = $conexion->prepare($consulta);
$resultado->execute();

$data = null;

if($resultado->rowCount() >= 1){
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION["s_username"] = $username;

}else{
    $_SESSION["s_username"] = null;
}

print json_encode($data);
$conexion=null;