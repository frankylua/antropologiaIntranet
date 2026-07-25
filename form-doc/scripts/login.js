function mostrarErrorLogin(mensaje) {
    $('#mnsj_row_login').show();
    $('#mnsj_login').removeClass('alert-success');
    $('#mnsj_login').addClass('alert-danger');
    $('#mnsj_login').html(mensaje);
    setTimeout(function () {
        $('#mnsj_row_login').hide();
        $('#mnsj_row_login').fadeOut(1500);
    }, 3000);
}

$('#form_ingr').submit(function (e) {
    e.preventDefault();

    const login = {
        correo: $('#correo_login').val(),
        pass: $('#pass_login').val()
    };

    $.ajax({
        url: '../ajax/login.php',
        method: 'POST',
        data: login,
        dataType: 'json'
    }).done(function (response) {
        if (Array.isArray(response) && response.length > 0) {
            location.href = '../index.php';
            return;
        }

        mostrarErrorLogin('La contraseña o el usuario son incorrectos');
    }).fail(function (request) {
        const error = request.responseJSON && request.responseJSON.error;
        if (request.status === 401 || error === 'AUTHENTICATION_FAILED') {
            mostrarErrorLogin('La contraseña o el usuario son incorrectos');
            return;
        }
        if (request.status === 403 || error === 'AUTH_CONTEXT_PERMISSIONS_EMPTY') {
            mostrarErrorLogin('La cuenta no tiene permisos de acceso');
            return;
        }

        mostrarErrorLogin('No fue posible completar el ingreso');
    });
});

$('#ingreso').click(function () {
    $('input:radio[name=tipo-ingreso]:checked').val() == 1
        ? location.href = 'docente.php'
        : location.href = 'estudiante.php';
});

$('#mnsj_row_login').hide();
