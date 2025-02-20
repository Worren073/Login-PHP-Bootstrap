<?php
session_start();

if (!isset($_SESSION["s_username"])) {
    die("Acceso no autorizado.");
}

include 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $planta_id = $_POST['planta_id'] ?? '';
    $cant_autoconsumo = $_POST['cant_autoconsumo'] ?? '';
    $observ_consumo = $_POST['observ_consumo'] ?? '';
    $fecha_consumo = $_POST['fecha_consumo'] ?? '';

    // Validar los datos
    if (empty($planta_id) || empty($cant_autoconsumo) || empty($observ_consumo) || empty($fecha_consumo)) {
        echo '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
        exit;
    }

    try {
        // Iniciar una transacción
        $conexion->beginTransaction();

        // 1. Verificar que la planta_id exista en la tabla plantas
        $query_check = "SELECT id FROM plantas WHERE id = :planta_id";
        $stmt_check = $conexion->prepare($query_check);
        $stmt_check->bindParam(':planta_id', $planta_id, PDO::PARAM_INT);
        $stmt_check->execute();

        if ($stmt_check->rowCount() == 0) {
            throw new Exception("La planta seleccionada no existe.");
        }

        // 2. Insertar el registro de autoconsumo
        $query_insert = "INSERT INTO autoconsumo (plantas_id, cant_autoconsumo, observ_consumo, fecha_consumo)
                         VALUES (:planta_id, :cant_autoconsumo, :observ_consumo, :fecha_consumo)";
        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->bindParam(':planta_id', $planta_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':cant_autoconsumo', $cant_autoconsumo, PDO::PARAM_INT);
        $stmt_insert->bindParam(':observ_consumo', $observ_consumo, PDO::PARAM_STR);
        $stmt_insert->bindParam(':fecha_consumo', $fecha_consumo, PDO::PARAM_STR);

        if (!$stmt_insert->execute()) {
            throw new Exception("Error al registrar el autoconsumo.");
        }

        // 3. Restar la cantidad consumida de la cantidad en traslado
        $query_update = "UPDATE planta_traslado 
                         SET cantidad_t = cantidad_t - :cant_autoconsumo 
                         WHERE plantas_id = :planta_id";
        $stmt_update = $conexion->prepare($query_update);
        $stmt_update->bindParam(':cant_autoconsumo', $cant_autoconsumo, PDO::PARAM_INT);
        $stmt_update->bindParam(':planta_id', $planta_id, PDO::PARAM_INT);

        if (!$stmt_update->execute()) {
            throw new Exception("Error al actualizar la cantidad en traslado.");
        }

        // Commit de la transacción
        $conexion->commit();

        echo '<div class="alert alert-success">Autoconsumo registrado exitosamente.</div>';
    } catch (PDOException $e) {
        $conexion->rollBack();
        echo '<div class="alert alert-danger">Error al registrar el autoconsumo: ' . htmlspecialchars($e->getMessage()) . '</div>';
    } catch (Exception $e) {
        $conexion->rollBack();
        echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-warning">Acceso no autorizado.</div>';
}
?>