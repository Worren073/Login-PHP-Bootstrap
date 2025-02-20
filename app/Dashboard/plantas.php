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
$data=$resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
        <div class="row">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
    <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">Nuevo</button>   
    <a href="reporte.php" target="_blank" class="btn btn-danger">
        <i class="fa-solid fa-file-pdf"></i> Descargar Reporte
    </a>
</div>

    <br>
    <br>  
<div class="container">
        <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">        
                        <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                       
                            <tr>
                                <!-- <th>Id</th> -->
                                <th>Nº</th>
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
                        <tbody>
                        <?php
                        $totalCantidad = 0; // Inicializa el total

                        foreach($data as $dat) {
                        $totalCantidad += $dat['cantidad']; // Suma la cantidad de cada fila
                         ?>
                           
                            <tr data-id="<?php echo $dat['id']; ?>">
    <td><?php echo $dat['id'] ?> </td>
    <td><?php echo $dat['nombre_comun'] ?></td>
    <td><?php echo $dat['nombre_cien'] ?></td>
    <td><?php echo $dat['fecha_siembra'] ?></td>
    <td><?php echo $dat['etapa'] ?></td>
    <td><?php echo $dat['tipo'] ?></td>
    <td><?php echo $dat['cantidad'] ?></td>
    <td><?php echo $dat['fecha_registro'] ?></td>

    <td>
                            <button class="btnEdit btn btn-info" data-toggle="modal" data-target="#modalCRUD" ><i class="fas fa-solid fa-pen"></i></button>
                            <button class="btnDelete btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                            </td>
                          <?php
                                }
                            ?>                                
                        </tbody>        
                        <!-- Agrega una fila para mostrar el total -->
<tfoot>
    <tr>
        <td colspan="6" class="text-right"><strong>Total:</strong></td>
        <td><strong><?php echo $totalCantidad; ?></strong></td>
        <td colspan="2"></td> <!-- Espacio para las acciones -->
    </tr>
</tfoot>
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
                <h5 class="modal-title" id="exampleModalLabel">Agregar/ Editar Planta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPlantas">
                <div class="modal-body">
                <input type="hidden" id="plantas_id">
                
                    <div class="form-group">
                        <label for="nombre_comun" class="col-form-label">Nombre Común:</label>
                        <input type="text" class="form-control" id="nombre_comun" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre_cien" class="col-form-label">Nombre Científico:</label>
                        <input type="text" class="form-control" id="nombre_cien" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_siembra" class="col-form-label">Fecha de Siembra:</label>
                        <input type="date" class="form-control" id="fecha_siembra" required>
                    </div>
                    
                    <div class="form-group">
                    <label for="etapa" class="col-form-label">Etapa:</label>
                    <select name="select" class="form-control" id="etapa" required>
                            <option value="pre-germinacion"> Pre-germinación</option>
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
                        <input type="number" class="form-control" id="cantidad" required>
                    </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Formulario para agregar una nueva planta
    $('#formPlantas').submit(function(e) {
        e.preventDefault(); // Previene el envío del formulario por defecto
        
        // Obtener los valores de los campos del formulario
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

        // Enviar los datos a través de AJAX
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

    // Evento para editar
    $('#tablaPersonas').on('click', '.btnEdit', function() {
        let row = $(this).closest('tr');
        let id = row.data('id');
        
        // Rellenar el formulario del modal con los datos del registro
        $('#plant_id').val(id);
        $('#nombre_comun').val(row.find('td').eq(1).text());
        $('#nombre_cien').val(row.find('td').eq(2).text());
        $('#fecha_siembra').val(row.find('td').eq(3).text());
        $('#etapa').val(row.find('td').eq(4).text());
        $('#tipo').val(row.find('td').eq(5).text());
        $('#cantidad').val(row.find('td').eq(6).text());

        // Cambiar título del modal
        $('#modalCRUD .modal-title').text('Editar Planta');
        
        // Mostrar modal
        $('#modalCRUD').modal('show');

        // Cambiar el evento del botón guardar para editar
        $('#btnGuardar').off('click').on('click', function() {
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
                url: '../db/eliminar_planta.php',  // Ruta del script PHP de eliminación
                type: 'POST',
                dataType: 'json',
                data: { id: id },  // Enviamos el 'id' como parámetro
                success: function(data) {
                    if (data.status === "success") {
                        alert("Planta eliminada correctamente.");
                        location.reload();  // Recargar la tabla después de eliminar
                    } else {
                        alert("Error: " + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("Hubo un error al eliminar la planta.");
                }
            });
        }
    });
});
$('#tablaPersonas').on('click', '.btnDelete', function() {
    let row = $(this).closest('tr');  // Seleccionamos la fila (tr) que contiene el botón
    let id = row.data('id');  // Obtenemos el 'id' del atributo data-id

    console.log('ID a eliminar:', id);  // Verificamos que el ID esté siendo correctamente pasado

    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../db/eliminar_planta.php',  // Ruta del script PHP de eliminación
                type: 'POST',
                dataType: 'json',
                data: { id: id },  // Enviamos el 'id' como parámetro
                success: function(data) {
                    if (data.status === "success") {
                        Swal.fire({
                            title: "¡Eliminado!",
                            text: "La planta ha sido eliminada correctamente.",
                            icon: "success"
                        }).then(() => {
                            location.reload();  // Recargar la tabla después de eliminar
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Error: " + data.message,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: "Error",
                        text: "Hubo un error al eliminar la planta.",
                        icon: "error"
                    });
                }
            });
        }
    });
});

</script>


<!--FIN del cont principal-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<?php require_once "vistas/parte_inferior.php" ?>