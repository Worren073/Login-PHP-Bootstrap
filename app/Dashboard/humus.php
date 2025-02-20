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
    <h1>Humus</h1>

    <?php
    include_once '../db/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT id, tipo_h, cantidad_h, fecha_rh FROM humus";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Calcular los totales de humus
    $total_litros = 0;
    $total_kilos = 0;
    foreach ($data as $dat) {
        if ($dat['tipo_h'] == 'liquido') {
            $total_litros += $dat['cantidad_h'];
        } else {
            $total_kilos += $dat['cantidad_h'];
        }
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">Nuevo</button>
                    </div>
                    <div>
                        <a href="reporte_h.php" target="_blank" class="btn btn-danger">
                            <i class="fas fa-file-pdf mr-2"></i> Generar Reporte
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table id="tablahumus" class="table table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center">
                                    <tr>
                                        <th>Tipo de Humus</th>
                                        <th>Cantidad de Humus</th>
                                        <th>Fecha de registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($data as $dat) {
                                    ?>
                                        <tr>
                                            <td><?php echo $dat['tipo_h'] ?></td>
                                            <td><?php echo $dat['cantidad_h'] ?></td>
                                            <td><?php echo $dat['fecha_rh'] ?></td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="btn-group">
                                                        <button class="btn btn-danger btnBorrar" data-id="<?php echo $dat['id'] ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="1">Total Líquido:</th>
                                        <th colspan="3"><?php echo $total_litros . " Litros" ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="1">Total Sólido:</th>
                                        <th colspan="3"><?php echo $total_kilos . " Kilos" ?></th>
                                    </tr>
                                </tfoot>
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
                            <h5 class="modal-title" id="exampleModalLabel">Agregar Humus</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="formHumus">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="tipo_h" class="col-form-label">Tipo:</label>
                                    <select name="select" class="form-control" id="tipo_h" required>
                                        <option value="">Seleccione un Tipo</option>
                                        <option value="liquido"> Líquido</option>
                                        <option value="solido"> Sólido</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cantidad_h" class="col-form-label">Cantidad:</label>
                                    <input type="number" class="form-control" id="cantidad_h" required>
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
            <!-- Modal DELETE -->
            <div class="modal fade" id="modalBorrar" tabindex="-1" role="dialog" aria-labelledby="modalBorrarLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalBorrarLabel">Confirmar Borrado</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ¿Está seguro de que desea eliminar este registro?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" id="btnBorrarConfirmar" class="btn btn-danger">Borrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                $(document).ready(function() {
                    let idBorrar;
                    // Agregar nuevo Humus
                    $('#formHumus').submit(function(e) {
                        e.preventDefault(); // Previene el envío del formulario por defecto

                        let tipo_h = $('#tipo_h').val();
                        let cantidad_h = $('#cantidad_h').val();

                        $.ajax({
                            url: '../db/insertar_humus.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                tipo_h: tipo_h,
                                cantidad_h: cantidad_h
                            },
                            success: function(data) {
                                $('#modalCRUD').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Humus agregado correctamente.',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    location.reload(); // Recargar la tabla después de añadir
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡Error!',
                                    text: 'Hubo un problema al agregar el humus. Por favor, inténtalo de nuevo.',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        });
                    });
                    // Antes de mostrar el modal de borrado, captura el ID del elemento a borrar
                    $(document).on("click", ".btnBorrar", function() {
                        idBorrar = $(this).data('id');
                        $("#modalBorrar").modal('show');
                    });
                    // Acción de borrado
                    $("#btnBorrarConfirmar").on("click", function() {
                        $.ajax({
                            url: "../db/eliminar_humus.php",
                            type: "POST",
                            dataType: "json",
                            data: {
                                id: idBorrar
                            },
                            success: function(data) {
                                $("#modalBorrar").modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: 'Humus eliminado correctamente.',
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    location.reload(); // Recargar la tabla después de eliminar
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: '¡Error!',
                                    text: 'Hubo un problema al eliminar el humus. Por favor, inténtalo de nuevo.',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        });
                    });
                });
            </script>

            <!-- Fin del cont principal -->
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
            <?php require_once "vistas/parte_inferior.php" ?>
