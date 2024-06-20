$('#formregister').submit(function(e) {
    e.preventDefault();
    var username = $.trim($("#username").val());
    var password = $.trim($("#password").val());

    if (username.length === 0 || password.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Todos los campos son obligatorios',
        });
        return false;
    } else {
        $.ajax({
            url: "../db/registro.php",
            type: "POST",
            dataType: "json",
            data: { username: username, password: password },
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro exitoso',
                        confirmButtonColor: "white",
                        confirmButtonText: "Ingresar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "index.php";
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la comunicación con el servidor',
                    text: 'Por favor, intente de nuevo más tarde.'
                });
            }
        });
    }
});