<?php
ob_start(); // ¡Añade esto al principio del todo! Inicia el búfer de salida.
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

// Conexión a la base de datos
include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// *** Sección para obtener datos de autoconsumo (INICIO) ***
try {
    // Asegúrate de que la conexión a la base de datos esté establecida
    if (!isset($conexion)) {
        $objeto = new Conexion();
        $conexion = $objeto->Conectar();
    }

    $queryAutoconsumo = "
        SELECT
            ac.id,
            p.nombre_comun AS planta_nombre,
            ac.cant_autoconsumo,
            ac.fecha_consumo,
            ac.observ_consumo
        FROM autoconsumo ac
        INNER JOIN plantas p ON ac.plantas_id = p.id";

    $stmtAutoconsumo = $conexion->prepare($queryAutoconsumo);
    $stmtAutoconsumo->execute();
    $dataAutoconsumo = $stmtAutoconsumo->fetchAll(PDO::FETCH_ASSOC);

    if (empty($dataAutoconsumo)) {
        $mensajeAutoconsumo = "<div class='alert alert-warning'>No hay datos de autoconsumo disponibles.</div>";
    }
} catch (PDOException $e) {
    die("Error en la consulta de autoconsumo: " . $e->getMessage());
}
// *** Sección para obtener datos de autoconsumo (FIN) ***

// Incluye la librería TCPDF. Ajusta la ruta si es necesario.
require_once('vendor/tcpdf/tcpdf.php');

// Crea un nuevo objeto TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Autoconsumo');
$pdf->SetSubject('Reporte PDF');
$pdf->SetKeywords('TCPDF, PDF, reportes, autoconsumo');

// Cambiar orientación a horizontal (landscape)
$pdf->setPageOrientation('L');

// Establecer márgenes
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Título del reporte
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, 'Jardín Botánico de la Universidad Experimental de los Llanos Occidentales “Ezequiel Zamora”', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'Reporte de Autoconsumo de Plantas', 0, 1, 'C');

// Salto de línea
$pdf->Ln(5);

// Definir encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(10, 8, 'Nº', 1, 0, 'C');
$pdf->Cell(60, 8, 'Planta', 1, 0, 'C');
$pdf->Cell(50, 8, 'Fecha de Consumo', 1, 0, 'C');
$pdf->Cell(40, 8, 'Cantidad', 1, 0, 'C');
$pdf->Cell(100, 8, 'Observación', 1, 0, 'C');
$pdf->Ln();

// Establecer fuente para el contenido
$pdf->SetFont('helvetica', '', 9);

// Variables para los totales
$totalCantidad = 0;

// Agregar filas a la tabla
foreach ($dataAutoconsumo as $row) {
    $pdf->Cell(10, 7, $row['id'], 1);
    $pdf->Cell(60, 7, $row['planta_nombre'], 1);
    $pdf->Cell(50, 7, $row['fecha_consumo'], 1);
    $pdf->Cell(40, 7, (int)$row['cant_autoconsumo'], 1, 0, 'C');
    $pdf->Cell(100, 7, $row['observ_consumo'], 1, 0, 'C');
    $pdf->Ln();

    // Sumar totales
    $totalCantidad += (int)$row['cant_autoconsumo'];
}

// Mostrar totales al final de la tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(120, 7, "Total Cantidad: ", 1, 0, 'R');
$pdf->Cell(40, 7, $totalCantidad, 1, 0, 'C');
$pdf->Cell(100, 7, '', 0, 0, 'C'); // Celda vacía para mantener el formato
$pdf->Ln();

// Pie del documento (opcional)
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8, 'Reporte generado automáticamente el: ' . date('Y-m-d'), 0, 0, 'C');

// Salida del PDF (mostrar en navegador)
$pdf->Output('reporte_autoconsumo.pdf', 'I'); // Mostrar en el navegador ('I') o forzar descarga ('D')

ob_end_flush(); // Envía el búfer de salida y lo desactiva.
exit();
?>
