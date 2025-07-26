//Agregar Pasantia
$('#btn_pasantia').click(function () {
    $('#boton_pasantia').hide();
    $('#pasantia').append('<div class="card mb-3" id="ingresar_pasantia"><div class="card-body" id="card_pasant"> </div></div>');
    $('#card_pasant').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Pasantía</h4></div><div class="col-auto"><button type="button"  id=""  class="btn btn-close btn-sm borrar_pasantia"></button></div></div>');
    $('#card_pasant').append('<div class="row" id="row_inst_pasant"><div class="col-md-6 mb-3" id="id_inst_pasant" name="0"><label for="inst" class="form-label">Institución</label><select class="form-select" id="inst_pasant"></select></div><div class="col-md-6 mb-3"><label for="prof_pat" class="form-label">Profesor/a Patrocinante</label><input type="text" class="form-control" id="prof_pat" maxlength="45"></div></div>');
    $('#card_pasant').append('<div class="row"><div class="col-md-6 mb-3"><label for="fondo" class="form-label">Fondo</label><input type="text" class="form-control" id="fondo" maxlength="45"></div><div class="col-md-6 mb-3"><label for="ciud_pasant" class="form-label">Ciudad</label><input type="text" class="form-control" id="ciud_pasant" maxlength="45"></div></div>');
    $('#card_pasant').append('<div class="row"><div class="col-md-4"><label for="pais_pasant" class="form-label pais_pa">País</label><select class="form-select" id="pais_pasant"></select></div><div class="col-md-4 mb-3"><label for="fech_in_pasant" class="form-label">Fecha Inicio</label><input type="date" class="form-control" id="fech_in_pasant"></div><div class="col-md-4 mb-3"><label for="fech_ter_pasant" class="form-label">Fecha Término</label><input type="date" class="form-control" id="fech_ter_pasant"> </div></div></div>');
    $('#card_pasant').append(' <div class="row justify-content-center " id="mnsj_row_pasant"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_pasant"></div></div>')
    $('#card_pasant').append('<div class="row justify-content-center"  ><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Pasantia</button></div></div>');
    ajaxSelect('#inst_pasant', ruta + 'ajax/institucion.php', 'Seleccione', 'read');
    ajaxSelect('#pais_pasant', ruta + 'ajax/pais.php', 'Seleccione', 'pais');
    $('#mnsj_row_pasant').hide();
});
$(document).on('change', '#inst_pasant', function () {
    $('#i_p').remove();
    if ($('#inst_pasant').val() == 'otro') {
        $('#row_inst_pasant').append('<div class="col-md-6" id="i_p"><input type="text" class="form-control mb-3" placeholder="Institución" id="nueva_inst_pasant" maxlength="80"></div>');
    }
})
//Eliminar Pasantia
$(document).on('click', '.borrar_pasantia', function () {
    $('#ingresar_pasantia').remove();
    $('#boton_pasantia').show();
});

function cargarPasantia(usuario, id) {
    op = 'read'
    $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/pasantia.php',
        data: { op, usuario },
        success: function (response) {
            let pasant = JSON.parse(response);
            let template = '';
            con = 1
            pasant.forEach(pas => {
                template += `
        <div class="card mb-3 grado_card">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr >
                <th class="col-md-4 titulo_acad"><h5>PASANTÍA</h5></th>
                <th class="ps-0"><button type="button" class="btn btn-link ps-1 link-secondary" id="${pas['id_pasantia']}">Editar Pasantía</button></th>
                </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>Profesor/a Patrocinante</td>
                            <td>${cadenaMay(pas['prof_patr'])}</td>
                        </tr>
                        <tr>
                            <td>Institución</td>
                            <td>${pas['inst']}</td>
                        </tr>
                        <tr>
                            <td>Fecha Inicio</td>
                            <td>${mostrarFecha(pas['fech_in'])}</td>
                        </tr> 
                        <tr>
                        <td>Fecha de Término</td>
                        <td>${mostrarFecha(pas['fech_ter'])}</td>
                    </tr> 
                    <tr>
                        <td>Fondo</td>
                        <td>${cadenaMay(pas['fondo'])}</td>
                    </tr> 
                    <tr>
                        <td>Ciudad</td>
                        <td>${cadenaMay(pas['ciudad'])}</td>
                    </tr>
                    <tr>
                        <td>País</td>
                        <td>${letraMay(pas['pais'])}</td>
                    </tr>       
                    </tbody>
                </table>    
            </div>
        </div>                   
        `
                con++
            });

            $(id).append(template);

        }
    })
}
$('#form_pasantia').submit(function (e) {
    e.preventDefault();
    $('#inst_pasant').click(function () { limpiarSelect('#inst_pasant'); })
    $('#prof_pat').click(function () { limpiarInput('#prof_pat'); })
    $('#fondo').click(function () { limpiarInput('#fondo'); })
    $('#ciud_pasant').click(function () { limpiarInput('#ciud_pasant'); })
    $('#pais_pasant').click(function () { limpiarSelect('#pais_pasant'); })
    $('#fech_in_pasant').click(function () { limpiarInput('#fech_in_pasant'); })
    $('#fech_ter_pasant').click(function () { limpiarInput('#fech_ter_pasant'); })
    let prof = guardar($('#prof_pat').val());
    let inst = $('#inst_pasant').val() == 'otro' ? $('#nueva_inst_pasant').val() : $('#inst_pasant').val();
    let fondo = guardar($('#fondo').val());
    let ciudad = guardar($('#ciud_pasant').val());
    let pais = $('#pais_pasant').val();
    let fecha_in = $('#fech_in_pasant').val();
    let fecha_ter = $('#fech_ter_pasant').val();
    let usuario = $('#id_usuario').attr('name');

    if (prof == '' || fondo == '' || inst == '0' || fecha_in == '' || fecha_ter == '' || ciudad == '' || pais == '0') {
        validCampoVacio('#prof_pat');
        validSelect('#inst_pasant');
        validCampoVacio('#fondo');
        validCampoVacio('#ciud_pasant');
        validSelect('#pais_pasant');
        validCampoVacio('#fech_in_pasant')
        validCampoVacio('#fech_ter_pasant')
        $('#mnsj_row_pasant').show();
        $('#mnsj_pasant').html('Rellene todos los campos requeridos')
    } else {
        nuevo_inst = $('#inst_pasant').val() == 'otro' ? true : false;//agregar nuevo instituto
        if (nuevo_inst) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/institucion.php',
                data: { op, nombre: inst },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#id_inst_pasant').attr('name', dato);
                }
            })
        }
        if ($('#id_inst_pasant').attr('name') != 0) {
            inst = $('#id_inst_pasant').attr('name')
        }
        op = 'insert-update';
        pasantia = { prof, inst, fondo, ciudad, pais, fecha_in, fecha_ter, usuario, op };
        $.post('../ajax/pasantia.php', pasantia, function (response) {
            let dato = JSON.parse(response);
            $('#mnsj_row_pasant').show();
            $('#mnsj_pasant').removeClass('alert-danger');
            $('#mnsj_pasant').addClass('alert-success');
            $('#mnsj_pasant').html(dato);
            setTimeout(function () {
                $('#ingresar_pasantia').remove();
                $('#boton_pasantia').show();
                // $('.pasantia_card').remove();
                cargarPasantia(usuario, '#pasantia_card')
                $("#mnsj_row_pasant").fadeOut(1500);
            }, 3000);
        })

    }
})

