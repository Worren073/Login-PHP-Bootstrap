<?php

session_start();

include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

#recepcion de datos

$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

$pass = md5($password); #Encriptacion de clave enviada por el usuario 
#Configurar la seccion de contraseña como varchar de 50 caracteres. Si es de menos el sistema no permitira que lea la contraseña MD5

# Consulta preparada para evitar SQL Injection
$consulta = "SELECT * FROM usuarios WHERE BINARY username = :username AND password = :password";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':username', $username, PDO::PARAM_STR);
$resultado->bindParam(':password', $pass, PDO::PARAM_STR);
$resultado->execute();

if($resultado->rowCount() >= 1){
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION["s_username"] = $username;
}else{
    $_SESSION["s_username"] = null;
    $data = null;
}

print json_encode($data);
$conexion=null;