<?php
// Incluir la biblioteca TCPDF
require_once('vendor/tcpdf/tcpdf.php');

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

// Cambiar orientación a horizontal (landscape)
$pdf->setPageOrientation('L');

// Establecer márgenes
$pdf->SetMargins(10, 10, 10); // Márgenes más pequeños para aprovechar mejor el espacio
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Definir el título del reporte
$pdf->SetFont('helvetica', 'B', 10); // Fuente más pequeña para el título
$pdf->Cell(0, 8, 'Jardín Botánico de la Universidad Experimental de los Llanos Occidentales “Ezequiel Zamora”', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 8, 'Reporte de Plantas en Etapa de Traslado', 0, 1, 'C');

// Salto de línea
$pdf->Ln(5);

// Definir encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los encabezados
$pdf->Cell(10, 8, 'Nº', 1, 0,'C');
$pdf->Cell(40, 8, 'Planta', 1, 0,'C');
$pdf->Cell(50, 8, 'Fecha de Traslado', 1, 0,'C');
$pdf->Cell(50, 8, 'Cantidad', 1, 0,'C');
$pdf->Cell(80, 8, 'Observación', 1, 0,'C');
$pdf->Cell(30, 8, 'Pérdidas', 1, 0,'C');
$pdf->Ln();

// Consultar datos de la base de datos
try {
    // Consulta SQL para obtener los datos de planta_traslado
    $query = "SELECT 
            pt.id,
            p.nombre_comun AS planta_nombre,
            pt.fecha_traslado,
            pt.cantidad_t AS cantidad_tr,
            pt.observ AS observacion,
            pt.perdida AS perdidas
          FROM planta_traslado pt
          INNER JOIN plantas p ON pt.plantas_id = p.id";

    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener los datos como un array asociativo

    // Inicializar totales
    $totalCantidad = 0; // Variable para almacenar el total de cantidades
    $totalPerdidas = 0; // Variable para almacenar el total de pérdidas

    // Verificar si hay datos para mostrar
    if (empty($data)) {
        $pdf->Cell(0, 10, 'No se encontraron registros.', 1, 1, 'C');
    } else {
        // Establecer fuente para el contenido
        $pdf->SetFont('helvetica', '', 9); // Fuente más pequeña para las filas

        // Agregar filas a la tabla
        foreach ($data as $row) {
            $pdf->Cell(10, 7, $row['id'], 1);
            $pdf->Cell(40, 7, $row['planta_nombre'], 1);
            $pdf->Cell(50, 7, $row['fecha_traslado'], 1);
            $pdf->Cell(50, 7,(int)$row['cantidad_tr'],1 ,0 ,'C'); // Cantidad numérica centrada
            $pdf->Cell(80 ,7 ,$row['observacion'],1 ,0 ,'C'); // Observación ajustada en la misma línea 
            $pdf->Cell(30 ,7 ,$row['perdidas'],1 ,0 ,'C'); // Pérdidas ajustadas en la misma línea 

            // Sumar totales
            $totalCantidad += (int)$row['cantidad_tr'];
            $totalPerdidas += (int)$row['perdidas'];

            $pdf->Ln();
        }

        // Mostrar totales al final de la tabla
        $pdf->SetFont('helvetica', 'B', 9); // Fuente más pequeña para los totales
        $pdf->Cell(100 ,7 ,"Total Cantidad: ",1 ,0 ,'R'); 
        $pdf->Cell(50 ,7 ,$totalCantidad ,1 ,0 ,'C'); 
        $pdf->Cell(80 ,7 ,"Total Pérdidas: ",1 ,0 ,'R'); 
        $pdf->Cell(30 ,7 ,$totalPerdidas ,1 ,0 ,'C'); 
        $pdf->Ln();
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

// Pie del documento (opcional)
$pdf->Ln();
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8,'Reporte generado automáticamente el: '.date('Y-m-d'),0 ,0,'C');

// Cerrar y enviar el archivo PDF al navegador
$pdf->Output('reporte_plantas_traslado.pdf', 'I'); // Mostrar en el navegador ('I') o forzar descarga ('D')

exit();
?>
