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

// Consultas para obtener datos
try {
    // Plantas con cantidad disponible en traslados
    $query_plantas = "SELECT
                pt.plantas_id,
                p.nombre_comun AS planta_nombre,
                SUM(pt.cantidad_t) AS cantidad_disponible
              FROM planta_traslado pt
              INNER JOIN plantas p ON pt.plantas_id = p.id
              GROUP BY pt.plantas_id, p.nombre_comun";

    $stmt_plantas = $conexion->prepare($query_plantas);
    $stmt_plantas->execute();
    $plantas = $stmt_plantas->fetchAll(PDO::FETCH_ASSOC);

    // Abonos con cantidad total disponible
    $query_abonos = "SELECT id, kil_os AS cantidad_total FROM abono";
    $stmt_abonos = $conexion->prepare($query_abonos);
    $stmt_abonos->execute();
    $abonos = $stmt_abonos->fetchAll(PDO::FETCH_ASSOC);

    // Humus con cantidad total disponible (en kg y litros)
    $query_humus = "SELECT id, tipo_h, cantidad_h AS cantidad_total FROM humus";
    $stmt_humus = $conexion->prepare($query_humus);
    $stmt_humus->execute();
    $humus = $stmt_humus->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<div class="container">
    <link rel="stylesheet" href="estilos.css">
    <div class="row">
        <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center">
    <h2>Nuevo Donativo</h2>
    <a href="historial_donativos.php" class="btn btn-info">
        Ver Historial de Donativos <i class="fas fa-eye ml-2"></i>
    </a>
</div>


            <form id="formDonativo" method="POST">
                <!-- Nuevos campos para el nombre y tipo de identificación del beneficiario -->
                <div class="form-group">
                    <label for="beneficiario">Institución / Empresa / Persona:</label>
                    <input type="text" class="form-control" id="beneficiario" name="beneficiario" required>
                </div>

                <div class="form-group">
                    <label for="tipo_documento">Tipo de Identificación:</label>
                    <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                        <option value="">Seleccione un Tipo</option>
                        <option value="V">V - Venezolano</option>
                        <option value="J">J - Jurídico</option>
                        <option value="E">E - Extranjero</option>
                        <option value="P">P - Pasaporte</option>
                        <option value="G">G - Gubernamental</option>
                        <option value="C">C - Cooperativa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="numero_documento">Número de Documento:</label>
                    <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
                </div>

                <!-- Campo oculto para el documento completo -->
                <input type="hidden" id="documento" name="documento">

                <div class="form-group">
                    <label for="tipo_donativo">Tipo de Donativo:</label>
                    <select class="form-control" id="tipo_donativo" name="tipo_donativo" required>
                        <option value="">Seleccionar Tipo</option>
                        <option value="Planta">Planta</option>
                        <option value="Abono">Abono</option>
                        <option value="Humus">Humus</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="form-group" id="campo_planta" style="display: none;">
                    <label for="planta_id">Planta Donada:</label>
                    <select class="form-control" id="planta_id" name="planta_id">
                        <option value="">Seleccionar Planta</option>
                        <?php foreach ($plantas as $planta) { ?>
                            <option
                                value="<?php echo htmlspecialchars($planta['plantas_id']); ?>"
                                data-cantidad_disponible="<?php echo htmlspecialchars($planta['cantidad_disponible']); ?>">
                                <?php echo htmlspecialchars($planta['planta_nombre']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group" id="campo_abono" style="display: none;">
                    <label for="abono_id">Abono Donado:</label>
                    <select class="form-control" id="abono_id" name="abono_id">
                        <option value="">Seleccionar Abono</option>
                        <?php foreach ($abonos as $abono) { ?>
                            <option
                                value="<?php echo htmlspecialchars($abono['id']); ?>"
                                data-cantidad_total="<?php echo htmlspecialchars($abono['cantidad_total']); ?>">
                                Abono ID: <?php echo htmlspecialchars($abono['id']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group" id="campo_humus" style="display: none;">
                    <label for="humus_id">Humus Donado:</label>
                    <select class="form-control" id="humus_id" name="humus_id">
                        <option value="">Seleccionar Humus</option>
                        <?php foreach ($humus as $humus_item) { ?>
                            <option
                                value="<?php echo htmlspecialchars($humus_item['id']); ?>"
                                data-cantidad_total="<?php echo htmlspecialchars($humus_item['cantidad_total']); ?>">
                                Humus ID: <?php echo htmlspecialchars($humus_item['id']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group" id="campo_cantidad_disponible" style="display: none;">
                    <label for="cantidad_tr">Cantidad Disponible:</label>
                    <input type="text" class="form-control" id="cantidad_tr" name="cantidad_tr" readonly>
                </div>

                <div class="form-group" id="campo_cantidad" style="display: none;">
                    <label for="cantidad">Cantidad Donada:</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1">
                </div>

                <div class="form-group" id="campo_descripcion" style="display: none;">
                    <label for="descripcion">Descripción (Otro):</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion">
                </div>

                <div class="form-group">
                    <label for="donante">Autorizado por:</label>
                    <input type="text" class="form-control" id="donante" name="donante" value="<?php echo htmlspecialchars($_SESSION['s_username']); ?>" readonly>
                </div>


                <div class="form-group">
                    <label for="observaciones">Observaciones:</label>
                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Registrar Donativo</button>
            </form>
            <div id="mensaje"></div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Función para mostrar/ocultar campos según el tipo de donativo
        function toggleCampos(tipo) {
            $('#campo_planta').hide();
            $('#campo_abono').hide();
            $('#campo_humus').hide();
            $('#campo_cantidad').hide();
            $('#campo_descripcion').hide();
            $('#campo_cantidad_disponible').hide();

            if (tipo === 'Planta') {
                $('#campo_planta').show();
                $('#campo_cantidad').show();
            } else if (tipo === 'Abono') {
                $('#campo_abono').show();
                $('#campo_cantidad').show();
            } else if (tipo === 'Humus') {
                $('#campo_humus').show();
                $('#campo_cantidad').show();
            } else if (tipo === 'Otro') {
                $('#campo_descripcion').show();
                $('#campo_cantidad').show();
            }
        }

        $('#tipo_donativo').change(function() {
            var tipo = $(this).val();
            toggleCampos(tipo);
        });

        // Función para mostrar la cantidad disponible
        function mostrarCantidadDisponible(selectId, cantidadDisponibleId) {
            $(selectId).change(function() {
                var cantidadDisponible = $(this).find(':selected').data('cantidad_disponible');
                $(cantidadDisponibleId).val(cantidadDisponible);
                $('#campo_cantidad_disponible').show();
            });
        }

        // Mostrar cantidad disponible para plantas
        $('#planta_id').change(function() {
            var cantidadDisponible = $(this).find(':selected').data('cantidad_disponible');
            $('#cantidad_tr').val(cantidadDisponible);
            $('#campo_cantidad_disponible').show();
        });

        // Mostrar cantidad disponible para abonos
        $('#abono_id').change(function() {
            var cantidadTotal = $(this).find(':selected').data('cantidad_total');
            $('#cantidad_tr').val(cantidadTotal);
            $('#campo_cantidad_disponible').show();
        });

        // Mostrar cantidad disponible para humus
        $('#humus_id').change(function() {
            var cantidadTotal = $(this).find(':selected').data('cantidad_total');
            $('#cantidad_tr').val(cantidadTotal);
            $('#campo_cantidad_disponible').show();
        });

        // Intercepta el envío del formulario
        $('#formDonativo').submit(function(e) {
            e.preventDefault(); // Evita que el formulario se envíe de forma tradicional

            // Recopila los datos del formulario
            var tipoDocumento = $('#tipo_documento').val();
            var numeroDocumento = $('#numero_documento').val();
            var documentoCompleto = tipoDocumento + numeroDocumento;

            // Establece el valor del campo oculto "documento"
            $('#documento').val(documentoCompleto);

            var formData = $(this).serialize();

            // Envía los datos al servidor usando AJAX
            $.ajax({
                url: 'registrar_donativo.php', // Reemplaza con la URL correcta
                type: 'POST',
                data: formData,
                dataType: 'json', // Esperamos una respuesta en formato JSON
                success: function(response) {
                    if (response.success) {
                        // Si el registro es exitoso, muestra el mensaje de éxito con SweetAlert
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.message,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ver Historial',
                            cancelButtonText: 'Generar PDF'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirige a la página de historial si el usuario hace clic en "Ver Historial"
                                window.location.href = 'historial_donativos.php';
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Abre el PDF en una nueva pestaña si el usuario hace clic en "Generar PDF"
                                if (response.pdf_link) {
                                    window.open(response.pdf_link, '_blank');
                                    window.location.href = 'historial_donativos.php';// Redirige al historial después de generar el PDF
                                } else {
                                    Swal.fire('¡Error!', 'No se pudo generar el PDF.', 'error');
                                }
                            }
                        });
                    } else {
                        // Si hay un error, muestra el mensaje de error con SweetAlert
                        Swal.fire({
                            title: '¡Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Si hay un error en la solicitud AJAX, muestra un mensaje de error genérico
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Ocurrió un error al procesar la solicitud. Por favor, inténtalo de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    });
</script>

<?php require_once "vistas/parte_inferior.php"; ?>
