<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

require_once('vendor/tcpdf/tcpdf.php'); // Asegúrate de que la ruta sea correcta
include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Obtener el ID del donaciones$donaciones de la URL
$donaciones_id = $_GET['id'] ?? null;

if (!$donaciones_id) {
    die("ID de donaciones$donaciones no proporcionado.");
}

// Consulta para obtener los datos del donaciones$donaciones
$query = "SELECT * FROM donaciones WHERE id = :id";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':id', $donaciones_id, PDO::PARAM_INT);
$stmt->execute();
$donaciones = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donaciones) {
    die("Donativo no encontrado.");
}

// Crear nuevo PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre/Organización');
$pdf->SetTitle('Comprobante de Donativo');
$pdf->SetSubject('Comprobante de Donativo');
$pdf->SetKeywords('Donativo, PDF, Comprobante');

// Margenes
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Salto de página automático
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer fuente
$pdf->SetFont('helvetica', '', 12);

// Añadir página
$pdf->AddPage();

// Contenido del PDF
$html = '<h1>Comprobante de Donativo</h1>
<p><strong>ID del Donativo:</strong> ' . htmlspecialchars($donaciones['id']) . '</p>
<p><strong>Beneficiario:</strong> ' . htmlspecialchars($donaciones['beneficiario']) . '</p>
<p><strong>Documento:</strong> ' . htmlspecialchars($donaciones['documento']) . '</p>
<p><strong>Tipo Donativo:</strong> ' . htmlspecialchars($donaciones['tipo_donativo']) . '</p>
<p><strong>Cantidad:</strong> ' . htmlspecialchars($donaciones['cantidad']) . '</p>
<p><strong>Donante:</strong> ' . htmlspecialchars($donaciones['donante']) . '</p>
<p><strong>Observaciones:</strong> ' . htmlspecialchars($donaciones['observaciones']) . '</p>
<p><strong>Fecha Donativo:</strong> ' . htmlspecialchars($donaciones['fecha_donativo']) . '</p>';

// Escribir el HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y generar el PDF
$pdf->Output('comprobante_donativo.pdf', 'D'); // 'D' fuerza la descarga
?>
