<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$datos = json_decode($_POST['datos'], true);

foreach ($datos as $dato) {
    // Verificar si el registro ya existe
    $query = "
        SELECT COUNT(*) AS count
        FROM historial_perdida
        WHERE plantas_id = :plantas_id
        AND cantidad_inicial = :cantidad_inicial
        AND cantidad_perdida = :cantidad_perdida
        AND cantidad_efectiva = :cantidad_efectiva
        AND fecha_siembra = :fecha_siembra
        AND fecha_trasplante = :fecha_trasplante
        AND fecha_traslado = :fecha_traslado
    ";

    $stmt = $conexion->prepare($query);
    $stmt->execute([
        ':plantas_id' => $dato['plantas_id'],
        ':cantidad_inicial' => $dato['cantidad_inicial'],
        ':cantidad_perdida' => $dato['perdida_total'],
        ':cantidad_efectiva' => $dato['cantidad_efectiva'],
        ':fecha_siembra' => $dato['fecha_siembra'],
        ':fecha_trasplante' => $dato['fecha_trasplante'],
        ':fecha_traslado' => $dato['fecha_traslado']
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row['count'] == 0) {
        // Insertar solo si no existe el registro
        $query_insert = "
            INSERT INTO historial_perdida 
            (cantidad_inicial, cantidad_perdida, cantidad_efectiva, plantas_id, fecha, nombre_comun, fecha_siembra, fecha_trasplante, fecha_traslado)
            VALUES 
            (:cantidad_inicial, :cantidad_perdida, :cantidad_efectiva, :plantas_id, :fecha, :nombre_comun, :fecha_siembra, :fecha_trasplante, :fecha_traslado)
        ";

        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->execute([
            ':cantidad_inicial' => $dato['cantidad_inicial'],
            ':cantidad_perdida' => $dato['perdida_total'],
            ':cantidad_efectiva' => $dato['cantidad_efectiva'],
            ':plantas_id' => $dato['plantas_id'],
            ':fecha' => date('Y-m-d'),
            ':nombre_comun' => $dato['planta'],
            ':fecha_siembra' => $dato['fecha_siembra'],
            ':fecha_trasplante' => $dato['fecha_trasplante'],
            ':fecha_traslado' => $dato['fecha_traslado']
        ]);
    }
}

echo json_encode("Datos guardados correctamente");
?>
