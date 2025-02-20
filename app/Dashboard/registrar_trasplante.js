$(document).ready(function() {
    $('#formTrasplante').submit(function(e) {
        e.preventDefault(); // Previene el envío del formulario por defecto
        
        // Obtener los valores de los campos del formulario
        let planta_id = $('#plantas_id').val();
        let cantidad_t = $('#cantidad_t').val();
        let fecha_trasplante = $('#fecha_trasplante').val();
        let observacion = $('#observacion').val();
        let perdida_t = $('#perdida_t').val();

        // Validar que los campos necesarios no estén vacíos
        if (!planta_id || !cantidad_t || !fecha_trasplante || !observacion) {
            alert("Por favor, complete todos los campos obligatorios.");
            return; // Detener el envío si falta algún campo
        }

        // Enviar los datos a través de AJAX
        $.ajax({
            url: '../db/registrar_trasplante.php', // Ruta al script PHP que maneja el trasplante
            type: 'POST',
            dataType: 'json',
            data: {
                planta_id: planta_id,
                cantidad_t: cantidad_t,
                fecha_trasplante: fecha_trasplante,
                observacion: observacion,
                perdida_t: perdida_t
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

