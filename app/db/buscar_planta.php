<?php
// Conexión a la base de datos
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Obtener el nombre de la planta desde el AJAX
$nombre = $_POST['nombre'];

// Consulta para buscar por nombre
$query = "
SELECT 
    hp.plantas_id,
    p.nombre_comun, 
    p.tipo AS planta, 
    hp.cantidad_inicial, 
    hp.cantidad_perdida,
    hp.cantidad_efectiva,
    hp.fecha_siembra,
    hp.fecha_trasplante,
    hp.fecha_traslado
FROM 
    historial_perdida hp
JOIN 
    plantas p ON hp.plantas_id = p.id
WHERE p.nombre_comun LIKE :nombre";

// Preparar y ejecutar la consulta
$resultado = $conexion->prepare($query);
$resultado->bindParam(':nombre', '%' . $nombre . '%');
$resultado->execute();

// Verificar si hay resultados
if ($resultado->rowCount() > 0) {
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} else {
    echo json_encode([]); // Devolver un array vacío si no hay resultados
}
?>
