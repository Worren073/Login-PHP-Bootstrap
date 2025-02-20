$(document).ready(function() {
    $('#formPlanta_traslado').submit(function(e) {
        e.preventDefault(); // Previene el envío del formulario por defecto
        
        // Obtener los valores de los campos del formulario
        let planta_id = $('#plantas_id').val();
        let cantidad_tr = $('#cantidad_tr').val();
        let fecha_traslado = $('#fecha_traslado').val();
        let observ = $('#observ').val();
        let per_traslado = $('#per_traslado').val();

        // Validar que los campos necesarios no estén vacíos
        if (!planta_id |cantidad_tr| !fecha_traslado || !observ || !per_traslado) {
            alert("Por favor, complete todos los campos obligatorios.");
            return; // Detener el envío si falta algún campo
        }

        // Enviar los datos a través de AJAX
        $.ajax({
            url: '../db/registrar_traslado.php', // Ruta al script PHP que maneja el trasplante
            type: 'POST',
            dataType: 'json',
            data: {
                planta_id: planta_id,
                cantidad_tr: cantidad_tr,
                fecha_traslado: fecha_traslado,
                observ: observ,
                per_traslado: per_traslado
            },
            success: function(data) {
                if (data.status === 'success') {
                    alert(data.message); // Mostrar mensaje de éxito
                    $('#modalCRUD').modal('hide'); // Cerrar modal
                    location.reload(); // Recargar la tabla después de añadir
                } else {
                    alert("Error: " + data.message); // Mostrar mensaje de error si el servidor devuelve un error
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Log detallado del error
                alert("Hubo un error al registrar el trasplante. Intenta nuevamente."); // Mensaje de error para el usuario
            }
        });
    });
});

