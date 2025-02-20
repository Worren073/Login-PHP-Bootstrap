<?php
session_start();
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

include 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que todos los datos requeridos estén presentes
    if (
        !isset($_POST['planta_id']) || 
        !isset($_POST['cantidad_tr']) || 
        !isset($_POST['fecha_traslado']) || 
        !isset($_POST['observ']) 
    ) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
        exit();
    }

    $planta_id = intval($_POST['planta_id']); // Asegurar que sea un entero
    $cantidad_tr = intval($_POST['cantidad_tr']);
    $fecha_traslado = $_POST['fecha_traslado'];
    $observ = $_POST['observ'];

    try {
        // Verificar si la planta existe en la tabla planta_trasplante
        $query = "SELECT cantidad FROM planta_trasplante WHERE plantas_id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$planta_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $cantidad_disponible = intval($resultado['cantidad']);

            // Verificar si la cantidad a trasladar excede la cantidad disponible
            if ($cantidad_tr > $cantidad_disponible) {
                echo json_encode(["status" => "error", "message" => "La cantidad a trasladar excede la cantidad disponible."]);
                exit();
            }

            // Calcular la diferencia (perdida)
            $perdida = $cantidad_disponible - $cantidad_tr;

            // Actualizar la cantidad a 0 en la tabla planta_trasplante
            $update_query = "UPDATE planta_trasplante SET cantidad = 0 WHERE plantas_id = ?";
            $update_stmt = $conexion->prepare($update_query);
            $update_stmt->execute([$planta_id]);

            // Insertar el registro de traslado en planta_traslado
            $insert_query = "INSERT INTO planta_traslado (plantas_id, fecha_traslado, cantidad_t, observ, perdida) 
                             VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conexion->prepare($insert_query);
            $insert_stmt->execute([$planta_id, $fecha_traslado, $cantidad_tr, $observ, $perdida]);

            // Actualizar el registro en la tabla historial_perdida
            $historial_query = "SELECT cantidad_perdida, cantidad_efectiva FROM historial_perdida WHERE plantas_id = ?";
            $historial_stmt = $conexion->prepare($historial_query);
            $historial_stmt->execute([$planta_id]);
            $historial_result = $historial_stmt->fetch(PDO::FETCH_ASSOC);

            if ($historial_result) {
                // Si existe el registro en historial_perdida, actualizar los campos correspondientes
                $cantidad_perdida_actual = intval($historial_result['cantidad_perdida']);
                $cantidad_efectiva_actual = intval($historial_result['cantidad_efectiva']);
                $nueva_cantidad_perdida = $cantidad_perdida_actual + $perdida;
                $nueva_cantidad_efectiva = $cantidad_efectiva_actual + $cantidad_tr;

                $update_historial_query = "UPDATE historial_perdida 
                                           SET cantidad_perdida = ?, cantidad_efectiva = ?, fecha_traslado = ? 
                                           WHERE plantas_id = ?";
                $update_historial_stmt = $conexion->prepare($update_historial_query);
                $update_historial_stmt->execute([$nueva_cantidad_perdida, $nueva_cantidad_efectiva, $fecha_traslado, $planta_id]);
            } else {
                // Si no existe el registro en historial_perdida, insertarlo
                $insert_historial_query = "INSERT INTO historial_perdida (plantas_id, cantidad_perdida, cantidad_efectiva, fecha_traslado) 
                                           VALUES (?, ?, ?, ?)";
                $insert_historial_stmt = $conexion->prepare($insert_historial_query);
                $insert_historial_stmt->execute([$planta_id, $perdida, $cantidad_tr, $fecha_traslado]);
            }

            echo json_encode(["status" => "success", "message" => "Traslado y pérdida actualizados correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Planta no encontrada en estado de trasplante."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al registrar el traslado: " . $e->getMessage()]);
    }
}
?>
