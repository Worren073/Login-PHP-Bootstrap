<?php
session_start();

// Verificar si la sesión está activa
if (!isset($_SESSION["s_username"])) {
    die("Acceso no autorizado.");
}

// Incluir archivo de conexión a la base de datos
include 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Obtener la tabla a consultar
$tabla = $_GET['tabla'] ?? 'planta_traslado';

try {
    if ($tabla === 'planta_traslado') {
        // Consulta para planta_traslado
        $query = "SELECT 
                    pt.id,
                    p.nombre_comun AS planta_nombre,
                    pt.fecha_traslado,
                    pt.cantidad_t AS cantidad,
                    pt.observ,
                    pt.perdida AS per_traslado
                  FROM planta_traslado pt
                  INNER JOIN plantas p ON pt.plantas_id = p.id";
    } else {
        // Consulta para autoconsumo
        $query = "SELECT 
                    ac.id,
                    p.nombre_comun AS planta_nombre,
                    ac.cant_autoconsumo AS cantidad,
                    ac.fecha_consumo AS fecha,
                    ac.observ_consumo AS observ
                  FROM autoconsumo ac
                  INNER JOIN plantas p ON ac.plantas_id = p.id";
    }

    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        echo "<tr><td colspan='5'>No se encontraron registros.</td></tr>";
    } else {
        foreach ($data as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['planta_nombre']) . "</td>
                    <td>" . htmlspecialchars($row['cantidad']) . "</td>
                    <td>" . htmlspecialchars($row[$tabla === 'planta_traslado' ? 'fecha_traslado' : 'fecha']) . "</td>
                    <td>" . htmlspecialchars($row['observ']) . "</td>
                  </tr>";
        }
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>