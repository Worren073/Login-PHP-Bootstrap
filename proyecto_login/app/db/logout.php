<?php
session_start();
unset($_SESSION["s_username"]);
unset($_SESSION["s_idRol"]);
unset($_SESSION["s_rol_descripcion"]);
session_destroy();
header("location:../login/index.php")
?>