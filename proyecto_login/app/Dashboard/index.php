<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if ($_SESSION["s_username"] === null) {
    header("Location: ../login/index.php");
    exit();
}
?>

<?php require_once "vistas/parte_superior.php" ?>

<!-- Inicio del cont principal -->

<div class="container">
    <h1>Página de Inicio</h1>

<!-- Fin del cont principal -->

<?php require_once "vistas/parte_inferior.php" ?>