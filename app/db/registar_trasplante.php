<?php
session_start();
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que todos los datos requeridos estén presentes
    if (
        !isset($_POST['planta_id']) ||
        !isset($_POST['cantidad_t']) || 
        !isset($_POST['fecha_trasplante']) || 
        !isset($_POST['observacion']) 
    ) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
        exit();
    }

    $planta_id = $_POST['planta_id'];
    $cantidad_t = intval($_POST['cantidad_t']);
    $fecha_trasplante = $_POST['fecha_trasplante'];
    $observacion = $_POST['observacion'];

    try {
        // Verificar la cantidad disponible de la planta
        $query = "SELECT cantidad, fecha_siembra FROM plantas WHERE id = ? AND etapa = 'pre-germinacion'";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$planta_id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $cantidad_disponible = intval($resultado['cantidad']);
            $fecha_siembra = $resultado['fecha_siembra']; // Obtener la fecha de siembra

            // Verificar si la cantidad a trasplantar excede la cantidad disponible
            if ($cantidad_t > $cantidad_disponible) {
                echo json_encode(["status" => "error", "message" => "Cantidad a trasplantar excede la cantidad disponible."]);
                exit();
            }

            // Calcular la nueva cantidad disponible después del trasplante
            $nueva_cantidad_disponible = $cantidad_disponible - $cantidad_t;

            // Actualizar la cantidad disponible de plantas
            $update_query = "UPDATE plantas SET cantidad = 0 WHERE id = ?";
            $update_stmt = $conexion->prepare($update_query);
            $update_stmt->execute([$planta_id]);

            // Calcular la pérdida de plantas
            $perdida_t = $cantidad_disponible - $cantidad_t;

            // Registrar el trasplante con la pérdida de plantas
            $insert_trasplante_query = "INSERT INTO planta_trasplante (plantas_id, fecha_trasplante, cantidad, observacion, perdida_t) 
                                        VALUES (?, ?, ?, ?, ?)";
            $insert_trasplante_stmt = $conexion->prepare($insert_trasplante_query);
            $insert_trasplante_stmt->execute([$planta_id, $fecha_trasplante, $cantidad_t, $observacion, $perdida_t]);

            // Registrar o actualizar en historial_perdida
            $fecha_hoy = date('Y-m-d'); // Fecha actual para el campo `fecha`

            // Verificar si ya existe un registro en historial_perdida para la planta
            $select_historial_query = "SELECT * FROM historial_perdida WHERE plantas_id = ?";
            $select_historial_stmt = $conexion->prepare($select_historial_query);
            $select_historial_stmt->execute([$planta_id]);
            $historial_result = $select_historial_stmt->fetch(PDO::FETCH_ASSOC);

            if ($historial_result) {
                // Actualizar el registro existente en historial_perdida
                $cantidad_perdida_actual = intval($historial_result['cantidad_perdida']);
                $nueva_cantidad_perdida = $cantidad_perdida_actual + $perdida_t;

                $update_historial_query = "UPDATE historial_perdida 
                                           SET cantidad_perdida = ?, fecha_trasplante = ?, fecha = ? 
                                           WHERE plantas_id = ?";
                $update_historial_stmt = $conexion->prepare($update_historial_query);
                $update_historial_stmt->execute([$nueva_cantidad_perdida, $fecha_trasplante, $fecha_hoy, $planta_id]);
            } else {
                // Insertar un nuevo registro en historial_perdida si no existe
                $insert_historial_query = "INSERT INTO historial_perdida (plantas_id, fecha_siembra, cantidad_inicial, cantidad_perdida, fecha_trasplante, fecha) 
                                           VALUES (?, ?, ?, ?, ?, ?)";
                $insert_historial_stmt = $conexion->prepare($insert_historial_query);
                $insert_historial_stmt->execute([$planta_id, $fecha_siembra, $cantidad_disponible, $perdida_t, $fecha_trasplante, $fecha_hoy]);
            }

            echo json_encode(["status" => "success", "message" => "Trasplante y pérdida registrados exitosamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Planta no encontrada o no está en estado de germinación."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al registrar el trasplante: " . $e->getMessage()]);
    }
}
?>