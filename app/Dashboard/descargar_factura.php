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
$pdf->SetAuthor('Jardin Botanico Ezequiel Zamora');
$pdf->SetTitle('Factura');
$pdf->SetSubject('Factura PDF');
$pdf->SetKeywords('TCPDF, PDF, facturas, ventas');

// Obtener los detalles del cliente y la venta usando el ID de la factura
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['descargar']) && isset($_POST['id'])) {
    $factura_id = $_POST['id'];

    $consulta_factura = "SELECT nombre_client, apellido_client, documento, num_telefono, fecha, productos FROM facturas WHERE id = :id";
    $resultado_factura = $conexion->prepare($consulta_factura);
    $resultado_factura->bindParam(':id', $factura_id, PDO::PARAM_INT);
    $resultado_factura->execute();
    $factura = $resultado_factura->fetch(PDO::FETCH_ASSOC);

    $nombre_client = $factura['nombre_client'];
    $apellido_client = $factura['apellido_client'];
    $documento = $factura['documento'];
    $num_telefono = $factura['num_telefono'];
    $fecha = $factura['fecha'];
    $productos = json_decode($factura['productos'], true);

    // Asegurarse de que $productos no sea null
    if (!$productos) {
        $productos = [];
    }

    // Calcular el total de la factura
    $total_factura = 0;
    foreach ($productos as $producto) {
        $total_factura += $producto['precio_total'];
    }

    // Generar la factura en HTML
    $facturaHTML = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .container {
                width: 80%;
                margin: auto;
                background-color: #fff;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h1, h3 {
                text-align: center;
            }
            .company-info, .client-info {
                margin-bottom: 20px;
            }
            .company-info p, .client-info p {
                margin: 5px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 10px;
                text-align: center;
            }
            th {
                background-color: #f4f4f4;
            }
            .summary {
                margin-top: 20px;
                text-align: right;
            }
            .summary p {
                margin: 5px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>FACTURA</h1>
            <div class='company-info'>
                <p><strong>Empresa:</strong> Jardin Botanico Ezequiel Zamora</p>
                <p><strong>Ubicación:</strong> UNELLEZ</p>
                <p><strong>Fecha:</strong> $fecha</p>
                <p><strong>N° de Factura:</strong> $factura_id</p>
            </div>
            <div class='client-info'>
                <h3>Facturar a</h3>
                <p><strong>Nombre:</strong> $nombre_client</p>
                <p><strong>Apellido:</strong> $apellido_client</p>
                <p><strong>Cédula:</strong> $documento</p>
                <p><strong>Teléfono:</strong> $num_telefono</p>
            </div>
            <h3>Detalles de la Venta</h3>
            <table>
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Producto</th>
                        <th>Precio Unidad</th>
                        <th>Precio Total</th>
                    </tr>
                </thead>
                <tbody>";

    foreach ($productos as $producto) {
        $facturaHTML .= "
        <tr>
            <td>{$producto['cantidad']}</td>
            <td>{$producto['nom_producto']}</td>
            <td>{$producto['precio_unidad']}</td>
            <td>{$producto['precio_total']}</td>
        </tr>";
    }

    $facturaHTML .= "
                </tbody>
            </table>
            <div class='summary'>
                <p><strong>Total Factura:</strong> " . number_format($total_factura, 2, ',', '.') . " €</p>
            </div>
        </div>
    </body>
    </html>";

    // Verificar si se ha generado salida antes del PDF
    if (ob_get_length()) ob_end_clean();

    // Convertir el HTML a PDF utilizando TCPDF
    $pdf->AddPage();
    $pdf->writeHTML($facturaHTML, true, false, true, false, '');

    // Cerrar y enviar el archivo PDF al navegador
    $pdf->Output('factura.pdf', 'I');  // 'I' muestra el PDF en el navegador
    // $pdf->Output('factura.pdf', 'D'); // 'D' fuerza la descarga del archivo
    exit();
}
?>
