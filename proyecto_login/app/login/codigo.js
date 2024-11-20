$('#formlogin').submit(function(e) {

    e.preventDefault()
    var username = $.trim($("#username").val());
    var password = $.trim($("#password").val());

    if (username.length == "" || password == "" ) {
        Swal.fire({
            icon:'warning',
            title:'Debe ingresar un usuario y/o contraseña',
        });
        return false;
    } else {
        $.ajax({
            url:"../db/login.php" ,
            type:"POST",
            datatype:"json",
            data: {username:username, password:password},
            success: function(data){
                if(data == "null"){
                    Swal.fire({
                        icon:'error',
                        title:'Usuario y/o contraseña incorrectos',
                    });
                }else{
                    Swal.fire({
                        icon:'success',
                        title:'La conexión ha sido exitosa',
                        text: "Bienvenid@",
                        confirmButtonText:"Ingresar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "pag_inicio.php";
                        }
                    });
                    
                }
            }
        });
    }
});