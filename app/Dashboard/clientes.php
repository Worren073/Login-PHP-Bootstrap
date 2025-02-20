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
    <h1>Clientes</h1>
    
    <?php
    include_once '../db/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT id, nombre_client, apellido_client, documento, num_telefono, correo FROM clientes";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12"></div>
        </div>
    
    <br>  
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">        
                    <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Documento</th>
                                <th>Numero Telefónico</th>
                                <th>Correo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $dat) { ?>
                            <tr data-id="<?php echo $dat['id']; ?>">
                                <td><?php echo $dat['nombre_client'] ?></td>
                                <td><?php echo $dat['apellido_client'] ?></td>
                                <td><?php echo $dat['documento'] ?></td>
                                <td><?php echo $dat['num_telefono'] ?></td>
                                <td><?php echo $dat['correo'] ?></td>
                                <td>
                                    <button class="btnEdit btn btn-info" data-toggle="modal" data-target="#modalCRUD" style="margin-right: 10px;"><i class="fas fa-solid fa-pen"></i></button>
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

<!-- Modal para CRUD -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formclientes">
                <div class="modal-body">
                    <input type="hidden" id="clientes_id">
                
                    <div class="form-group">
                        <label for="nombre_client" class="col-form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre_client" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido_client" class="col-form-label">Apellido:</label>
                        <input type="text" class="form-control" id="apellido_client" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="documento" class="col-form-label">Documento:</label>
                        <input type="text" class="form-control" id="documento" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="num_telefono" class="col-form-label">Numero Telefónico:</label>
                        <input type="text" class="form-control" id="num_telefono" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="correo" class="col-form-label">Correo:</label>
                        <input type="text" class="form-control" id="correo" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Evento para editar
    $('#tablaPersonas').on('click', '.btnEdit', function() {
        let row = $(this).closest('tr');
        let id = row.data('id');
        
        // Rellenar el formulario del modal con los datos del registro
        $('#clientes_id').val(id);
        $('#nombre_client').val(row.find('td').eq(0).text());
        $('#apellido_client').val(row.find('td').eq(1).text());
        $('#documento').val(row.find('td').eq(2).text());
        $('#num_telefono').val(row.find('td').eq(3).text());
        $('#correo').val(row.find('td').eq(4).text());

        // Cambiar título del modal
        $('#modalCRUD .modal-title').text('Editar Cliente');
        
        // Mostrar modal
        $('#modalCRUD').modal('show');

        // Cambiar el evento del botón guardar para editar
        $('#btnGuardar').off('click').on('click', function() {
            let nombre_client = $('#nombre_client').val();
            let apellido_client = $('#apellido_client').val();
            let documento = $('#documento').val();
            let num_telefono = $('#num_telefono').val();
            let correo = $('#correo').val();

            $.ajax({
                url: '../db/editar_cliente.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    nombre_client: nombre_client,
                    apellido_client: apellido_client,
                    documento: documento,
                    num_telefono: num_telefono,
                    correo: correo 
                },
                success: function(data) {
                    $('#modalCRUD').modal('hide');
                    location.reload(); // Recargar la tabla después de editar
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    // Evento para eliminar
    $('#tablaPersonas').on('click', '.btnDelete', function() {
        let row = $(this).closest('tr');  // Seleccionamos la fila (tr) que contiene el botón
        let id = row.data('id');  // Obtenemos el 'id' del atributo data-id

        console.log('ID a eliminar:', id);  // Verificamos que el ID esté siendo correctamente pasado

        if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
            $.ajax({
                url: '../db/eliminar_cliente.php',  // Ruta del script PHP de eliminación
                type: 'POST',
                dataType: 'json',
                data: { id: id },  // Enviamos el 'id' como parámetro
                success: function(data) {
                    if (data.status === "success") {
                        alert("Cliente eliminada correctamente.");
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

<!--FIN del cont principal-->
<?php require_once "vistas/parte_inferior.php" ?>
