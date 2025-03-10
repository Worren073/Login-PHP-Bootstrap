<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if ($_SESSION["s_username"] === null) {
    header("Location: index.php");
    exit();
}else{
    if($_SESSION["s_idRol"]!=1){
        header("Location: ../Dashboard/index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Login</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.5.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/sweetalert/sweetalert2.min.css">

    <style>
        /* Estilos para la imagen de fondo */
        body {
            background-image: url('Dashboard/img/fondo.jpg'); /* tu imagen */
            background-size: cover; /* Cubre todo el fondo */
            background-repeat: no-repeat; /* Evita la repetición de la imagen */
            background-position: center center; /* Centra la imagen */
            height: 100vh; /* Asegura que el fondo cubra toda la altura de la ventana */
            margin: 0; /* Elimina el margen predeterminado del body */
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="bg-white p-5 rounded-5 text-secondary" style="width: 25rem">
        <div class="row">
            <div class="col-lg-12">
                <div class="jumbotron">

                    <h1 class="display-4 text-center">Bienvenido</h1>
                    <h2 class="text-center">Usuario: <span class="badge badge-primary bg-success"><?php echo $_SESSION["s_username"];?></span></h2>
                    <p class="lead text-center">Página de inicio</p>
                    <hr class="my-4">
                    <a class="btn btn-danger btn-lg" href="../db/logout.php" id="logoutBtn" role="button">Cerrar Sesión</a>

                </div>
            </div>
        </div>
    </div>

<script src="../../assets/jquery-3.3.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="codigo.js"></script>
<script src="../../assets/sweetalert/sweetalert2.all.min.js"></script>

<script>
        // Manejador de evento para el botón de Cerrar Sesión
        document.querySelector('#logoutBtn').addEventListener('click', function (event) {
            event.preventDefault();  // Prevenir la acción predeterminada de redirigir inmediatamente

            // Mostrar la ventana de confirmación de SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Se cerrará tu sesión!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                // Si el usuario confirma, redirigir a logout.php para cerrar la sesión
                if (result.isConfirmed) {
                    window.location.href = "../db/logout.php"; // Redirige al archivo de logout para destruir la sesión
                }
            });
        });
</script>


</body>
</html>
