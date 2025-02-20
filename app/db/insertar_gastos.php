<?php
include_once 'conexion.php'; // Asegúrate de que la ruta sea correcta
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario de manera segura
    $responsable = isset($_POST['responsable']) ? trim($_POST['responsable']) : '';
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
    $total = isset($_POST['total']) ? trim($_POST['total']) : '';
    $razon = isset($_POST['razon']) ? trim($_POST['razon']) : '';

    // Log de los datos recibidos (opcional)
    error_log("Datos recibidos: responsable=$responsable, fecha=$fecha, total=$total, razon=$razon");

    // Preparar la consulta SQL utilizando parámetros para evitar inyecciones SQL
    $consulta = "INSERT INTO gastos (responsable, fecha, total, razon) VALUES (:responsable, :fecha, :total, :razon)";
    $resultado = $conexion->prepare($consulta);

    // Asociar los parámetros
    $resultado->bindParam(':responsable', $responsable);
    $resultado->bindParam(':fecha', $fecha);
    $resultado->bindParam(':total', $total);
    $resultado->bindParam(':razon', $razon);

    // Ejecutar la consulta y manejar el resultado
    if ($resultado->execute()) {
        echo json_encode(["status" => "success", "message" => "Registro guardado"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar el registro"]);
        error_log("Error al ejecutar la consulta: " . implode(":", $resultado->errorInfo()));
    }
}

// Cerrar la conexión
$conexion = null;
?>





