<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

require_once "vistas/parte_superior.php"; 

// Conexión a la base de datos
include '../db/conexion.php'; 
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Variables para la búsqueda
$nombre = '';
$tipo = '';
$mes = '';

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
    $data = [];
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
    $data = [];
    if ($resultado->rowCount() > 0) {
        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; 
        }
    } else {
        echo "No se encontraron registros.";
    }
}
?>

<!--INICIO del cont principal-->
<div class="container">
    <h1>Historial de Plantas</h1>
    <div class="container">
    
    <br>  
    <div class="row">
        <div class="col-lg-12">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre de la planta:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>">
                </div>
                <div class="form-group">
            <label for="tipo">Tipo de Planta:</label>
            <select name="tipo" class="form-control" id="tipo">
                <option value="">Seleccione un tipo</option>
                <option value="ornamental">Ornamental</option>
                <option value="medicinal">Medicinal</option>
                <option value="frutal">Frutal</option>
                <option value="forestal">Forestal</option>
            </select>
</div>
<div class="form-group">
    <label for="mes">Mes:</label>
    <select class="form-control" id="mes" name="mes">
        <option value="">Seleccione un mes</option>
        
        <?php
            $meses = array(
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            );

            foreach ($meses as $numero => $nombre) {
                echo '<option value="' . $numero . '" ' . (($numero == $mes && isset($mes)) ? 'selected' : '') . '>' . $nombre . '</option>';
            }
        ?>
    </select>
</div>
<div class="row">
    <div class="col-lg-6 d-flex justify-content-start">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-magnifying-glass"></i> Buscar
        </button>
    </div>
    <div class="col-lg-6 d-flex justify-content-end">
        <a href="reporte_p.php" target="_blank" class="btn btn-danger">
            <i class="fa-solid fa-file-pdf"></i> Descargar Reporte
        </a>
    </div>
</div>

    
    <br>  
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">        
                <table id="tablaPersonas" class="table table-striped table-bordered table-condensed" style="width:100%">
                    <thead class="text-center">
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
                    <tbody>
                
    <?php 
    // Inicializar variables para los totales
    $totalCantidadInicial = 0;
    $totalPerdidaTotal = 0;
    $totalCantidadEfectiva = 0;

    // Recorrer los datos y calcular totales
    foreach ($data as $dat) {
        $totalCantidadInicial += $dat['cantidad_inicial'];
        $totalPerdidaTotal += $dat['cantidad_perdida'];
        $totalCantidadEfectiva += $dat['cantidad_efectiva'];
    ?>
                            <tr>
                                <td><?php echo $dat['plantas_id'] ?></td>  
                                <td><?php echo $dat['planta_nombre'] ?></td> 
                                <td><?php echo $dat['planta_tipo'] ?></td>
                                <td><?php echo $dat['cantidad_inicial'] ?></td>
                                <td><?php echo $dat['cantidad_perdida'] ?></td>
                                <td><?php echo $dat['cantidad_efectiva'] ?></td>
                                <td><?php echo $dat['fecha_siembra'] ?></td>
                                <td><?php echo $dat['fecha_trasplante'] ?></td>
                                <td><?php echo $dat['fecha_traslado'] ?></td>
                            </tr>
                        <?php } ?>                                 
                    </tbody> 
                           <!-- Agregar fila para mostrar los totales -->
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Totales:</strong></td>
                            <td><strong><?php echo $totalCantidadInicial; ?></strong></td>
                            <td><strong><?php echo $totalPerdidaTotal; ?></strong></td>
                            <td><strong><?php echo $totalCantidadEfectiva; ?></strong></td>
                            <td colspan="3"></td> <!-- Espacio para las fechas -->
                        </tr>
                    </tfoot>
                </table>    
                    
            </div>
        </div>
    </div>  
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<?php require_once "vistas/parte_inferior.php"; ?>
