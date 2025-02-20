<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if ($_SESSION["s_username"] === null) {
    header("Location: ../login/index.php");
    exit();
}
?>

<?php require_once "vistas/parte_superior.php" ?>
<!-- INICIO del cont principal -->
<div class="container">
    <h1>Abono</h1>

    <?php
    include_once '../db/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT id, kil_os, fech_a, fech_reg FROM abono";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Calcular el total de kilos
    $totalKilos = 0;
    foreach ($data as $dat) {
        $totalKilos += $dat['kil_os'];
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <button id="btnNuevo" type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCRUD">Nuevo</button>
                <a href="reporte_a.php" target="_blank" class="btn btn-danger"> <i class="fa-solid fa-file-pdf mr-2"></i> Descargar Reporte </a>
            </div>
        </div>
        <br>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table id="tablaabono" class="table table-striped table-bordered table-condensed" style="width:100%">
                    <thead class="text-center">

                        <tr>
                            <th>Nº</th>
                            <th>kilos</th>
                            <th>Fecha</th>
                            <th>Fecha de registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data as $dat) {
                        ?>
                            <tr>
                                <td><?php echo $dat['id'] ?></td>
                                <td><?php echo $dat['kil_os'] ?></td>
                                <td><?php echo $dat['fech_a'] ?></td>
                                <td><?php echo $dat['fech_reg'] ?></td>
                                <td>
                                    <a href="#" class="btn btn-danger btnEliminar" data-id="<?php echo $dat['id'] ?>"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">Total Kilos:</th>
                            <th><?php echo $totalKilos ?></th>
                            <th colspan="2"></th>
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
                <h5 class="modal-title" id="exampleModalLabel">Agregar Abono</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form id="formAbono">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kil_os" class="col-form-label">Kilos:</label required>
                        <input type="number" class="form-control" id="kil_os" required>
                    </div>

                    <div class="form-group">
                        <label for="fech_a" class="col-form-label">Fecha:</label required>
                        <input type="date" class="form-control" id="fech_a" required>
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
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#formAbono').submit(function(e) {
            e.preventDefault(); // Previene el envío del formulario por defecto

            let kil_os = $('#kil_os').val();
            let fech_a = $('#fech_a').val();

            console.log({

                kil_os: kil_os,
                fech_a: fech_a
            });

            $.ajax({
                url: '../db/insertar_abono.php',
                type: 'POST',
                dataType: 'json',
                data: {

                    kil_os: kil_os,
                    fech_a: fech_a
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

        // Función para eliminar con SweetAlert
        $(document).on('click', '.btnEliminar', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../db/eliminar_abono.php',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            Swal.fire(
                                '¡Eliminado!',
                                'El registro ha sido eliminado.',
                                'success'
                            ).then(() => {
                                location.reload(); // Recargar la tabla después de eliminar
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            })
        });
    });
</script>

<!-- Fin del cont principal -->
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
<?php require_once "vistas/parte_inferior.php" ?>
