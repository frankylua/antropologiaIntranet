//Agregar grado académico
//se construye el contenedor para edit grado
function formGrado(contenedor,valorPorDefecto = null) {
    $(contenedor).append('<div class="row " id="grad_row"><div class="col-md-6 mb-3"><label class="form-label" for="grad_acad">Tipo Grado</label><select id="grad_acad" class="form-select grado"><option selected value="0">Seleccione tipo</option><option value="1">Pregrado</option><option value="2">Postgrado</option></select></div><div class="col-md-6 mb-3" id="grado_ac"><label class="form-label" for="">Grado Académico</label><select id="grado" class="form-select tit post" name="grado"><option value="0"></option></select></div></div>');
    $(contenedor).append('<div class="row" id="tit_row"><div class="col-md-6 mb-3" id="id_inst" name="0"><label class="form-label" for="grad_acad">Institución</label><select  class="form-select inst" id="inst_grado"></select></div><div class="col-md-6 mb-3" id="titulo" name="0"><label class="form-label" for="s_tit">Título Grado</label><select id="s_tit" class="form-select titulos"><option value="0"></option></select></div></div>');
    $(contenedor).append('<div class="row" ><div class="col-md-6 " id="institucion"></div><div class="col-md-6 " id="otro_titulo"></div></div>')
    $(contenedor).append('<div class="row"><div class="col-md-6"><label class="form-label" for="fech_grado">Fecha de Graduación</label><input type="date" class="form-control" id="fech_grado"></div></div>')
    $(contenedor).append(' <div class="row justify-content-center mt-3" id="mnsj_row_grad"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_grad"></div></div>')
    $.post('../ajax/institucion.php', {op:'read',tipo:'inst'}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#inst_grado')
        if (valorPorDefecto) {
            $('#inst_grado').val(valorPorDefecto).trigger('change');
            $('#inst_grado').attr('name', valorPorDefecto)
            if($('lista_doc')=='true'){
                btn_editar_acad('#campos_grado', $('#info_doc').attr('name'))
                editAcadDoc()
            }else{
                btn_editar_acad('#campos_grado', $('#info_est').attr('name'))
                editAcadEst() 
    }
        }
})

    $('#mnsj_row_grad').hide();
}
function listaGrados(id_tipo, id_grado) {
    if ($(id_tipo).val() == '1') {
        $(id_grado).html('<option selected value="0">Grado</option><option value="1">Licenciatura</option><option value="2">Título Universitario</option>');
    }
    if ($(id_tipo).val() == '2') {
        $(id_grado).html('<option selected value="0">Grado</option><option value="3">Magister</option><option value="4">Doctorado</option>');
    }
}
// function formEditGrado(){
//$('#edit_academicos').append('<h3 class="mb-5 text-center" id="text-tit">EDITAR GRADO ACADÉMICO</h3><form action="" id="form_edit_grado"></form>');
// }
$('#btn_grado').click(function () {
    $('#boton_grado').hide();
    $('#grado_academico').append('<div class="card mb-3" id="ingresar_grado"><div class="card-body" id="card_pre"> </div></div>');
    $('#card_pre').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Grado Académico</h4></div><div class="col-auto"><button type="button" name="add" id="borrar_grado"  class="btn btn-close btn-sm borrar_grado"></button></div></div>');
    formGrado('#card_pre');
    $('#card_pre').append('<div class="row justify-content-center" ><div class="col-md-6  d-grid gap-2"><button type="submit" class="btn btn-dark mt-3">Guardar Grado Académico</button></div></div>');


});
//otra institucion 
$(document).on('change', '.inst', function () {
    $('#input_inst').remove()
    otroCampo('input_inst', '#institucion', '#inst_grado', 'Nombre Institucion', '80')
    $('#id_inst').attr('name', 0)
})
//otro titulo
$(document).on('change', '.titulos', function () {
    $('#input_tit').remove()
    otroCampo('input_tit', '#otro_titulo', '#s_tit', 'Nombre titulo', '80')
    $('#titulo').attr('name', 0)
})
//Agregar grado y título
$(document).on('change', '.grado', function () {

    listaGrados('#grad_acad', '#grado')

});
$(document).on('change', '.tit', function () {
    if ($('#grado').val() == '1') {
        tipo = 'lic'
    }
    if ($('#grado').val() == '2') {
        tipo = 'un'
    }
    if ($('#grado').val() == '3') {
        tipo = 'mag'
    }
    if ($('#grado').val() == '4') {
        tipo = 'doc'
    }
      $.post('../ajax/titulo.php', {op:'read',tipo:tipo}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#s_tit')
})
})
//Eliminar Grado Académico  (eliminar el registro relcionado al id)
$(document).on('click', '#borrar_grado', function () {
    //id = $(this).attr("id");
    $('#ingresar_grado').remove();
    $('#boton_grado').show();
    //$('#grado_card'+id).fadeIn()
});
function cargarGrado(usuario, id) {
    op = 'read'
    $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/grado.php',
        data: { op, usuario },
        success: function (response) {
            let grados = JSON.parse(response);
            let conpre = 1
            let conpost = 1
            let template = '';
            grados.forEach(grado => {
                fecha = mostrarFecha(grado['4']);
                if (grado[3] == 1 || grado[3] == 2) {
                    tipo_grado = 'Pregrado';
                    con = conpre;
                    if (grado[3] == 1) {
                        grado_acad = 'Licenciatura'
                    }
                    if (grado[3] == 2) {
                        grado_acad = 'Titulo Universitario'
                    }
                } else {
                    con = conpost;
                    tipo_grado = 'Postgrado';
                    if (grado[3] == 3) {
                        grado_acad = 'Magister'
                    }
                    if (grado[3] == 4) {
                        grado_acad = 'Doctorado'
                    }


                }

                template += `
        <div class="col-12" id="cont-${grado['id_grado']}">
        <div class="card mb-3" id="grado_card${grado['id_grado']}">
            <div class="card-body">
                <table class="table table-striped">
                <thead>
                <tr >
                <th class="col-md-4 titulo_acad"><h5 >GRADO</h5></th>
                <th class="row justify-content-end ps-0 botones"><button type="button" class="col-auto btn btn-link link-success ps-1 editarGrado" id="${grado['id_grado']}">Editar</button><button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarGrado" id="${grado['id_grado']}">Eliminar</button></th>
                </tr>
                </thead>
                    <tbody>
                    <tr>
                            <td>Tipo Grado</td>
                            <td>${tipo_grado}</td>
                        </tr>
                        <tr>
                            <td>Grado Académico</td>
                            <td>${grado_acad}</td>
                        </tr>
                        <tr>
                            <td>Título</td>
                            <td>${cadenaMay(grado[2])}</td>
                        </tr>
                        <tr>
                            <td>Institución</td>
                            <td>${cadenaMay(grado[1])}</td>
                        </tr> 
                        <tr>
                        <td>Fecha de Graduación</td>
                        <td>${fecha}</td>
                    </tr>        
                    </tbody>
                </table>    
            </div>
        </div>  
        </div>                 
        `
                if (grado[3] == 1 || grado[3] == 2) {
                    conpre++
                }
                if (grado[3] == 3 || grado[3] == 4) {
                    conpost++
                }

            });
            $(id).append(template)
        }
    })
}
$("body").on("click", ".editarGrado", function () {
    id_grado = $(this).attr("id");
    console.log(id_grado)
    $('#edit_academicos').attr('name', id_grado)
    $('#campos_grado').append('<h3 class="mb-5 text-center" id="text-tit">EDITAR GRADO ACADÉMICO</h3>')    
    $.ajax({
        async: false,
        url: "../ajax/grado.php",
        type: "POST",
        data: { op: "read_grado_id", id_grado },
        success: function (response) {
            let grado = JSON.parse(response);
            formGrado('#campos_grado',grado[0]['inst_grado'])
            console.log(grado)
            tipo = grado[0]['tipo_grado']
            $('#grad_acad').val(tipo == 1 || tipo == 2 ? 1 : 2)
            $('#grad_acad').attr('name', tipo == 1 || tipo == 2 ? 1 : 2)
            $('#fech_grado').val(grado[0]['fech_graduacion'])
            $('#fech_grado').attr('name', grado[0]['fech_graduacion'])
            $('#grado').html('<option selected value="0">Seleccione</option')
            tipo_tit = tipo == 1 ? 'lic' : tipo == 2 ? 'un' : tipo == 3 ? 'mag' : 'doc'
            // ajaxSelect('#s_tit', ruta + 'ajax/titulo.php', 'Seleccione', 'read', tipo_tit)
            $.post('../ajax/titulo.php', {op:'read',tipo:tipo_tit}, function (response) {
                data = JSON.parse(response);
                templateSelect(data,'#s_tit')
                $('#s_tit').val(grado[0]['tit_grado'])
            })
            $('#s_tit').attr('name', grado[0]['tit_grado'])
            if (tipo == 1 || tipo == 2) {
                $('#grado').append('<option value="1">Licenciatura</option><option selected value="2">Título Universitario</option>')
                if (tipo == 1) {
                    $('#grado option[value="1"]').attr("selected", true)
                } else {
                    $('#grado option[value="2"]').attr("selected", true)
                }


            } else {
                $('#grado').html('<option value="3">Magister</option><option selected value="4">Doctorado</option>')
                tipo == 3 ? $('#grado option[value="3"]').attr("selected", true) : tipo == 4 ? $('#grado option[value="4"]').attr("selected", true) : ''
            }
        },
    });
});
$("body").on("click", ".eliminarGrado", function () {
    id_grado = $(this).attr("id");
    usu = $('#info_doc').attr('name')
    $.ajax({
        url: "../ajax/grado.php",
        type: "POST",
        data: { id_grado, op: "delete" },
        success: function (response) {
            let mensaje = JSON.parse(response);
            if ($("#tipo_usuario").attr('name') == 'prof') {
                if($('lista_doc')=='true'){
                rcargarFichaDoc($("#info_doc").attr('name'))
            }else{
                cargarFichaEst($("#info_doc").attr('name')) 
            }
                $("#mnsj_row_acad_doc").show();
                $("#mnsj_acad_doc").addClass("alert-success");
                $("#mnsj_acad_doc").html(mensaje);
                setTimeout(function () {
                    $("#mnsj_row_acad_doc").fadeOut(1500);
                }, 3000);
            } else if ($("#tipo_usuario").attr('name') == 'est') {
                cargarFichaEst($("#info_est").attr('name'))
                $("#mnsj_row_elim_acad_est").show();
                $("#mnsj_row_elim_acad_est").addClass("alert-success");
                $("#mnsj_elim_acad_est").html(mensaje);
                setTimeout(function () {
                    $("#mnsj_row_elim_acad_est").fadeOut(1500);
                }, 3000);
            }
        },
    });

});
$('#form_grado').submit(function (e) {
    e.preventDefault();
    $('#grados_card').html('')
    $('#grad_acad').click(function () { limpiarSelect('#grad_acad'); })
    $('#grado').click(function () { limpiarSelect('#grado'); })
    $('#inst_grado').click(function () { limpiarSelect('#inst_grado'); })
    $('#fech_grado').click(function () { limpiarInput('#fech_grado'); })
    $('#s_tit').click(function () { limpiarSelect('#s_tit'); })
    let tipo_grado = $('#grad_acad').val();
    let grado = $('#grado').val();
    let inst = $('#inst_grado').val() == 'otro' ? espacios($('#input_inst').val()) : $('#inst_grado').val();
    let titulo = $('#s_tit').val() == 'otro' ? guardar($('#input_tit').val()) : $('#s_tit').val();
    let usuario = $('#id_usuario').attr('name');
    let fecha = $('#fech_grado').val();
    if (validCampoVacio('#input_tit') || validCampoVacio('#input_inst') || fecha == '' || tipo_grado == '0' || grado == '0' || inst == '0' || titulo == '0') {
        validSelect('#grad_acad');
        validSelect('#grado');
        validSelect('#inst_grado');
        validSelect('#s_tit');
        validCampoVacio('#input_inst')
        validCampoVacio('#fech_grado')
        $('#mnsj_row_grad').show();
        $('#mnsj_grad').html('Rellene todos los campos')
    } else {
        nuevo_inst = $('#inst_grado').val() == 'otro' ? true : false;//agregar nuevo instituto
        nuevo_tit = $('#s_tit').val() == 'otro' ? true : false;//agregar nuevo titulo
        // en caso de ser "true" ingresar en tabla titulo y/o instituto y devolver id para guardar grado academico
        if (nuevo_inst) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/institucion.php',
                data: { op, nombre: inst },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#id_inst').attr('name', dato);
                }
            })
        }
        if (nuevo_tit) {
            op = 'insert';
            $.ajax({
                async: false,
                type: 'POST',
                url: '../ajax/titulo.php',
                data: { op, nombre: titulo, tipo_grado: grado },
                success: function (response) {
                    let dato = JSON.parse(response);
                    $('#titulo').attr('name', dato);
                }
            })
        }
        inst = $('#id_inst').attr('name') == 0 ? inst : $('#id_inst').attr('name')
        titulo = $('#titulo').attr('name') == 0 ? titulo : titulo = $('#titulo').attr('name')
        op = 'insert-update';
        grado_arr = { inst, titulo, fecha, usuario, op };
        console.log(grado_arr)
        $.post('../ajax/grado.php', grado_arr, function (response) {
            let dato = JSON.parse(response);
            $('#mnsj_row_grad').show();
            $('#mnsj_grad').removeClass('alert-danger');
            $('#mnsj_grad').addClass('alert-success');
            $('#mnsj_grad').html(dato);
            setTimeout(function () {
                $('#ingresar_grado').remove();
                $('#boton_grado').show();
                cargarGrado(usuario, '#grados_card')
                $('.editarGrado').hide()
                $("#mnsj_row_grad").fadeOut(1500);
            }, 3000);
        })

    }
})
$('#form_edit_grado').submit(function (e) {
    e.preventDefault();
    let id_grado = $('#edit_academicos').attr('name');
    let tipo_grado = $('#grad_acad').val();
    let grado = $('#grado').val() == 0 ? $('#grado').attr('name') : $('#grado').val();
    let inst = $('#inst_grado').val() == 'otro' ? espacios($('#input_inst').val()) : $('#inst_grado').val();
    inst = $('#inst_grado').val() == 0 ? $('#inst_grado').attr('name') : inst;
    let titulo = $('#s_tit').val() == 'otro' ? guardar($('#input_tit').val()) : $('#s_tit').val();
    titulo = $('#s_tit').val() == 0 ? $('#s_tit').attr('name') : titulo;
    let fecha = $('#fech_grado').val() == '' ? $('#fech_grado').attr('name') : $('#fech_grado').val();
    nuevo_inst = $('#inst_grado').val() == 'otro' ? true : false;//agregar nuevo instituto
    nuevo_tit = $('#s_tit').val() == 'otro' ? true : false;//agregar nuevo titulo
    // en caso de ser "true" ingresar en tabla titulo y/o instituto y devolver id para guardar grado academico
    if (nuevo_inst) {
        op = 'insert';
        $.ajax({
            async: false,
            type: 'POST',
            url: '../ajax/institucion.php',
            data: { op, nombre: inst },
            success: function (response) {
                let dato = JSON.parse(response);
                $('#id_inst').attr('name', dato);
            }
        })
    }
    if (nuevo_tit) {
        op = 'insert';
        $.ajax({
            async: false,
            type: 'POST',
            url: '../ajax/titulo.php',
            data: { op, nombre: titulo, tipo_grado: grado },
            success: function (response) {
                let dato = JSON.parse(response);
                $('#titulo').attr('name', dato);
            }
        })
    }
    inst = $('#id_inst').attr('name') == 0 ? inst : $('#id_inst').attr('name')
    titulo = $('#titulo').attr('name') == 0 ? titulo : titulo = $('#titulo').attr('name')
    op = 'insert-update';
    grado_arr = { inst, titulo, fecha, op, id_grado };
    console.log(grado_arr)
    $.post('../ajax/grado.php', grado_arr, function (response) {
        let dato = JSON.parse(response);
        $('#mnsj_row_grad').show();
        $('#mnsj_grad').removeClass('alert-danger');
        $('#mnsj_grad').addClass('alert-success');
        $('#mnsj_grad').html(dato);
        setTimeout(function () {
            if($('lista_doc')=='true'){
                reiniciarInfoDoc()
            }else{
                reiniciarInfoEst()
            }
            $("#mnsj_row_grad").fadeOut(1500);
        }, 3000);
    })
})