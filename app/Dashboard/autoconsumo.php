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
                pt.plantas_id,  -- Usar plantas_id en lugar de pt.id
                p.nombre_comun AS planta_nombre,
                pt.fecha_traslado,
                pt.cantidad_t,
                pt.observ,
                pt.perdida AS per_traslado
              FROM planta_traslado pt
              INNER JOIN plantas p ON pt.plantas_id = p.id";

    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        echo "<div class='alert alert-warning'>No se encontraron registros de traslado.</div>";
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!-- INICIO del cont principal -->
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Registrar Autoconsumo</h2>

            <form id="formAutoconsumo" method="POST">
                <div class="form-group">
                    <label for="planta_id">Nombre de la Planta:</label>
                    <select class="form-control" id="planta_id" name="planta_id" required>
    <option value="">Seleccionar Planta</option>
    <?php
    foreach ($data as $planta) {
        echo "<option value='" . htmlspecialchars($planta['plantas_id']) . "' 
              data-cantidad_tr='" . htmlspecialchars($planta['cantidad_t']) . "'>" . 
              htmlspecialchars($planta['planta_nombre']) . 
              " (Cantidad Trasladada: " . htmlspecialchars($planta['cantidad_t']) . ")</option>";
    }
    ?>
</select>
                </div>
                <div class="form-group">
                    <label for="cantidad_tr">Cantidad en Traslado:</label>
                    <input type="number" class="form-control" id="cantidad_tr" name="cantidad_tr" readonly>
                </div>
                <div class="form-group">
                    <label for="observ_consumo">Razón del Autoconsumo:</label>
                    <input type="text" class="form-control" id="observ_consumo" name="observ_consumo" required>
                </div>
                <div class="form-group">
                    <label for="cant_autoconsumo">Cantidad a Consumir:</label>
                    <input type="number" class="form-control" id="cant_autoconsumo" name="cant_autoconsumo" min="1" required>
                </div>
                <!-- Campo para la fecha de consumo -->
                <div class="form-group">
                    <label for="fecha_consumo">Fecha de Consumo:</label>
                    <input type="date" class="form-control" id="fecha_consumo" name="fecha_consumo" required>
                </div>
                <button type="submit" class="btn btn-primary" id="btnRegistrar">Registrar Autoconsumo</button>
            </form>
            <div id="mensaje"></div>
        </div>
    </div>

    <br>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <h2>Historial</h2>
            <!-- Botones para alternar entre las tablas -->
            <div class="mb-3 d-flex justify-content-between align-items-center">
    <button id="btnTraslados" class="btn btn-secondary"><i class="fas fa-eye ml-2"></i>Ver Traslados</button>

    <button id="btnAutoconsumo" class="btn btn-secondary">
        <i class="fas fa-eye ml-2"></i> Ver Autoconsumo
    </button>

    <a href="r_autoconsumo.php" target="_blank" class="btn btn-danger">
    <i class="fas fa-file-pdf mr-2"></i> Descargar Autoconsumo </a>
    
</div>

            <div class="table-responsive">
                <table id="tablaDatos" class="table table-striped table-bordered table-condensed" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>Nº</th>
                            <th>Planta</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Función para cargar datos en la tabla
        function cargarDatos(tabla) {
            $.ajax({
                url: '../db/cargar_datos.php', // Archivo PHP que carga los datos
                type: 'GET',
                data: { tabla: tabla },
                success: function(response) {
                    $('#tablaDatos tbody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#mensaje').html('<div class="alert alert-danger">Error al cargar los datos.</div>');
                }
            });
        }

        // Cargar datos de traslados por defecto
        cargarDatos('planta_traslado');

        // Botón para cargar datos de traslados
        $('#btnTraslados').on('click', function() {
            cargarDatos('planta_traslado');
        });

        // Botón para cargar datos de autoconsumo
        $('#btnAutoconsumo').on('click', function() {
            cargarDatos('autoconsumo');
        });

        // Actualizar la cantidad en traslado cuando se selecciona una planta
        $('#planta_id').on('change', function() {
            var cantidad_traslado = $(this).find(':selected').data('cantidad_tr');
            $('#cantidad_tr').val(cantidad_traslado);
        });

        // Validar el formulario antes de enviar
        $('#formAutoconsumo').submit(function(e) {
            e.preventDefault();

            let planta_id = $('#planta_id').val();
            let cant_autoconsumo = $('#cant_autoconsumo').val();
            let observ_consumo = $('#observ_consumo').val();
            let fecha_consumo = $('#fecha_consumo').val();
            let cantidad_traslado = $('#cantidad_tr').val();

            // Validar que la cantidad a consumir no sea mayor que la cantidad en traslado
            if (parseInt(cant_autoconsumo) > parseInt(cantidad_traslado)) {
                $('#mensaje').html('<div class="alert alert-danger">La cantidad a consumir no puede ser mayor que la cantidad en traslado.</div>');
                return;
            }

            // Deshabilitar el botón de envío para evitar múltiples clics
            $('#btnRegistrar').prop('disabled', true);

            $.ajax({
                url: '../db/registrar_autoconsumo.php',
                type: 'POST',
                data: {
                    planta_id: planta_id,
                    cant_autoconsumo: cant_autoconsumo,
                    observ_consumo: observ_consumo,
                    fecha_consumo: fecha_consumo
                },
                success: function(response) {
                    $('#mensaje').html(response);
                    $('#formAutoconsumo')[0].reset();
                    cargarDatos('planta_traslado'); // Recargar datos de traslados
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#mensaje').html('<div class="alert alert-danger">Error al registrar el autoconsumo.</div>');
                },
                complete: function() {
                    // Habilitar el botón de envío después de completar la solicitud
                    $('#btnRegistrar').prop('disabled', false);
                }
            });
        });
    });
</script>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
<?php require_once "vistas/parte_inferior.php"; ?>