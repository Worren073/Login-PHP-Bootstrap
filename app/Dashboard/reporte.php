<?php
// Incluir la biblioteca TCPDF
include('vendor/tcpdf/tcpdf.php');

// Incluir la conexión a la base de datos
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

try {
    // Consultar datos de la base de datos
    $consulta = "SELECT id, nombre_comun, nombre_cien, fecha_siembra, tipo, cantidad, fecha_registro FROM plantas";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $plantas = $resultado->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error al consultar los datos: " . $e->getMessage());
}

// Crear un nuevo objeto TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Plantas');
$pdf->SetSubject('Reporte PDF');
$pdf->SetKeywords('TCPDF, PDF, reportes, plantas');
$pdf->setPageOrientation('L');

// Establecer márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->AddPage(); // Agregar una página

// Definir el título
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Jardín Botánico de la Universidad Experimental de los Llanos Occidentales “Ezequiel Zamora”', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Reporte de Plantas', 0, 1, 'C');

// Salto de línea
$pdf->Ln(10);

// Definir encabezados de la tabla
// Definir encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los encabezados
$pdf->Cell(8, 8, 'Nº', 1, 0,'C');
$pdf->Cell(35, 8, 'Nombre', 1, 0,'C');
$pdf->Cell(45, 8, 'Nombre Científico', 1, 0,'C');
$pdf->Cell(30, 8, 'Fecha Siembra', 1, 0,'C');
$pdf->Cell(25, 8, 'Tipo', 1, 0,'C');
$pdf->Cell(20, 8, 'Cantidad', 1, 0,'C');
$pdf->Cell(35, 8, 'Fecha Registro', 1, 0,'C');
$pdf->Ln();

// Establecer fuente para el contenido
$pdf->SetFont('helvetica', '', 9); // Fuente más pequeña para las filas

$totalCantidad = 0; // Variable para almacenar el total de cantidades

// Agregar filas a la tabla con los datos reales
foreach ($plantas as $row) {
    $pdf->Cell(8, 7, $row['id'], 1, 0,'C'); // ID
    $pdf->Cell(35, 7, $row['nombre_comun'], 1); // Nombre común
    $pdf->Cell(45, 7, $row['nombre_cien'], 1); // Nombre científico
    $pdf->Cell(30, 7, $row['fecha_siembra'], 1); // Fecha siembra
    $pdf->Cell(25, 7, $row['tipo'], 1); // Tipo
    $pdf->Cell(20, 7,(int)$row['cantidad'],1 ,0 ,'C'); // Cantidad numérica centrada
    $pdf->Cell(35 ,7 ,$row['fecha_registro'],1 ,0 ,'C'); // Fecha registro ajustada en la misma línea 
    $pdf->Ln();

    // Sumar la cantidad total
    $totalCantidad += (int)$row['cantidad'];
}

// Mostrar totales al final de la tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(143 - (20 + (35)),7,"Total Cantidad: ",1 ,0 ,'R'); 
// Mostrar total acumulado 
$pdf->Cell(30 ,7 ,$totalCantidad ,1 ,0 ,'C'); 

// Establecer fuente para el pie de página
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0 ,10 ,'Reporte de Plantas - Generado el: '.date('Y-m-d'),0 ,1 ,'C');

$pdf->Output('reporte.pdf', 'I'); // Cerrar y enviar el archivo PDF al navegador

exit();
?>
