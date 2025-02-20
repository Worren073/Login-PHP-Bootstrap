<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();


// Procesar solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $plantaSeleccionada = $_POST['plantaSeleccionada'];
        $per_traslado= $_POST['per_traslado'];

        // Consulta para obtener la cantidad actual
        $query = "SELECT cantidad_t FROM planta_traslado WHERE id = :plantaSeleccionada";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':plantaSeleccionada', $plantaSeleccionada);
        $stmt->execute();
        $cantidadActual = $stmt->fetchColumn();

        if ($cantidadActual === false) {
            echo "No se encontró el registro para la planta seleccionada.";
            exit;
        }

        // Actualiza la cantidad restando la nueva pérdida
        $nuevaCantidad = $cantidadActual - $nuevaPerdida;

        // Actualiza la pérdida sumando la nueva pérdida
        $query = "UPDATE planta_traslado SET cantidad_t = :per_traslado, perdida = perdida + :per_traslado WHERE id = :plantaSeleccionada";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':plantaSeleccionada', $plantaSeleccionada);
        $stmt->bindParam(':nuevaCantidad', $nuevaCantidad);
        $stmt->bindParam(':per_traslado', $per_traslado);
        $stmt->execute();

        echo "Pérdida y cantidad actualizadas correctamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar los datos: " . $e->getMessage();
    }
}
?>
