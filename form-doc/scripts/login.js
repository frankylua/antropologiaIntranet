let temporizadorErrorLogin;
let temporizadorEsperaLogin;
let ingresoDisponibleDesde = 0;

function mostrarErrorLogin(mensaje) {
    if (Date.now() < ingresoDisponibleDesde) return;
    clearTimeout(temporizadorErrorLogin);
    $('#mnsj_row_login').show();
    $('#mnsj_login').removeClass('alert-success');
    $('#mnsj_login').addClass('alert-danger');
    $('#mnsj_login').text(mensaje);
    temporizadorErrorLogin = setTimeout(function () {
        $('#mnsj_row_login').hide();
        $('#mnsj_row_login').fadeOut(1500);
    }, 3000);
}

function mostrarEsperaLogin(segundos) {
    clearTimeout(temporizadorErrorLogin);
    clearInterval(temporizadorEsperaLogin);
    const recibido = Number(segundos);
    const espera = Number.isFinite(recibido) && recibido > 0 && recibido <= 3600 ? Math.ceil(recibido) : 60;
    ingresoDisponibleDesde = Date.now() + espera * 1000;
    $('#form_ingr :submit').prop('disabled', true);
    function actualizarEspera() {
        const restante = Math.max(0, Math.ceil((ingresoDisponibleDesde - Date.now()) / 1000));
        $('#mnsj_row_login').stop(true, true).show();
        $('#mnsj_login').removeClass('alert-success').addClass('alert-danger');
        if (restante === 0) {
            clearInterval(temporizadorEsperaLogin);
            $('#form_ingr :submit').prop('disabled', false);
            $('#mnsj_login').text('Ya puede volver a intentar ingresar.');
            return;
        }
        $('#mnsj_login').text('Demasiados intentos. Espere ' + restante + ' segundos antes de volver a ingresar.');
    }
    actualizarEspera();
    temporizadorEsperaLogin = setInterval(actualizarEspera, 1000);
}

$('#form_ingr').submit(function (e) {
    e.preventDefault();
    if (Date.now() < ingresoDisponibleDesde) return;

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
        if (request.status === 429 || error === 'AUTH_RATE_LIMITED') {
            mostrarEsperaLogin(request.responseJSON && request.responseJSON.retry_after);
            return;
        }
        if (request.status === 503 || error === 'AUTH_THROTTLE_UNAVAILABLE') {
            mostrarErrorLogin('El ingreso no está disponible temporalmente. Intente nuevamente más tarde.');
            return;
        }
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
