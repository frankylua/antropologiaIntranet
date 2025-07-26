


$('#form_ingr').submit(function (e) {
    e.preventDefault();
    let correo = $('#correo_login').val();
    let pass = $('#pass_login').val();
    const login = {
        correo: correo,
        pass: pass
    }
    console.log(login)
    $.post('../ajax/login.php', login, function (response) {
        mensaje = JSON.parse(response);
        console.log(mensaje)
        if (mensaje.length == 0) {
            mensaje = 'La contraseña o el usuario son incorrectos'
            $('#mnsj_row_login').show();
            $('#mnsj_login').removeClass('alert-success');
            $('#mnsj_login').addClass('alert-danger');
            $('#mnsj_login').html(mensaje);
            setTimeout(function () {
                $('#mnsj_row_login').hide();
                $("#mnsj_row_login").fadeOut(1500);
            }, 3000);
        }
        else {
            location.href = '../index.php';  
        }


    })
})

$('#ingreso').click(function () {
    $('input:radio[name=tipo-ingreso]:checked').val() == 1 ? location.href = 'docente.php' : location.href = 'estudiante.php';

    //location.href='ingr.dat.pers.php?tipo='+tipo_ingreso;
})
$('#mnsj_row_login').hide();





