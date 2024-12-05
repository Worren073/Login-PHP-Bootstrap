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
    <h1>Plantas</h1>
    
 <?php
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT id, nombre_comun, nombre_cien, fecha_siembra, etapa, tipo, cantidad FROM plantas";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
        <div class="row">
            <div class="col-lg-12">            
            <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">Nuevo</button>
            </div>    
        </div>    
    </div>    
    <br>  
<div class="container">
        <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">        
                        <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <!-- <th>Id</th> -->
                                <th>Nombre</th>
                                <th>Nombre Cientifico</th>
                                <th>Fecha</th>
                                <th>Etapa</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php                            
                            foreach($data as $dat) {                                                        
                            ?>
                            <tr>
                                <!-- <?php echo $dat['id'] ?> -->
                                <td><?php echo $dat['nombre_comun'] ?></td>
                                <td><?php echo $dat['nombre_cien'] ?></td>
                                <td><?php echo $dat['fecha_siembra'] ?></td>
                                <td><?php echo $dat['etapa'] ?></td>
                                <td><?php echo $dat['tipo'] ?></td>
                                <td><?php echo $dat['cantidad'] ?></td>
                            </tr>
                            <?php
                                }
                            ?>                                
                        </tbody>        
                       </table>                    
                    </div>
                </div>
        </div>  
    </div>    
      
<!-- Otros contenidos de tu archivo plantas.php -->

<!-- Modal para CRUD -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Planta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPlantas">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_comun" class="col-form-label">Nombre Común:</label>
                        <input type="text" class="form-control" id="nombre_comun">
                    </div>
                    <div class="form-group">
                        <label for="nombre_cien" class="col-form-label">Nombre Científico:</label>
                        <input type="text" class="form-control" id="nombre_cien">
                    </div>
                    <div class="form-group">
                        <label for="fecha_siembra" class="col-form-label">Fecha de Siembra:</label>
                        <input type="date" class="form-control" id="fecha_siembra">
                    </div>
                    <div class="form-group">
                        <label for="etapa" class="col-form-label">Etapa:</label>
                        <input type="text" class="form-control" id="etapa">
                    </div>
                    <div class="form-group">
                        <label for="tipo" class="col-form-label">Tipo:</label>
                        <input type="text" class="form-control" id="tipo">
                    </div>
                    <div class="form-group">
                        <label for="cantidad" class="col-form-label">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidad">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $('#formPlantas').submit(function(e) {
        e.preventDefault(); // Previene el envío del formulario por defecto
        let nombre_comun = $('#nombre_comun').val();
        let nombre_cien = $('#nombre_cien').val();
        let fecha_siembra = $('#fecha_siembra').val();
        let etapa = $('#etapa').val();
        let tipo = $('#tipo').val();
        let cantidad = $('#cantidad').val();

        console.log({
            nombre_comun: nombre_comun,
            nombre_cien: nombre_cien,
            fecha_siembra: fecha_siembra,
            etapa: etapa,
            tipo: tipo,
            cantidad: cantidad
        });

        $.ajax({
            url: '../db/insertar_planta.php',
            type: 'POST',
            dataType: 'json',
            data: {
                nombre_comun: nombre_comun,
                nombre_cien: nombre_cien,
                fecha_siembra: fecha_siembra,
                etapa: etapa,
                tipo: tipo,
                cantidad: cantidad
            },
            success: function(data) {
                console.log(data);
                $('#modalCRUD').modal('hide');
                location.reload(); // Recargar la tabla después de añadir
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
</script>

<!--FIN del cont principal-->

<?php require_once "vistas/parte_inferior.php" ?>