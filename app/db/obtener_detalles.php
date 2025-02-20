<?php
include '../db/conexion.php'; // Incluye tu archivo de conexión a la base de datos

$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id === false) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
        exit();
    }

    try {
        $query = "SELECT nombre_comun, cantidad FROM plantas WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $planta = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'planta' => $planta]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró la planta.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta: ' . $e->getMessage()]);
    }
}
?>
