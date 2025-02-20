<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if ($_SESSION["s_username"] === null) {
    header("Location: ../login/index.php");
    exit();
}
?>

<?php require_once "vistas/parte_superior.php" ?>

<!--INICIO del cont principal-->
<div class="container">
    <h1>Facturas</h1>


    
 <?php
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT id, nombre_client, apellido_client, documento, num_telefono, fecha, productos, precio_total FROM facturas";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="row">
    <br>  
    <div class="container">
        <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">        
                        <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Nroº</th>
                                <th>Cliente</th>
                                <th>Documento</th>
                                <th>Productos</th>
                                <th>Fecha</th>
                                <th>Precio Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $dat) { 
                                $productos = json_decode($dat['productos'], true); // Decodificar los productos de JSON
                                $productos_texto = '';
                                if ($productos) {
                                    foreach ($productos as $producto) {
                                        $productos_texto .= $producto['cantidad'] . ' x ' . $producto['nom_producto'] . '<br>';
                                    }
                                }
                            ?>
                                <tr data-id="<?php echo $dat['id']; ?>">
                                    <td><?php echo $dat['id'] ?></td>
                                    <td><?php echo $dat['nombre_client'] ?></td>
                                    <td><?php echo $dat['documento'] ?></td>
                                    <td><?php echo $productos_texto ?></td>
                                    <td><?php echo $dat['fecha'] ?></td>
                                    <td><?php echo $dat['precio_total'] ?></td>
                                    <td>
                                        <form method="post" action="descargar_factura.php" target="_blank">
                                            <input type="hidden" name="id" value="<?php echo $dat['id']; ?>">
                                            <button class="btn btn-warning" name="descargar"><i class="fas fa-solid fa-file-arrow-down"></i></button>
                                        </form>
                                        <button class="btnDelete btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
    // Evento para eliminar
    $('#tablaPersonas').on('click', '.btnDelete', function() {
        let row = $(this).closest('tr');  // Seleccionamos la fila (tr) que contiene el botón
        let id = row.data('id');  // Obtenemos el 'id' del atributo data-id

        console.log('ID a eliminar:', id);  // Verificamos que el ID esté siendo correctamente pasado

        if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
            $.ajax({
                url: '../db/eliminar_factura.php',  // Ruta del script PHP de eliminación
                type: 'POST',
                dataType: 'json',
                data: { id: id },  // Enviamos el 'id' como parámetro
                success: function(data) {
                    if (data.status === "success") {
                        alert("Factura eliminada correctamente.");
                        location.reload();  // Recargar la tabla después de eliminar
                    } else {
                        alert("Error: " + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("Hubo un error al eliminar al Cliente.");
                }
            });
        }
    });
});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<!--FIN del cont principal-->

<?php require_once "vistas/parte_inferior.php" ?>
