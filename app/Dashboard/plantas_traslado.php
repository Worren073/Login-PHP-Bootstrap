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
    // Consulta SQL para obtener los datos de planta_traslado
    $query = "SELECT 
            pt.id,
            p.nombre_comun AS planta_nombre,
            pt.fecha_traslado,
            pt.cantidad_t AS cantidad_tr,
            pt.observ,
            pt.perdida AS per_traslado
          FROM planta_traslado pt
          INNER JOIN plantas p ON pt.plantas_id = p.id";

    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        echo "<div class='alert alert-warning'>No se encontraron registros.</div>";
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!-- INICIO del cont principal -->
<div class="container">
    <h1>Plantas - Traslado</h1>
    
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-between">
            <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">
                Registrar Traslado
            </button>
            
            <a href="reporte_3.php" target="_blank" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf"></i> Descargar Reporte
            </a>
            
            <button id="btnNuevaPerdida" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalNuevaPerdida">
                Registrar Nueva Pérdida
            </button>
        </div>
    </div>

    <br> 
    <br> 
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">        
                <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>Nº</th>
                            <th>Planta</th>
                            <th>Fecha de Traslado</th>
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
                        $totalCantidad += $dat['cantidad_tr']; // Suma la cantidad de cada fila
                        $totalDiferencia += $dat['per_traslado']; // Suma la diferencia de cada fila
                    ?>
                        <tr data-id="<?php echo htmlspecialchars($dat['id']); ?>">
                            <td><?php echo htmlspecialchars($dat['id']); ?></td>                            
                            <td><?php echo htmlspecialchars($dat['planta_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($dat['fecha_traslado']); ?></td>
                            <td><?php echo htmlspecialchars($dat['cantidad_tr']); ?></td>
                            <td><?php echo htmlspecialchars($dat['observ']); ?></td>
                            <td><?php echo htmlspecialchars($dat['per_traslado']); ?></td> 
                            <td>
                                <button class="btnDelete btn btn-danger"><i class="fas fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php } ?>                                 
                    </tbody> 
                    <!-- Agrega una fila para mostrar los totales -->
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong><?php echo htmlspecialchars($totalCantidad); ?></strong></td>
                            <td></td> <!-- Espacio para observación -->
                            <td><strong><?php echo htmlspecialchars($totalDiferencia); ?></strong></td>
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
                <h5 class="modal-title">Registrar Traslado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPlanta_traslado">
                <div class="modal-body">
                    <!-- Seleccionar Planta -->
                    <div class="form-group">
                        <label for="plantas_id">Seleccionar Planta:</label>
                        <select name="plantas_id" class="form-control" id="plantas_id" required>
                            <?php
                            // Consulta para obtener las plantas en estado de trasplante con sus cantidades
                            $query = "SELECT pt.plantas_id, p.nombre_comun, pt.cantidad 
                                      FROM planta_trasplante pt
                                      INNER JOIN plantas p ON pt.plantas_id = p.id";
                            $resultado = $conexion->query($query);

                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['plantas_id']}' data-cantidad='{$row['cantidad']}'>" . htmlspecialchars($row['nombre_comun']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Cantidad Disponible -->
                    <div class="form-group">
                        <label for="cantidad">Cantidad Disponible:</label>
                        <input type="number" class="form-control" id="cantidad" readonly />
                    </div>

                    <!-- Cantidad a Trasladar -->
                    <div class="form-group">
                        <label for="cantidad_tr">Cantidad a Trasladar:</label>
                        <input type="number" class="form-control" id="cantidad_tr" required />
                    </div>

                    <!-- Diferencia -->
                    <div class="form-group">
                        <label for="per_traslado">Diferencia:</label>
                        <input type="number" class="form-control" id="per_traslado" readonly />
                    </div>

                    <!-- Fecha de Traslado -->
                    <div class="form-group">
                        <label for="fecha_traslado">Fecha de Traslado:</label>
                        <input type="date" class="form-control" id="fecha_traslado" required />
                    </div>

                    <!-- Observación -->
                    <div class="form-group">
                        <label for="observ">Observación:</label>
                        <input type="text" class="form-control" id="observ" required />
                    </div>

                </div>

                <!-- Botones del Modal -->
                <div class="modal-footer">
                    <button type='button' class='btn btn-light' data-dismiss='modal'>Cancelar</button>
                    <button type='submit' class='btn btn-success'>Registrar Traslado</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Modal para registrar pérdidas adicionales -->
<!-- Modal para nueva pérdida -->
<div class="modal fade" id="modalNuevaPerdida" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nueva Pérdida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevaPerdida">
                <div class="modal-body">
                    <!-- Seleccionar Planta -->
                    <div class="form-group">
                        <label for="plantas_id_perdida">Seleccionar Planta:</label>
                        <select name="plantas_id_perdida" class="form-control" id="plantas_id_perdida" required>
                            <option value="">Seleccione una planta</option>
                            <?php
                            // Consulta para obtener las plantas en la tabla planta_traslado
                            $query = "SELECT pt.id, p.nombre_comun, pt.cantidad_t 
                                      FROM planta_traslado pt
                                      INNER JOIN plantas p ON pt.plantas_id = p.id";
                            $resultado = $conexion->query($query);

                            while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$row['id']}' data-cantidad='{$row['cantidad_t']}'>" . htmlspecialchars($row['nombre_comun']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Cantidad Disponible en Traslado -->
                    <div class="form-group">
                        <label for="cantidad_disponible">Cantidad Disponible en Traslado:</label>
                        <input type="number" class="form-control" id="cantidad_disponible" readonly />
                    </div>

                    <!-- Cantidad de Pérdida -->
                    <div class="form-group">
                        <label for="cantidad_perdida">Cantidad de Pérdida:</label>
                        <input type="number" class="form-control" id="cantidad_perdida" name="cantidad_perdida" required />
                    </div>

                    <!-- Fecha de Pérdida -->
                    <div class="form-group">
                        <label for="fecha_perdida">Fecha de Pérdida:</label>
                        <input type="date" class="form-control" id="fecha_perdida" name="fecha_perdida" required />
                    </div>

                    <!-- Observación -->
                    <div class="form-group">
                        <label for="observ_perdida">Observación:</label>
                        <input type="text" class="form-control" id="observ_perdida" name="observ_perdida" required />
                    </div>
                </div>

                <!-- Botones del Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Pérdida</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Código JavaScript para manejar eventos y formularios

$(document).ready(function() {
    // Actualizar cantidad disponible al seleccionar una planta
    $('#plantas_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const cantidad = parseInt(selectedOption.data('cantidad')) || 0;
        $('#cantidad').val(cantidad);
        $('#per_traslado').val('');
    });

    // Calcular diferencia al ingresar cantidad a trasladar
    $('#cantidad_tr').on('input', function() {
        const cantidad = parseInt($('#cantidad').val()) || 0;
        const cantidad_tr = parseInt($(this).val()) || 0;
        const per_traslado = cantidad - cantidad_tr;
        $('#per_traslado').val(per_traslado >= 0 ? per_traslado : 0);
    });

    // Enviar formulario de traslado
    $('#formPlanta_traslado').submit(function(e) {
        e.preventDefault();

        const planta_id = $('#plantas_id').val();
        const cantidad_tr = $('#cantidad_tr').val();
        const fecha_traslado = $('#fecha_traslado').val();
        const observ = $('#observ').val();
        const per_traslado = $('#per_traslado').val();

        $.ajax({
            url: '../db/registrar_traslado.php',
            type: 'POST',
            dataType: 'json',
            data: { planta_id, cantidad_tr, fecha_traslado, observ, per_traslado },
            success: function(data) {
                alert(data.message);
                $('#modalCRUD').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert("Error al registrar el traslado.");
                console.error(xhr.responseText);
            }
        });
    });

    // Evento para eliminar registro
    $('#tablaPersonas').on('click', '.btnDelete', function() {
        let row = $(this).closest('tr');  
        let id = row.data('id');  

        if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
            $.ajax({
                url: '../db/eliminar_traslado.php',  
                type: 'POST',
                dataType: 'json',
                data: { id: id },  
                success: function(data) {
                    if (data.status === "success") {
                        alert(data.message);
                        row.remove();  
                    } else {
                        alert("Error: " + data.message);
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Hubo un error al eliminar el traslado.");
                }
            });
        }
    });

    // Enviar solicitud para registrar nueva pérdida
    $(document).ready(function() {
    // Actualizar la cantidad disponible cuando se selecciona una planta
    $('#plantas_id_perdida').change(function() {
        const selectedOption = $(this).find('option:selected');
        const cantidadDisponible = parseInt(selectedOption.data('cantidad')) || 0;
        $('#cantidad_disponible').val(cantidadDisponible);
    });

    // Enviar el formulario de nueva pérdida
    $('#formNuevaPerdida').submit(function(e) {
        e.preventDefault();

        const trasladoId = $('#plantas_id_perdida').val(); // ID del traslado
        const cantidadPerdida = $('#cantidad_perdida').val();
        const fechaPerdida = $('#fecha_perdida').val();
        const observPerdida = $('#observ_perdida').val();

        $.ajax({
            url: '../db/registrar_perdida_adicional.php',
            type: 'POST',
            dataType: 'json',
            data: {
                traslado_id: trasladoId,
                cantidad_perdida: cantidadPerdida,
                fecha_perdida: fechaPerdida,
                observ_perdida: observPerdida
            },
            success: function(data) {
                alert(data.message);
                $('#modalNuevaPerdida').modal('hide');
                location.reload(); // Recargar la página para actualizar los datos
            },
            error: function(xhr) {
                alert("Error al registrar la pérdida.");
                console.error(xhr.responseText);
            }
        });
    });
});
});
</script>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
<?php require_once "vistas/parte_inferior.php"; ?>