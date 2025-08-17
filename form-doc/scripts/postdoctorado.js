// agregar postdoctorado
function formPostdoc(contenedor) {
    $(contenedor).append('<div class="row " id="postdoc_row"><div class="col-md-6 mb-3"><label class="form-label" for="prof_postdoc">Profesor/a Patrocinante</label><input class="form-control" type="text" id="prof_postdoc" maxlength="60"></div><div class="col-md-6 mb-3" id="id_instpostdoc" name="0"><label class="form-label" for="inst_postdoc">Institución</label><select id="inst_postdoc" class="form-select"></select></div></div>');
    $(contenedor).append('<div class="row "> <div class="col-md-6 mb-3"><label for="fech_in_postdoc" class="form-label">Fecha Inicio</label><input type="date" class="form-control" id="fech_in_postdoc" ></div><div class="col-md-6 mb-3"><label for="fech_ter_postdoc" class="form-label">Fecha Término</label><input type="date" class="form-control" id="fech_ter_postdoc" name="fecha_ter"> </div></div>');
    $(contenedor).append(' <div class="row justify-content-center " id="mnsj_row_postdoc"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_postdoc"></div></div>')
    $('#mnsj_row_postdoc').hide();
     $.post('../ajax/institucion.php', {op:'read'}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#inst_postdoc')
        })

}
$('#btn_postdoc').click(function () {
    $('#boton_postdoc').hide();
    $('#postdoctorado').append('<div class="card mb-3" id="ingresar_postdoc"><div class="card-body" id="card_postdoc"> </div></div>');
    $('#card_postdoc').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Postdoctorado</h4></div><div class="col-auto"><button type="button" name="add" id="close_postdoc"  class="btn btn-close btn-sm"></button></div></div>');
    formPostdoc('#card_postdoc')
    $('#card_postdoc').append('<div class="row justify-content-center" ><div class="col-md-6  d-grid gap-2"><button type="submit" class="btn btn-dark mt-3">Guardar Postdoctorado</button></div></div>');
});
$(document).on('change', '#inst_postdoc', function () {
    $('#input_instpostdoc').remove();
    if ($('#inst_postdoc').val() == 'otro') {
        $('#postdoc_row').append('<div class="col-md-6 mb-3" id="input_instpostdoc"><input type="text" class="form-control" id="nuevo_inst" placeholder="Institución" maxlength="80"></div>');
    }
});
//Eliminar Postdoctorado
$(document).on('click', '#close_postdoc', function () {
    $('#ingresar_postdoc').remove();
    $('#boton_postdoc').show();
});

function cargarPostdoc(usuario, id) {
    op = 'read'
    $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/postdoctorado.php',
        data: { op, usuario },
        success: function (response) {
            let postdocs = JSON.parse(response);
            let template = '';
            let con = 1
            postdocs.forEach(postdoc => {
                template += `
        <div class="col-12">
        <div class="card mb-3 grado_card">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr >
                <th class="col-md-4 titulo_acad"><h5>POSTDOCTORADO</h5></th>
                <th class="row justify-content-end ps-0 botones"><button type="button" class="col-auto btn btn-link link-success ps-1 editarPostdoc" id="${postdoc['id_postdoc']}">Editar</button><button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarPostdoc" id="${postdoc['id_postdoc']}">Eliminar</button></th>
                </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>Profesor/a Patrocinante</td>
                            <td>${cadenaMay(postdoc['prof'])}</td>
                        </tr>
                        <tr>
                            <td>Institución</td>
                            <td>${cadenaMay(postdoc['inst'])}</td>
                        </tr>
                        <tr>
                            <td>Fecha Inicio</td>
                            <td>${mostrarFecha(postdoc[2])}</td>
                        </tr> 
                        <tr>
                        <td>Fecha de Término</td>
                        <td>${mostrarFecha(postdoc[3])}</td>
                    </tr>        
                    </tbody>
                </table>    
            </div>
        </div>  
        </div>                 
        `
                con++
            });
            $(id).append(template);

        }
    })
}
function leerDatosPostdoc() {
    let prof = guardar($('#prof_postdoc').val());
    let inst = $('#inst_postdoc').val() == 'otro' ? espacios($('#nuevo_inst').val()) : $('#inst_postdoc').val();
    let fech_in = $('#fech_in_postdoc').val();
    let fech_ter = $('#fech_ter_postdoc').val();
    let usuario = $('#id_usuario').attr('name');
    return { prof, inst, fech_in, fech_ter, usuario }
}
$("body").on("click", ".editarPostdoc", function () {
    id_postdoc = $(this).attr("id");
    $('#edit_academicos').attr('name', id_postdoc)
    $('#campos_postdoc').append('<h3 class="mb-5 text-center" id="text-tit">EDITAR POSTDOCTORADO</h3>')
    formPostdoc('#campos_postdoc')
    btn_editar_acad('#campos_postdoc', $('#info_doc').attr('name'))
    editAcadDoc()
    $.ajax({
        async: false,
        url: "../ajax/postdoctorado.php",
        type: "POST",
        data: { op: "read_postdoc_id", id_postdoc },
        success: function (response) {
            let postdoc = JSON.parse(response);
            $('#prof_postdoc').val(cadenaMay(postdoc[0]['prof']))
            $('#prof_postdoc').attr('name', cadenaMay(postdoc[0]['prof']))
            $('#inst_postdoc').val(postdoc[0]['inst_postdoc'])
            $('#inst_postdoc').attr('name', postdoc[0]['inst_postdoc'])
            $('#fech_in_postdoc').val(postdoc[0]['fecha_inicio'])
            $('#fech_in_postdoc').attr('name', postdoc[0]['fecha_inicio'])
            $('#fech_ter_postdoc').val(postdoc[0]['fecha_termino'])
            $('#fech_ter_postdoc').attr('name', postdoc[0]['fecha_termino'])
        },
    });
});
$("body").on("click", ".eliminarPostdoc", function () {
    id_postdoc = $(this).attr("id");
    usu = $('#info_doc').attr('name')
    $.ajax({
        url: "../ajax/postdoctorado.php",
        type: "POST",
        data: { id_postdoc, op: "delete" },
        success: function (response) {
            cargarFichaDoc(usu);
            let mensaje = JSON.parse(response);
            $("html, body").animate({ scrollTop: $('#ant_acad_doc').offset().top }, 100);
            $("#mnsj_row_acad_doc").show();
            $("#mnsj_acad_doc").addClass("alert-success");
            $("#mnsj_acad_doc").html(mensaje);
            setTimeout(function () {
                $("#mnsj_row_acad_doc").fadeOut(1500);
            }, 3000);
        },
    });

});
$('#form_postdoc').submit(function (e) {
    e.preventDefault();
    $('#prof_postdoc').click(function () { limpiarSelect('#prof_postdoc'); })
    $('#inst_postdoc').click(function () { limpiarSelect('#inst_postdoc'); })
    $('#fech_in_postdoc').click(function () { limpiarSelect('#fech_in_postdoc'); })
    $('#fech_ter_postdoc').click(function () { limpiarSelect('#fech_ter_postdoc'); })
    lista = leerDatosPostdoc()
    if (lista['prof'] == '' || lista['fech_in'] == '' || lista['inst'] == '0' || lista['fech_ter'] == '0') {
        validCampoVacio('#prof_postdoc');
        validSelect('#inst_postdoc');
        validCampoVacio('#fech_in_postdoc');
        validCampoVacio('#fech_ter_postdoc');
        $('#mnsj_row_postdoc').show();
        $('#mnsj_postdoc').html('Rellene todos los campos requeridos')
    } else {
        nuevo_inst = $('#inst_postdoc').val() == 'otro' ? true : false;
        if (nuevo_inst) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/institucion.php',
                data: { op, nombre: lista['inst'] },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#id_instpostdoc').attr('name', dato);
                }
            })
        }
        if ($('#id_instpostdoc').attr('name') != 0) {
            lista['inst'] = $('#id_instpostdoc').attr('name')
        }
        lista.op = 'insert';
        $.post('../ajax/postdoctorado.php', lista, function (response) {
            let dato = JSON.parse(response);
            $('#mnsj_row_postdoc').show();
            $('#mnsj_postdoc').removeClass('alert-danger');
            $('#mnsj_postdoc').addClass('alert-success');
            $('#mnsj_postdoc').html(dato);
            setTimeout(function () {
                $('#ingresar_postdoc').remove();
                $('#boton_postdoc').show();
                cargarPostdoc(lista['usuario'], '#postdoc_card')
                $("#mnsj_row_postdoc").fadeOut(1500);
            }, 3000);
        })

    }
})
$('#form_edit_postdoc').submit(function (e) {
    e.preventDefault();
    let datos_postdoc = leerDatosPostdoc()
    //validar campos vacios (retorna valor base de datos)
    datos_postdoc['inst'] = $('#inst_postdoc').val() == 0 ? $('#inst_postdoc').attr('name') : datos_postdoc['inst'];
    datos_postdoc['prof'] = $('#prof_postdoc').val() == '' ? $('#prof_postdoc').attr('name') : datos_postdoc['prof'];
    datos_postdoc['fech_in'] = $('#fech_in_postdoc').val() == '' ? $('#fech_in_postdoc').attr('name') : datos_postdoc['fech_in'];
    datos_postdoc['fech_ter'] = $('#fech_ter_postdoc').val() == '' ? $('#fech_ter_postdoc').attr('name') : datos_postdoc['fech_ter'];
    nuevo_inst = $('#inst_postdoc').val() == 'otro' ? true : false;
    if (nuevo_inst) {
        op = 'insert';
        $.ajax({
            async: false,
            type: 'POST',
            url: '../ajax/institucion.php',
            data: { op, nombre: datos_postdoc['inst'] },
            success: function (response) {
                let dato = JSON.parse(response);
                $('#id_instpostdoc').attr('name', dato);
            }
        })
    }
    datos_postdoc['inst'] = $('#id_instpostdoc').attr('name') == 0 ? datos_postdoc['inst'] : $('#id_instpostdoc').attr('name')
    datos_postdoc.op = 'update';
    datos_postdoc.id_postdoc = $('#edit_academicos').attr('name')
    console.log(datos_postdoc)
    $.post('../ajax/postdoctorado.php', datos_postdoc, function (response) {
        let dato = JSON.parse(response);
        $('#mnsj_row_postdoc').show();
        $('#mnsj_postdoc').removeClass('alert-danger');
        $('#mnsj_postdoc').addClass('alert-success');
        $('#mnsj_postdoc').html(dato);
        setTimeout(function () {
            reiniciarInfoDoc()
            $('#campos_postdoc').html('')
            $("#mnsj_row_postdoc").fadeOut(1500);
        }, 3000);
    })
})
//cargarPostdoc($('#id_usuario').attr('name'))

