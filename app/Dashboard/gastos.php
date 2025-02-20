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
    <h1>Gastos</h1>
    
 <?php
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();


$consulta = "SELECT id, responsable, fecha, total, razon, fech_gas FROM gastos";
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
                        <table id="tablagastos" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                            
                                <th>Responsable</th>
                                <th>Fecha</th>
                                <th>total</th>
                                <th>Razón</th>
                                <th>Fecha de Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php                            
                            foreach($data as $dat) {                                                        
                            ?>
                            <tr>
                                
                                <td><?php echo $dat['responsable'] ?></td>
                                <td><?php echo $dat['fecha'] ?></td>
                                <td><?php echo $dat['total'] ?></td>
                                <td><?php echo $dat['razon'] ?></td>
                                <td><?php echo $dat['fech_gas'] ?></td>
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

 <!-- Modal para CRUD -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Gastos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formGastos">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="responsable" class="col-form-label">Responsable:</label>
                        <input type="text" class="form-control" id="responsable" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha" class="col-form-label">Fecha:</label>
                        <input type="date" class="form-control" id="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="razon" class="col-form-label">Razon:</label>
                        <input type="text" class="form-control" id="razon" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total" class="col-form-label">Total:</label>
                        <input type="number" class="form-control" id="total" required>
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
    $('#formGastos').submit(function(e) {
        e.preventDefault(); // Previene el envío del formulario por defecto
        let responsable = $('#responsable').val();
        let fecha = $('#fecha').val();
        let razon = $('#razon').val();
        let total = $('#total').val();

        console.log({
            responsable: responsable,
            fecha: fecha,
            razon: razon,
            total: total
        });

        $.ajax({
            url: '../db/insertar_gastos.php',
            type: 'POST',
            dataType: 'json',
            data: {
                responsable: responsable,
                fecha: fecha,
                razon: razon,
                total: total
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