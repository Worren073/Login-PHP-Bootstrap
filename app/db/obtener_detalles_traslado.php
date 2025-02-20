<?php
session_start();
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

include 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $trasladoId = intval($_GET['id']);

    try {
        // Consulta para obtener los detalles del traslado, incluyendo el nombre de la planta
        $query = "SELECT pt.id, p.nombre_comun AS planta_nombre, pt.cantidad_t 
                  FROM planta_traslado pt
                  INNER JOIN plantas p ON pt.plantas_id = p.id
                  WHERE pt.id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$trasladoId]);
        $traslado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($traslado) {
            echo json_encode([
                "status" => "success",
                "planta_nombre" => $traslado['planta_nombre'],
                "cantidad_tr" => $traslado['cantidad_t']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Traslado no encontrado."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al obtener los detalles del traslado: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID de traslado no proporcionado."]);
}
?>