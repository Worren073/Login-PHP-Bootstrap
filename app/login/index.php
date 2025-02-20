<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Login</title>
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome-free-6.5.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/sweetalert/sweetalert2.min.css">
    <style>
        body {
            background: url('../../app/Dashboard/img/fondologin.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Para solucionar problemas de contraste */
            background-color: rgba(0, 0, 0, 0.5); /* Agrega un color de fondo semi-transparente */
            background-blend-mode: overlay; /* Combina el color de fondo y la imagen */
        }

        .login-container {
            z-index: 1;
        }

        /* Ajustes menores para mejorar la apariencia del formulario */
        .login-container .bg-white {
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1); /* Sutil sombra */
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="bg-white p-5 rounded-5 text-muted" style="width: 25rem">
        <div class="d-flex justify-content-center" style="font-size: 7rem"><i class="fa-solid fa-circle-user"></i></div>

        <form id="formlogin" class="form" action="" method="post">
            <div class="text-center fs-2 fw-bold">Iniciar Sesión</div>

            <div class="input-group mt-2">
                <i class="fa-solid fa-user input-group-text bg-success" style="color: white; padding-top: 10px;"></i>
                <input class="form-control" type="text" placeholder="Usuario" name="username" id="username">
            </div>

            <div class="input-group mt-2">
                <i class="fa-solid fa-key input-group-text bg-success" style="color: white;  padding-top: 10px;"></i>
                <input class="form-control" type="password" placeholder="Contraseña" name="password" id="password">
            </div>

            <div>
                <input type="submit" name="submit" class="btn btn-success text-white w-100 mt-3" value="Iniciar Sesión">
            </div>

        </form>

    </div>
</div>

<!-- Scripts JavaScript -->
<script src="../../assets/jquery-3.3.1.min.js"></script>
<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script src="codigo.js"></script>
<script src="../../assets/sweetalert/sweetalert2.all.min.js"></script>

</body>
</html>
