<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

// Incluir la biblioteca TCPDF
require_once('vendor/tcpdf/tcpdf.php');

// Conexión a la base de datos
include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Variables para la búsqueda
$nombre = '';
$tipo = '';
$mes = '';
$data = [];  // Inicializar $data para evitar errores si no hay búsqueda

// Si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $mes = $_POST['mes'] ?? '';

    // Preparar la consulta según los criterios de búsqueda
    $query = "
        SELECT 
            hp.plantas_id,
            hp.nombre_comun AS planta_nombre,
            p.tipo AS planta_tipo,  
            hp.cantidad_inicial, 
            hp.cantidad_perdida,
            hp.cantidad_efectiva,
            hp.fecha_siembra,
            hp.fecha_trasplante,
            hp.fecha_traslado
        FROM 
            historial_perdida hp
        JOIN plantas p ON hp.plantas_id = p.id
    ";

    $where = [];
    $params = [];

    if (!empty($nombre)) {
        $where[] = "hp.nombre_comun LIKE :nombre";
        $params[':nombre'] = '%' . $nombre . '%';
    }

    if (!empty($tipo)) {
        $where[] = "p.tipo = :tipo";
        $params[':tipo'] = $tipo;
    }

    if (!empty($mes)) {
        // Asumiendo que el mes se busca en la fecha de siembra
        $where[] = "MONTH(hp.fecha_siembra) = :mes";
        $params[':mes'] = $mes;
    }

    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    // Ejecutar la consulta
    $resultado = $conexion->prepare($query);
    $resultado->execute($params);

    // Verificar resultados
    if ($resultado->rowCount() > 0) {
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    } else {
        echo "No se encontraron registros.";
    }
} else {
    // Si no se ha enviado el formulario, mostrar todos los registros
    $query = "
        SELECT 
            hp.plantas_id,
            hp.nombre_comun AS planta_nombre,
            p.tipo AS planta_tipo,  
            hp.cantidad_inicial, 
            hp.cantidad_perdida,
            hp.cantidad_efectiva,
            hp.fecha_siembra,
            hp.fecha_trasplante,
            hp.fecha_traslado
        FROM 
            historial_perdida hp
        JOIN plantas p ON hp.plantas_id = p.id
    ";

    // Ejecutar la consulta
    $resultado = $conexion->prepare($query);
    $resultado->execute();

    // Verificar resultados
    if ($resultado->rowCount() > 0) {
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    } else {
        echo "No se encontraron registros.";
    }
}

// Crear nuevo documento PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Reporte de Historial de Plantas');
$pdf->SetSubject('Reporte generado con TCPDF');
$pdf->SetKeywords('TCPDF, PDF, reporte, plantas');

// Márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Auto ajuste de página
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Establecer fuente
$pdf->SetFont('helvetica', '', 10);

// Añadir una página
$pdf->AddPage();

// Contenido HTML
$html = '<h1>Reporte de Historial de Plantas</h1>
<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Planta ID</th>
            <th>Planta</th>
            <th>Tipo de Planta</th>
            <th>Cantidad Inicial</th>
            <th>Pérdida Total</th>
            <th>Cantidad Efectiva</th>
            <th>Fecha Siembra</th>
            <th>Fecha Trasplante</th>
            <th>Fecha Traslado</th>
        </tr>
    </thead>
    <tbody>';

// Inicializar variables para los totales
$totalCantidadInicial = 0;
$totalPerdidaTotal = 0;
$totalCantidadEfectiva = 0;

foreach ($data as $dat) {
    $totalCantidadInicial += $dat['cantidad_inicial'];
    $totalPerdidaTotal += $dat['cantidad_perdida'];
    $totalCantidadEfectiva += $dat['cantidad_efectiva'];

    $html .= '<tr>
                <td>' . $dat['plantas_id'] . '</td>
                <td>' . $dat['planta_nombre'] . '</td>
                <td>' . $dat['planta_tipo'] . '</td>
                <td>' . $dat['cantidad_inicial'] . '</td>
                <td>' . $dat['cantidad_perdida'] . '</td>
                <td>' . $dat['cantidad_efectiva'] . '</td>
                <td>' . $dat['fecha_siembra'] . '</td>
                <td>' . $dat['fecha_trasplante'] . '</td>
                <td>' . $dat['fecha_traslado'] . '</td>
            </tr>';
}

// Agregar totales al HTML
$html .= '<tr>
            <td colspan="3" align="right"><strong>Totales:</strong></td>
            <td><strong>' . $totalCantidadInicial . '</strong></td>
            <td><strong>' . $totalPerdidaTotal . '</strong></td>
            <td><strong>' . $totalCantidadEfectiva . '</strong></td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>';

// Imprimir el HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y enviar el PDF al navegador
$pdf->Output('reporte_plantas.pdf', 'I');
?>
