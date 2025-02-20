<?php
include_once "conexion.php";
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recibe el id a través de POST
$id = isset($_POST['id']) ? $_POST['id'] : null;

// Si no se recibe el id, retornar error
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Falta el parámetro id']);
    exit();
}

// Realizar la eliminación
$query = "DELETE FROM facturas WHERE id = :id";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Factura eliminada correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró la Factura para eliminar']);
}
?>
