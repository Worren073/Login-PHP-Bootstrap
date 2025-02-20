<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

require_once('vendor/tcpdf/tcpdf.php'); // Ajusta la ruta si es necesario
include '../db/conexion.php';

$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Consulta para obtener el historial de donativos
$query = "SELECT * FROM donaciones ORDER BY fecha_donativo DESC";
$stmt = $conexion->prepare($query);
$stmt->execute();
$donativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear el objeto TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Establecer la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre o Empresa');
$pdf->SetTitle('Historial de Donativos General');
$pdf->SetSubject('Reporte de Donativos');
$pdf->SetKeywords('Donativos, Reporte, PDF');

// Establecer márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Crear el contenido HTML
$html = '<h1>Historial de Donativos General</h1>';
$html .= '<table border="1" cellpadding="5">';
$html .= '<thead><tr>
            <th>ID</th>
            <th>Beneficiario</th>
            <th>Documento</th>
            <th>Tipo Donativo</th>
            <th>Cantidad</th>
            <th>Donante</th>
            <th>Observaciones</th>
            <th>Fecha Donativo</th>
        </tr></thead>';
$html .= '<tbody>';

foreach ($donativos as $donativo) {
    $html .= '<tr>
                <td>' . htmlspecialchars($donativo['id']) . '</td>
                <td>' . htmlspecialchars($donativo['beneficiario']) . '</td>
                <td>' . htmlspecialchars($donativo['documento']) . '</td>
                <td>' . htmlspecialchars($donativo['tipo_donativo']) . '</td>
                <td>' . htmlspecialchars($donativo['cantidad']) . '</td>
                <td>' . htmlspecialchars($donativo['donante']) . '</td>
                <td>' . htmlspecialchars($donativo['observaciones']) . '</td>
                <td>' . htmlspecialchars($donativo['fecha_donativo']) . '</td>
            </tr>';
}

$html .= '</tbody></table>';

// Agregar totales
$totales = [
    'Planta' => 0,
    'Abono' => 0,
    'Humus' => 0,
    'Otro' => 0
];

foreach ($donativos as $donativo) {
    $tipo = htmlspecialchars($donativo['tipo_donativo']);
    $cantidad = htmlspecialchars($donativo['cantidad']);

    if (array_key_exists($tipo, $totales)) {
        $totales[$tipo] += $cantidad;
    } else {
        $totales[$tipo] = $cantidad;
    }
}

$html .= '<h2>Totales:</h2>';
foreach ($totales as $tipo => $total) {
    $html .= htmlspecialchars($tipo) . ": " . htmlspecialchars($total) . "<br>";
}

// Escribir el HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y generar el PDF
$pdf->Output('historial_donativos_general.pdf', 'I');
?>
