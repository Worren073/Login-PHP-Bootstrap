<?php
// Incluir la biblioteca TCPDF
include('vendor/tcpdf/tcpdf.php');

// Conexión a la base de datos
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Crear un nuevo objeto TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Plantas');
$pdf->SetSubject('Reporte PDF');
$pdf->SetKeywords('TCPDF, PDF, reportes, plantas');
$pdf->setPageOrientation('P');

// Establecer márgenes
$pdf->SetMargins(10, 10, 10); // Márgenes más pequeños para aprovechar mejor el espacio
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->AddPage(); // Agregar una página

// Definir el título
$pdf->SetFont('helvetica', 'B', 10); // Fuente más pequeña para el título
$pdf->Cell(0, 8, 'Fundación Jardín Botánico “Ezequiel Zamora” ', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'Reporte de Abono', 0, 1, 'C');

// Salto de línea
$pdf->Ln(5);

// Definir encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los encabezados
$pdf->Cell(30, 8, 'Nº', 1, 0,'C');
$pdf->Cell(40, 8, 'Kilos', 1, 0,'C');
$pdf->Cell(30, 8, 'Fecha', 1, 0,'C');
$pdf->Cell(40, 8, 'Fecha Registro', 1, 0,'C');
$pdf->Ln();

// Consultar datos de la base de datos
$consulta = "SELECT id, kil_os AS kilos, fech_a AS fecha_abono, fech_reg AS fecha_registro FROM abono";
$resultado = $conexion->prepare($consulta);
$resultado->execute();

// Establecer fuente para el contenido
$pdf->SetFont('helvetica', '', 9); // Fuente más pequeña para las filas

$totalKilos = 0; // Variable para almacenar el total de kilos

// Agregar filas a la tabla
foreach ($resultado as $row) {
    $pdf->Cell(30, 7, $row['id'], 1, 0, 'C');
    $pdf->Cell(40, 7, (int)$row['kilos'], 1); // Kilos numéricos centrados
    $pdf->Cell(30, 7, $row['fecha_abono'], 1);
    $pdf->Cell(40, 7, $row['fecha_registro'], 1);

    // Sumar total de kilos
    $totalKilos += (int)$row['kilos'];

    $pdf->Ln();
}

// Mostrar total al final de la tabla
$pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los totales
$pdf->Cell(30 ,7 ,"Total: ",1 ,0 ,'C'); 
$pdf->Cell(40 ,7 ,$totalKilos ,1 ,0 ,'C'); 
$pdf->Cell(30 ,7 ,"",1 ,0 ,'C'); // Espacio vacío para alineación
$pdf->Cell(40 ,7 ,"",1 ,0 ,'C'); // Espacio vacío para alineación
$pdf->Ln();

// Pie del documento (opcional)
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0 ,8 ,'Reporte generado automáticamente el: '.date('Y-m-d'),0 ,0,'C');

// Salida del PDF (mostrar en navegador)
$pdf->Output('reporte_abono.pdf', 'I'); // Mostrar en el navegador ('I') o forzar descarga ('D')

exit();
?>
