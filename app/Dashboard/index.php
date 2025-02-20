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

<div class="container text-center">
    <!-- Sección de bienvenida con fondo -->
    <div class="welcome-container">
        <h1 class="welcome-text">¡Bienvenido!</h1>
    </div>
</div>

<!-- Fin del cont principal -->

<?php require_once "vistas/parte_inferior.php" ?>
<style>
    .welcome-container {
        position: relative;
        display: inline-block;
        width: 100%;
        height: auto;
        background-image: url('img/planta.jpg'); /* Ruta de la imagen de fondo */
        background-size: cover; /* Ajusta el tamaño del fondo */
        background-position: center; /* Centra la imagen de fondo */
        padding: 100px 0; /* Ajusta el tamaño del padding según tus necesidades */
    }

    .welcome-text {
        position: relative;
        color: white; /* Ajusta el color del texto según tu preferencia */
        font-size: 48px; /* Ajusta el tamaño del texto según tus necesidades */
        font-weight: bold;
    }
</style>
