<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Registro</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.5.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/sweetalert/sweetalert2.min.css">
</head>
<body class="bg-success d-flex justify-content-center align-items-center vh-100">

<div>
    <div class="bg-white p-5 rounded-5 text-secondary" style="width: 25rem">
        <div class="d-flex justify-content-center" style="font-size: 7rem"><i class="fa-solid fa-circle-user"></i></div>
        
        <form id="formregister" class="form" action="" method="post">
            <div class="text-center fs-2 fw-bold">Registro</div>

            <div class="input-group mt-2">
                <i class="fa-solid fa-user input-group-text bg-success" style="color: white; padding-top: 10px;"></i>
                <input class="form-control" type="text" placeholder="Usuario" name="username" id="username">
            </div>

            <div class="input-group mt-2">
                <i class="fa-solid fa-key input-group-text bg-success" style="color: white;  padding-top: 10px;"></i>
                <input class="form-control" type="password" placeholder="Contraseña" name="password" id="password">
            </div>

            <div>
                <input type="submit" name="submit" class="btn btn-success text-white w-100 mt-2" value="Registrar">
            </div>

            <div>
                <a href="index.php" class="btn btn-success text-white w-100 mt-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>    

<script src="../../assets/jquery-3.3.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/sweetalert/sweetalert2.all.min.js"></script>
<script src="codigo_register.js"></script>



</body>
</html>