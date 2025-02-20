<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "vistas/parte_superior.php"; 

// Conexión a la base de datos
include '../db/conexion.php'; 
$objeto = new Conexion();
$conexion = $objeto->Conectar();

try {
    // Consulta SQL
    $query = "SELECT pt.id, p.nombre_comun, p.cantidad AS planta, pt.fecha_trasplante, pt.cantidad, pt.observacion, pt.perdida_t 
          FROM planta_trasplante pt 
          INNER JOIN plantas p ON pt.plantas_id = p.id";

    $resultado = $conexion->prepare($query);
    $resultado->execute();

    // Verificar resultados
    $data = [];
    if ($resultado->rowCount() > 0) {
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; // Guardar los resultados en un array
        }
    } else {
        echo "No se encontraron registros.";
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!--INICIO del cont principal-->
<div class="container">
    <h1>Plantas - Trasplante</h1>
    
    <div class="row">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
    <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">Registrar Trasplante</button> 
    <a href="reporte_2.php" target="_blank" class="btn btn-danger">
        <i class="fa-solid fa-file-pdf"></i> Descargar Reporte
    </a>
</div>
    </div>
    
    <br>  
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">        
                <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>Nº</th>
                            <th>Planta</th>
                            <th>Fecha de Trasplante</th>
                            <th>Cantidad</th>
                            <th>Observación</th>
                            <th>Diferencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $totalCantidad = 0; // Inicializa el total de cantidad
    $totalDiferencia = 0; // Inicializa el total de diferencia

    foreach($data as $dat) {
        $totalCantidad += $dat['cantidad']; // Suma la cantidad de cada fila
        $totalDiferencia += $dat['perdida_t']; // Suma la diferencia de cada fila
    ?>
                            <tr data-id="<?php echo $dat['id']; ?>">
                                <td><?php echo $dat['id'] ?></td>                            
                                <td><?php echo $dat['nombre_comun'] ?></td>
                                <td><?php echo $dat['fecha_trasplante'] ?></td>
                                <td><?php echo $dat['cantidad'] ?></td>
                                <td><?php echo $dat['observacion'] ?></td>
                                <td><?php echo $dat['perdida_t'] ?></td>
                                <td>
                                    
                                    <button class="btnDelete btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>                                
                    </tbody>
                                            <!-- Agrega una fila para mostrar el total -->
<!-- Agrega una fila para mostrar los totales -->
<tfoot>
    <tr>
        <td colspan="3" class="text-right"><strong>Total:</strong></td>
        <td><strong><?php echo $totalCantidad; ?></strong></td>
        <td></td> <!-- Espacio para observación -->
        <td><strong><?php echo $totalDiferencia; ?></strong></td>
        <td></td> <!-- Espacio para acciones -->
    </tr>
</tfoot>     
                </table>                    
            </div>
        </div>
    </div>  
</div>

<!-- Modal para agregar trasplante -->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registrar Trasplante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formPlanta_trasplante">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plantas_id">Seleccionar Planta:</label>
                        <select name="plantas_id" class="form-control" id="plantas_id" required>
                            <?php
                            // Obtener plantas en estado de germinación
                            $query = "SELECT id, nombre_comun, cantidad FROM plantas WHERE etapa = 'pre-germinacion'";
                            $resultado = $conexion->query($query);

                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id']}' data-cantidad='{$row['cantidad']}'>{$row['nombre_comun']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="plantas_id">Cantidad Disponible:</label>
                        <input type="number" class="form-control" id="cantidad" readonly />
                    </div>
                    <div class="form-group">
                        <label for="cantidad_t">Cantidad a Trasplantar:</label>
                        <input type="number" class="form-control" id="cantidad_t" required />
                    </div>
                    <div class="form-group">
                        <label for="diferencia">Diferencia:</label>
                        <input type="number" class="form-control" id="diferencia" readonly />
                    </div>
                    <div class="form-group">
                        <label for="fecha_trasplante" class="col-form-label">Fecha de Trasplante:</label>
                        <input type="date" class="form-control" id="fecha_trasplante" required />
                    </div>
                    <div class="form-group">
                        <label for="observacion" class="col-form-label">Observación:</label>
                        <input type="text" class="form-control" id="observacion" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Trasplante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
$(document).ready(function() {
    $('#plantas_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const cantidad = parseInt(selectedOption.data('cantidad'));
        $('#cantidad').val(cantidad);
        $('#diferencia').val('');
    });

    $('#cantidad_t').on('input', function() {
        const cantidad = parseInt($('#cantidad').val());
        const cantidad_t = parseInt($(this).val()) || 0;
        const diferencia = cantidad - cantidad_t;
        $('#diferencia').val(diferencia);
    });

    $('#formPlanta_trasplante').submit(function(e) {
        e.preventDefault();

        const planta_id = $('#plantas_id').val();
        const cantidad_t = $('#cantidad_t').val();
        const fecha_trasplante = $('#fecha_trasplante').val();
        const observacion = $('#observacion').val();
        const diferencia = $('#diferencia').val();

        $.ajax({
            url: '../db/registar_trasplante.php',
            type: 'POST',
            dataType: 'json',
            data: { planta_id, cantidad_t, fecha_trasplante, observacion, diferencia },
            success: function(data) {
                alert(data.message);
                $('#modalCRUD').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert("Error al registrar el trasplante.");
                console.error(xhr.responseText);
            }
        });
    });

            // Evento para eliminar
    $('#tablaPersonas').on('click', '.btnDelete', function() {
        let row = $(this).closest('tr');  // Seleccionamos la fila (tr) que contiene el botón
        let id = row.data('id');  // Obtenemos el 'id' del atributo data-id

        console.log('ID a eliminar:', id);  // Verificamos que el ID esté siendo correctamente pasado

        if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
            $.ajax({

                url: '../db/eliminar_trasplante.php',  // Ruta del script PHP de eliminación
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
            };
        });
    });   



</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<?php require_once "vistas/parte_inferior.php"; ?>
