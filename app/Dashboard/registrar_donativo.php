<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if (!isset($_SESSION["s_username"])) {
    header("Location: ../login/index.php");
    exit();
}

include '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Inicializar variables para el mensaje
$response = array('success' => false, 'message' => '', 'pdf_link' => null);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recolección de datos del formulario
    $beneficiario = $_POST['beneficiario'] ?? '';
    $documento = $_POST['documento'] ?? '';
    $tipo_donativo = $_POST['tipo_donativo'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $donante = $_POST['donante'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';

    // Campos específicos del tipo de donativo
    $plantas_id = $_POST['planta_id'] ?? null;
    $abono_id = $_POST['abono_id'] ?? null;
    $humus_id = $_POST['humus_id'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;

    try {
        // Iniciar transacción
        $conexion->beginTransaction();

        // Validar que la cantidad sea mayor a cero
        if ($cantidad <= 0) {
            throw new Exception("La cantidad donada debe ser mayor a cero.");
        }

        // Validar que se haya seleccionado un ítem si es Planta, Abono o Humus
        if (($tipo_donativo === 'Planta' && empty($plantas_id)) ||
            ($tipo_donativo === 'Abono' && empty($abono_id)) ||
            ($tipo_donativo === 'Humus' && empty($humus_id))) {
            throw new Exception("Debe seleccionar un elemento para el tipo de donativo seleccionado.");
        }

        // Insertar el donativo
        $query_insert = "INSERT INTO donaciones (
                            beneficiario, documento, tipo_donativo, cantidad, donante, observaciones,
                            plantas_id, abono_id, humus_id, descripcion, fecha_donativo
                          ) VALUES (
                            :beneficiario, :documento, :tipo_donativo, :cantidad, :donante, :observaciones,
                            :plantas_id, :abono_id, :humus_id, :descripcion, NOW()
                          )";

        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->bindParam(':beneficiario', $beneficiario);
        $stmt_insert->bindParam(':documento', $documento);
        $stmt_insert->bindParam(':tipo_donativo', $tipo_donativo);
        $stmt_insert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt_insert->bindParam(':donante', $donante);
        $stmt_insert->bindParam(':observaciones', $observaciones);
        $stmt_insert->bindParam(':plantas_id', $plantas_id, $plantas_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt_insert->bindParam(':abono_id', $abono_id, $abono_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt_insert->bindParam(':humus_id', $humus_id, $humus_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt_insert->bindParam(':descripcion', $descripcion);

        if ($stmt_insert->execute()) {
            $donaciones_id = $conexion->lastInsertId();
            $response['pdf_link'] = "generar_pdf.php?id=" . $donaciones_id;

            // Actualizar stock si es necesario
            $query_update = null;
            switch ($tipo_donativo) {
                case 'Planta':
                    $query_update = "UPDATE planta_traslado SET cantidad_t = cantidad_t - :cantidad WHERE plantas_id = :plantas_id AND cantidad_t >= :cantidad";
                    break;
                case 'Abono':
                    $query_update = "UPDATE abono SET kil_os = kil_os - :cantidad WHERE id = :abono_id AND kil_os >= :cantidad";
                    break;
                case 'Humus':
                    $query_update = "UPDATE humus SET cantidad_h = cantidad_h - :cantidad WHERE id = :humus_id AND cantidad_h >= :cantidad";
                    break;
            }

            if ($query_update) {
                $stmt_update = $conexion->prepare($query_update);
                $stmt_update->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);

                if ($tipo_donativo === 'Planta') {
                    $stmt_update->bindParam(':plantas_id', $plantas_id, PDO::PARAM_INT);
                } elseif ($tipo_donativo === 'Abono') {
                    $stmt_update->bindParam(':abono_id', $abono_id, PDO::PARAM_INT);
                } elseif ($tipo_donativo === 'Humus') {
                    $stmt_update->bindParam(':humus_id', $humus_id, PDO::PARAM_INT);
                }

                if ($stmt_update->execute()) {
                    if ($stmt_update->rowCount() > 0) {
                        // Éxito: Donativo registrado y stock actualizado
                        $conexion->commit();
                        $response['success'] = true;
                        $response['message'] = "¡Donativo registrado y cantidad actualizada!";
                    } else {
                        // Error: No hay suficiente stock
                        $conexion->rollBack();
                        $response['success'] = false; // Asegurar que success sea false
                        $response['message'] = "No hay suficiente cantidad disponible para este donativo.";
                    }
                } else {
                    // Error al actualizar el stock
                    $conexion->rollBack();
                    $response['success'] = false; // Asegurar que success sea false
                    $response['message'] = "¡Ups! Hubo un problema al actualizar la cantidad. El donativo no se ha registrado.";
                }
            } else {
                // Éxito: Donativo registrado sin actualización de stock
                $conexion->commit();
                $response['success'] = true;
                $response['message'] = "¡Donativo registrado! (No se requirió actualización de cantidad)";
            }
        } else {
            // Error al insertar el donativo
            $conexion->rollBack();
            $response['success'] = false; // Asegurar que success sea false
            $response['message'] = "¡Oh no! No pudimos registrar el donativo. Por favor, verifica los datos e inténtalo de nuevo.";
        }

    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        $response['success'] = false; // Asegurar que success sea false
        $response['message'] = $e->getMessage(); // Captura el mensaje de la excepción
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        $response['success'] = false; // Asegurar que success sea false
        $response['message'] = "¡Error crítico! Algo salió muy mal en la base de datos. Por favor, contacta a soporte técnico. Detalle: " . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
