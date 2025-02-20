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
        !isset($_POST['traslado_id']) ||
        !isset($_POST['cantidad_perdida']) ||
        !isset($_POST['fecha_perdida'])
    ) {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios."]);
        exit();
    }

    $trasladoId = intval($_POST['traslado_id']);
    $cantidadPerdida = intval($_POST['cantidad_perdida']);
    $fechaPerdida = $_POST['fecha_perdida'];

    try {
        // Obtener los datos del traslado
        $query = "SELECT pt.plantas_id, pt.cantidad_t, pt.perdida, pt.fecha_traslado, p.nombre_comun 
                  FROM planta_traslado pt
                  INNER JOIN plantas p ON pt.plantas_id = p.id
                  WHERE pt.id = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$trasladoId]);
        $traslado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($traslado) {
            $plantaId = $traslado['plantas_id'];
            $cantidadTraslado = intval($traslado['cantidad_t']);
            $perdidaActual = intval($traslado['perdida']);
            $fechaTraslado = $traslado['fecha_traslado'];
            $nombreComun = $traslado['nombre_comun'];

            // Verificar que la pérdida no exceda la cantidad disponible
            if ($cantidadPerdida > $cantidadTraslado) {
                echo json_encode(["status" => "error", "message" => "La pérdida excede la cantidad disponible en el traslado."]);
                exit();
            }

            // Actualizar la cantidad y la pérdida en el traslado
            $nuevaCantidad = $cantidadTraslado - $cantidadPerdida;
            $nuevaPerdida = $perdidaActual + $cantidadPerdida;

            $updateQuery = "UPDATE planta_traslado 
                            SET cantidad_t = ?, perdida = ? 
                            WHERE id = ?";
            $updateStmt = $conexion->prepare($updateQuery);
            $updateStmt->execute([$nuevaCantidad, $nuevaPerdida, $trasladoId]);

            // Obtener o crear el registro en historial_perdida
            $selectHistorialQuery = "SELECT * FROM historial_perdida WHERE plantas_id = ?";
            $selectHistorialStmt = $conexion->prepare($selectHistorialQuery);
            $selectHistorialStmt->execute([$plantaId]);
            $historial = $selectHistorialStmt->fetch(PDO::FETCH_ASSOC);

            if ($historial) {
                // Si existe el registro, actualizar los valores
                $cantidadPerdidaActual = intval($historial['cantidad_perdida']);
                $cantidadEfectivaActual = intval($historial['cantidad_efectiva']);

                $nuevaCantidadPerdida = $cantidadPerdidaActual + $cantidadPerdida;
                $nuevaCantidadEfectiva = $cantidadEfectivaActual - $cantidadPerdida;

                $updateHistorialQuery = "UPDATE historial_perdida 
                                         SET cantidad_perdida = ?, cantidad_efectiva = ?, fecha_traslado = ?
                                         WHERE plantas_id = ?";
                $updateHistorialStmt = $conexion->prepare($updateHistorialQuery);
                $updateHistorialStmt->execute([$nuevaCantidadPerdida, $nuevaCantidadEfectiva, $fechaTraslado, $plantaId]);
            } else {
                // Si no existe el registro, insertar uno nuevo
                $insertHistorialQuery = "INSERT INTO historial_perdida 
                                         (cantidad_inicial, cantidad_perdida, cantidad_efectiva, plantas_id, fecha, nombre_comun, fecha_traslado) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertHistorialStmt = $conexion->prepare($insertHistorialQuery);
                $insertHistorialStmt->execute([
                    $cantidadTraslado, // cantidad_inicial
                    $cantidadPerdida,  // cantidad_perdida
                    $nuevaCantidad,    // cantidad_efectiva
                    $plantaId,         // plantas_id
                    $fechaPerdida,     // fecha
                    $nombreComun,      // nombre_comun
                    $fechaTraslado     // fecha_traslado
                ]);
            }

            echo json_encode(["status" => "success", "message" => "Pérdida registrada correctamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Traslado no encontrado."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al registrar la pérdida: " . $e->getMessage()]);
    }
}
?>