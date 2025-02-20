<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "vistas/parte_superior.php";

include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Consulta para obtener el historial de donativos
$query = "SELECT * FROM donaciones ORDER BY fecha_donativo DESC";
$stmt = $conexion->prepare($query);
$stmt->execute();
$donativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicializar el array para los totales
$totales = [
    'Planta' => 0,
    'Abono' => 0,
    'Humus' => 0,
    'Otro' => 0
];

// Calcular los totales
foreach ($donativos as $donativo) {
    $tipo = htmlspecialchars($donativo['tipo_donativo']);
    $cantidad = htmlspecialchars($donativo['cantidad']);

    // Asegurarse de que el tipo de donativo existe en el array de totales
    if (array_key_exists($tipo, $totales)) {
        $totales[$tipo] += $cantidad;
    } else {
        $totales[$tipo] = $cantidad; // Si es un tipo nuevo, inicializarlo
    }
}
?>

<div class="container">
    <h1>Historial de Donativos</h1>
    
    <!-- Botón para descargar el reporte general -->
    <a href="generarhd_pdf.php" target="_blank" class="btn btn-danger m-3">
        <i class="fa-solid fa-file-pdf"></i> Descargar Reporte General
    </a>

    <table id="tablaDonativos" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Beneficiario</th>
                <th>Documento</th>
                <th>Tipo Donativo</th>
                <th>Cantidad</th>
                <th>Donante</th>
                <th>Observaciones</th>
                <th>Fecha Donativo</th>
                <th>Acciones</th> <!-- Nueva columna para los botones individuales -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donativos as $donativo) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($donativo['id']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['beneficiario']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['documento']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['tipo_donativo']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['donante']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['observaciones']); ?></td>
                    <td><?php echo htmlspecialchars($donativo['fecha_donativo']); ?></td>
                    <td>
                        <!-- Botón para descargar el reporte individual -->
                        <a href="generar_pdf_individual.php?id=<?php echo htmlspecialchars($donativo['id']); ?>"  target="_blank" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-file-pdf"></i>Comprobante</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Totales:</th>
                <th colspan="5">
                    <?php
                    foreach ($totales as $tipo => $total) {
                        echo htmlspecialchars($tipo) . ": " . htmlspecialchars($total) . "<br>";
                    }
                    ?>
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css'>
<?php require_once "vistas/parte_inferior.php"; ?>
