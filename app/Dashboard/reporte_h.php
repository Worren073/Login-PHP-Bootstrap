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
$pdf->SetTitle('Reporte de Humus');
$pdf->SetSubject('Reporte PDF');
$pdf->SetKeywords('TCPDF, PDF, reportes, humus');
$pdf->setPageOrientation('P');

// Establecer márgenes
$pdf->SetMargins(10, 10, 10); // Márgenes más pequeños para aprovechar mejor el espacio
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);
$pdf->AddPage(); // Agregar una página

// Definir el título
$pdf->SetFont('helvetica', 'B', 10); // Fuente más pequeña para el título
$pdf->Cell(0, 8, 'Fundación Jardín Botánico “Ezequiel Zamora”', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'Reporte de Humus', 0, 1, 'C');

// Salto de línea
$pdf->Ln(5);

// Definir encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los encabezados
$pdf->Cell(10, 8, 'Nº', 1, 0,'C');
$pdf->Cell(40, 8, 'Tipo', 1, 0,'C');
$pdf->Cell(30, 8, 'Cantidad', 1, 0,'C');
$pdf->Cell(40, 8, 'Fecha Registro', 1, 0,'C');
$pdf->Ln();

// Consultar datos de la base de datos
$consulta = "SELECT id, tipo_h AS tipo_humus, cantidad_h AS cantidad_humus, fecha_rh AS fecha_registro FROM humus";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Inicializar totales
$total_kilos = 0; // Total en kilos
$total_litros = 0; // Total en litros

// Verificar si hay datos antes de procesar
if (count($datos) > 0) {
    // Establecer fuente para el contenido
    $pdf->SetFont('helvetica', '', 9); // Fuente más pequeña para las filas

    // Agregar filas a la tabla y calcular totales
    foreach ($datos as $row) {
        $pdf->Cell(10, 7, $row['id'], 1, 0, 'C');
        $pdf->Cell(40, 7, $row['tipo_humus'], 1);
        $pdf->Cell(30, 7,(int)$row['cantidad_humus'], 1); // Cantidad numérica centrada
        $pdf->Cell(40, 7,$row['fecha_registro'], 1);

        // Sumar totales según el tipo de humus
        if (strtolower(trim($row['tipo_humus'])) === 'liquido') {
            $total_litros += (int)$row['cantidad_humus'];
        } elseif (strtolower(trim($row['tipo_humus'])) === 'kilos') {
            $total_kilos += (int)$row['cantidad_humus'];
        }

        $pdf->Ln();
    }

    // Mostrar totales al final de la tabla
    $pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los totales

    // Total en litros
    $pdf->Cell(50 ,7 ,"Total Líquido:",1 ,0 ,'R'); 
    $pdf->Cell(30 ,7 ,$total_litros . " Litros",1 ,0 ,'C'); 
    $pdf->Ln();

    // Total en kilos
    $pdf->Cell(50 ,7 ,"Total Sólido:",1 ,0 ,'R'); 
    $pdf->Cell(30 ,7 ,$total_kilos . " Kilos",1 ,0 ,'C'); 
    $pdf->Ln();
} else {
    // Si no hay datos que mostrar
    $pdf->Cell(0, 10, 'No se encontraron registros.', 1, 1, 'C');
}

// Pie del documento (opcional)
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0 ,8 ,'Reporte generado automáticamente el: '.date('Y-m-d'),0 ,0,'C');

// Salida del PDF (mostrar en navegador)
$pdf->Output('reporte_humus.pdf', 'I'); // Mostrar en el navegador ('I') o forzar descarga ('D')

exit();
?>
