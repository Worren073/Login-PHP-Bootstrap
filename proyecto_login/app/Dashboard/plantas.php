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

$consulta = "SELECT id, nombre_comun, nombre_cien, fecha_siembra, etapa, tipo, cantidad, fecha_registro FROM plantas";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Nombre</th>
                            <th>Nombre Cientifico</th>
                            <th>Fecha</th>
                            <th>Etapa</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Fecha de Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $dat) { ?>
                        <tr data-id="<?php echo $dat['id'] ?>">
                            <td><?php echo $dat['nombre_comun'] ?></td>
                            <td><?php echo $dat['nombre_cien'] ?></td>
                            <td><?php echo $dat['fecha_siembra'] ?></td>
                            <td><?php echo $dat['etapa'] ?></td>
                            <td><?php echo $dat['tipo'] ?></td>
                            <td><?php echo $dat['cantidad'] ?></td>
                            <td><?php echo $dat['fecha_registro'] ?></td>
                            <td>
                                <button class="btnEdit btn btn-info" data-toggle="modal" data-target="#modalCRUD">Editar</button>
                                <button class="btnDelete btn btn-danger">Eliminar</button>
                            </td>
                        </tr>
                        <?php } ?>
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
                <h5 class="modal-title" id="exampleModalLabel">Agregar/Editar Planta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formPlantas">
                <div class="modal-body">
                    <input type="hidden" id="plant_id">
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
                    <select name="select" class="form-control" id="etapa" required>
                        
                            <option value="">Seleccione una Etapa</option>
                            <option value="pre-germinacion"> Pre-germinación</option>
                            <option value="trasplante"> Trasplante</option>
                            <option value="traslado"> Traslado</option>
                            </select>
                        
                    </div>
                    <div class="form-group">
                        <label for="tipo" class="col-form-label">Tipo:</label>
                        <select name="tipo" class="form-control" id="tipo" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="ornamental">Ornamental</option>
                            <option value="medicinal">Medicinal</option>
                            <option value="frutal">Frutal</option>
                            <option value="forestal">Forestal</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <label for="cantidad" class="col-form-label">Cantidad:</label>
                        <input type="number" class="form-control" id="cantidad">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardar" class="btn btn-dark">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Botón Nuevo
    $('#btnNuevo').click(function() {
        // Limpiar el formulario
        $('#formPlantas')[0].reset();
        $('#modalCRUD .modal-title').text('Agregar Planta');
        $('#btnGuardar').show();

        // Cambiar el evento del botón guardar para añadir
        $('#btnGuardar').off('click').on('click', function() {
            let nombre_comun = $('#nombre_comun').val();
            let nombre_cien = $('#nombre_cien').val();
            let fecha_siembra = $('#fecha_siembra').val();
            let etapa = $('#etapa').val();
            let tipo = $('#tipo').val();
            let cantidad = $('#cantidad').val();

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
                    $('#modalCRUD').modal('hide');
                    location.reload(); // Recargar la tabla después de añadir
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    // Botón Editar
    $('#tablaPersonas').on('click', '.btnEdit', function() {
        let id = $(this).closest('tr').data('id');

        // Rellenar el formulario del modal con los datos del registro
        let rowData = $(this).closest('tr').children('td');
        $('#plant_id').val(id);
        $('#nombre_comun').val(rowData.eq(0).text());
        $('#nombre_cien').val(rowData.eq(1).text());
        $('#fecha_siembra').val(rowData.eq(2).text());
        $('#etapa').val(rowData.eq(3).text());
        $('#tipo').val(rowData.eq(4).text());
        $('#cantidad').val(rowData.eq(5).text());

        $('#modalCRUD .modal-title').text('Editar Planta');
        $('#btnGuardar').show();

        // Cambiar el evento del botón guardar para editar
        $('#btnGuardar').off('click').on('click', function() {
            let id = $('#plant_id').val();
            let nombre_comun = $('#nombre_comun').val();
            let nombre_cien = $('#nombre_cien').val();
            let fecha_siembra = $('#fecha_siembra').val();
            let etapa = $('#etapa').val();
            let tipo = $('#tipo').val();
            let cantidad = $('#cantidad').val();

            $.ajax({
                url: '../db/editar_planta.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id,
                    nombre_comun: nombre_comun,
                    nombre_cien: nombre_cien,
                    fecha_siembra: fecha_siembra,
                    etapa: etapa,
                    tipo: tipo,
                    cantidad: cantidad
                },
                success: function(data) {
                    $('#modalCRUD').modal('hide');
                    location.reload(); // Recargar la tabla después de editar
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    // Botón Eliminar
    $('#tablaPersonas').on('click', '.btnDelete', function() {
        let id = $(this).closest('tr').data('id');
        if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
            $.ajax({
                url: '../db/eliminar_planta.php',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(data) {
                    location.reload(); // Recargar la tabla después de eliminar
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    });
});

</script>

<!--FIN del cont principal-->

<?php require_once "vistas/parte_inferior.php" ?>