<?php
session_start();

if($_SESSION["s_username"] === null){
    header("Location: index.php");
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
</head>
<body  class="bg-info d-flex justify-content-center align-items-center vh-100">

    <div class="bg-white p-5 rounded-5 text-secondary" style="width: 25rem">
        <div class="row">
            <div class="col-lg-12">
                <div class="jumbotron">

                    <h1 class="display-4 text-center">Bienvenido</h1>
                    <h2 class="text-center">Usuario: <span class="badge badge-primary bg-info"><?php echo $_SESSION["s_username"];?></span></h2>
                    <p class="lead text-center">Pagina de inicio</p>
                    <hr class="my-4">
                    <a class="btn btn-danger btn-lg" href="../db/logout.php" role="button">Cerrar Sesión</a>

                </div>
            </div>
        </div>
    </div>

<script src="../../assets/jquery-3.3.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="codigo.js"></script>
<script src="../../assets/sweetalert/sweetalert2.all.min.js"></script>

</body>
  

</html>