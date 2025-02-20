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

// Verificar si se ha pasado el ID del donativo
if (!isset($_GET['id'])) {
    echo "Error: ID del donativo no proporcionado.";
    exit();
}

$id_donativo = $_GET['id'];

// Consulta para obtener el donativo específico
$query = "SELECT * FROM donaciones WHERE id = :id_donativo";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':id_donativo', $id_donativo, PDO::PARAM_INT);
$stmt->execute();
$donativo = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el donativo
if (!$donativo) {
    echo "Error: Donativo no encontrado.";
    exit;
}

// Crear el objeto TCPDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Establecer la información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre o Empresa');
$pdf->SetTitle('Detalle del Donativo #' . $donativo['id']);
$pdf->SetSubject('Reporte de Donativo Individual');
$pdf->SetKeywords('Donativo, Reporte, PDF, Individual');

// Establecer márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Añadir una página
$pdf->AddPage();

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Crear el contenido HTML
$html = '<h1>Detalle del Donativo #' . htmlspecialchars($donativo['id']) . '</h1>';
$html .= '<table border="1" cellpadding="5">';
$html .= '<tr><th>Campo</th><th>Valor</th></tr>';
$html .= '<tr><td>ID</td><td>' . htmlspecialchars($donativo['id']) . '</td></tr>';
$html .= '<tr><td>Beneficiario</td><td>' . htmlspecialchars($donativo['beneficiario']) . '</td></tr>';
$html .= '<tr><td>Documento</td><td>' . htmlspecialchars($donativo['documento']) . '</td></tr>';
$html .= '<tr><td>Tipo Donativo</td><td>' . htmlspecialchars($donativo['tipo_donativo']) . '</td></tr>';
$html .= '<tr><td>Cantidad</td><td>' . htmlspecialchars($donativo['cantidad']) . '</td></tr>';
$html .= '<tr><td>Donante</td><td>' . htmlspecialchars($donativo['donante']) . '</td></tr>';
$html .= '<tr><td>Observaciones</td><td>' . htmlspecialchars($donativo['observaciones']) . '</td></tr>';
$html .= '<tr><td>Fecha Donativo</td><td>' . htmlspecialchars($donativo['fecha_donativo']) . '</td></tr>';
$html .= '</table>';

// Escribir el HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y mostrar el PDF en el navegador
$pdf->Output('donativo_' . $donativo['id'] . '.pdf', 'I');
?>
