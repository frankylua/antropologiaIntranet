function consolidarAntecedentesEstudiante() {
    const configuracion = [
        ['#collapseGrado', '#grado_est', '#grado-app,#form_grado,#boton_grado,#form_edit_grado'],
        ['#collapsePostdoc', '#postdoc_est', '#postdoctorado-app,#form_postdoc,#boton_postdoc,#form_edit_postdoc'],
        ['#collapsePub', '#publi_est', '#form_publicacion,#boton_publicacion,#form_edit_publicacion'],
        ['#collapseCongreso', '#cong_est', '#form_congreso,#boton_congreso'],
        ['#collapseProy', '#proy_est', '#form_proyecto,#boton_proyecto'],
        ['#collapsePasantiaEst', '#pasant_est', '#pasantia-app,#form_pasantia,#boton_pasantia'],
        ['#collapsePasant', '#beca_est', '#beca_app']
    ];
    configuracion.forEach(function (item) {
        const $zona = $(item[0]).find('.accordion-body').first().addClass('oa-zone');
        $zona.find(item[1]).first().addClass('oa-list');
        $(item[2]).each(function () { $(this).appendTo($zona); });
        $zona.find('[id^="boton_"]').addClass('oa-add');
        $zona.find('form, #beca_editor, #pasantia').not(':has(.oa-list)').addClass('oa-editor');
        $zona.find('.oa-add button').text('Agregar nuevo');
        $zona.find('.oa-editor').hide();
    });
    $('#congreso_card,#proyecto_card,#pasantia_card,#beca_card').remove();
    $('#id_usuario').empty().addClass('d-none').prependTo('#ficha_ant');
    $('#acad_est,#edit_acad_est').remove();
}
consolidarAntecedentesEstudiante();
$(document).trigger('antecedentesEstudianteListos');

//vista de ficha estudiantes
function fichaEst() {
    $("#info_est").fadeIn();
    $("#ficha_acad").fadeOut();
    $("#lista_est").fadeOut();
    $("#acad_est").fadeOut();
    $("#edit_pers_est").fadeOut();
    $("#edit_prog_est").fadeOut();
    $("#edit_acad_est").fadeOut();
    
}
//vista editar datos programa
function editProgEst() {
    $("#lista_est").fadeOut();
    $("#info_est").fadeOut();
    $("#acad_est").fadeOut();
    $("#edit_pers_est").fadeOut();
    $("#edit_prog_est").fadeIn();
    $("#edit_acad_est").fadeOut();
}
//vista editar datos personales
function editPersDoc() {
    $("#lista_est").fadeOut();
    $("#info_est").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_est").fadeOut();
    $("#edit_pers_est").fadeIn();
    $("#edit_prog_est").fadeOut();
    $("#edit_acad_est").fadeOut();
}

function mensajeErrorFichaEstudiante(xhr) {
    const codigo = xhr && xhr.responseJSON ? xhr.responseJSON.error : '';
    const mensajes = {
        NO_AUTENTICADO: 'Debe iniciar sesión nuevamente.',
        NO_AUTORIZADO: 'No tiene autorización para realizar esta operación.',
        FORBIDDEN: 'No tiene autorización para consultar esta ficha.',
        IDENTIDAD_INCOMPATIBLE: 'No fue posible resolver una identidad de Estudiante válida.',
        CAMBIO_PASSWORD_NO_AUTORIZADO: 'El cambio de contraseña no está disponible en esta ficha.',
        CREDENCIALES_AJENAS_NO_AUTORIZADAS: 'No puede cambiar el correo de acceso de otra persona.',
        DATOS_INVALIDOS: 'Revise los datos y las opciones seleccionadas.',
        DATOS_INCOMPATIBLES: 'Los datos son incompatibles con un registro existente.',
        ESTUDIANTE_NO_ENCONTRADO: 'No se encontró la ficha del Estudiante.'
    };
    return mensajes[codigo] || 'No fue posible completar la operación. Revise los datos e intente nuevamente.';
}

function mostrarErrorFichaEstudiante(xhr) {
    $('#info_est,#id_usuario').attr('name', '0');
    $('#login').attr('name', '');
    $('#datos_fichaest').empty().append($('<tr>').append(
        $('<td>').attr('colspan', 2).addClass('text-danger').text(mensajeErrorFichaEstudiante(xhr))
    ));
    $('#ant_acad_est,#grado_est,#postdoc_est,#publi_est,#cong_est,#pasant_est,#proy_est,#beca_est').empty();
}

function ocultarPasswordFichaEstudiante() {
    $('#box-pass,#btn_cambiar_pass').hide();
    $('#pass,#pass2').val('').prop('disabled', true);
}
ocultarPasswordFichaEstudiante();

function cadenaMayFichaEstudiante(valor) {
    const texto = String(valor == null ? '' : valor).trim().replace(/\s+/g, ' ');
    return texto ? cadenaMay(texto) : '';
}

function llenarFormPersEst(id_usu) {
    $.ajax({
        async: false,
        url: "../ajax/estudiante.php",
        type: "POST",
        dataType: 'json',
        data: { op: "read_est_id", id_usu },
        success: function (usu) {
            if (!Array.isArray(usu) || !usu.length) {
                mostrarErrorFichaEstudiante();
                fichaEst();
                return;
            }
            $('#login').attr('name', usu[0]['id_login'])
            $('#nombres').val(cadenaMayFichaEstudiante(usu[0]['nombres']));
            $('#ap_pat').val(cadenaMayFichaEstudiante(usu[0]['ap_pat']));
            $('#ap_mat').val(cadenaMayFichaEstudiante(usu[0]['ap_mat']));
            $('#fech_nac').val(usu[0]['fecha_nac']);
            $('#documento').val(usu[0]['tipo_doc']);
            $('#nro_doc').val(usu[0]['nro_doc']);
            $('#pais_nac').val(usu[0]['pais_nac']);
            $('#genero').val(usu[0]['genero']);
            if (usu[0]['id_pueblo'] == 1) {
                $("input[type='radio'][name='pueblo'][value='no']").attr('checked', true);
            } else {
                $("input[type='radio'][name='pueblo'][value='si']").attr('checked', true);
                $('#p').remove()
                $('#row_pueb').append('<div class="col-md-6 mb-3" id="p"><label for="pueb_ind" class="form-label" >Pueblo Indígena</label><select id="select_pueb" name="select_pueb" class="form-select"></select></div>')
                ajaxSelect('#select_pueb', ruta + 'ajax/pueblo.php', 'Seleccione', 'read', undefined, 'id_pueblo', 'pueblo');
                $('#select_pueb').val(usu[0]['id_pueblo']);
            }
            $('#pais_res').val(usu[0]['pais_res']);
            $('#region').val(cadenaMayFichaEstudiante(usu[0]['region']));
            $('#comuna').val(cadenaMayFichaEstudiante(usu[0]['comuna']));
            $('#telefono').val(usu[0]['telefono']);
            $('#direccion').val(cadenaMayFichaEstudiante(usu[0]['direccion']));
            $('#cont_em').val(cadenaMayFichaEstudiante(usu[0]['cont_em']));
            $('#tel_em').val(usu[0]['tel_em']);
            $('#genero').val(usu[0]['genero']);
            $('#correo').val(usu[0]['correo']);
            $('#correo').prop('readonly', /\/admin\/ver\.estudiante\.php$/i.test(window.location.pathname));
            ocultarPasswordFichaEstudiante();
        },
        error: function (xhr) {
            mostrarErrorFichaEstudiante(xhr);
            fichaEst();
        },
    });
}
//cargar formulario datos programa desde ajax
function llenarFormProgEst(id_usu) {
    $.ajax({
        async: false,
        url: "../ajax/estudiante.php",
        type: "POST",
        data: { op: "read_est_prog", id_usu },
        success: function (response) {
            let usu = JSON.parse(response);
            console.log(usu)
            $.ajax({
                url: "../ajax/estudiante.php",
                type: "POST",
                dataType: "json",
                data: { op: "read_nom_prof", id_estudiante: usu[0]['id_usuario'] },
                success: function (response) {
                    if (!response.ok || !response.datos || response.datos.length !== 1) {
                        return;
                    }
                    let prof = response.datos;
                    let nombre = prof[0]['nombres'] + ' ' + prof[0]['ap_pat'] + ' ' + prof[0]['ap_mat']
                    $('#prof_guia').val(cadenaMay(nombre));
                    $('#prof_guia').attr('name', prof[0]['id_usuario']);
                },
                error: function () {
                    console.error('No fue posible cargar el profesor guía del Estudiante.');
                }
            });

            $('#login_est').attr('name', usu[0]['id_login'])
            $('#inst_unid').val(usu[0]['inst_usuario']);
            $('#promedio').val(usu[0]['prom_notas']);
            $('input[name="trab"]').prop("checked", true).val(usu[0]['trabaja']);
            $('#orient').val(usu[0]['orient_disc']);
            $('#sit_ocup').val(usu[0]['sit_previa']);
            $('#fech_ing').val(usu[0]['fech_ingr']);
            $('#fech_grad').val(usu[0]['fecha_egr']);
            $('#tipo_est_edit').val(usu[0]['tipo_est']);
            usu.forEach(u => {
                $("input[type='checkbox'][name='linea'][id='" + u['linea_inv'] + "']").attr('checked', true);
            });
        }
    });
}
//leer los datos de programa de los estudiantes
function leerDatProgEst() {
    let inst_unid = $('#inst_unid').val() == 'otro' ? espacios($('#otro_inst_doc').val()) : $('#inst_unid').val();
    let promedio = $('#promedio').val();
    let trabaja = $("input[type=checkbox][name=trab]:checked").val()
    let orient = $('#orient').val();
    let lineaInv = []
    let profesor = $('#prof_guia').attr('name')
    let sit_ocup = $('#sit_ocup').val()
    let fecha_ingreso = $('#fecha_ing').val()
    let fecha_grad = $('#fecha_grad').val()
    let tipo_est = $('#tipo_est').val()
    // let id_login=$('#id_login').val()==''?0:$('#id_login').val();
    $("input[type=checkbox][name=linea]:checked").each(function () {
        lineaInv.push($(this).val())
    });
    datProg = { inst_unid, lineaInv, promedio, trabaja, orient, profesor, sit_ocup, fecha_ingreso, fecha_grad, tipo_est }
    return datProg

}
  function editAcadEst() {
    $("#lista_est").fadeOut();
    $("#info_est").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_est").fadeOut();
    $("#edit_pers_est").fadeOut();
    $("#edit_acad_est").fadeIn();
    // cargarGrado($('#id_usuario').attr('name'), '#grados_card')
  }
function reiniciarInfoEst() {
    ocultarPasswordFichaEstudiante();
    $("html, body").animate({ scrollTop: 0 }, 100);
    fichaEst();
    $("#datos_fichadoc").html("");
    $("#ant_acad_doc").html("");
    cargarFichaEst($('#id_usuario').attr('name'))
    // mostrarBotonPass()
}
$('.volverInfoEst').click(function () {
    reiniciarInfoEst()
})

$('#prof_guia').keyup(function () {
    $('#list_prof').hide();
    busqueda = $('#prof_guia').val();
    if (busqueda !== '') {
        console.log(busqueda);
        $.ajax({
            url: '../ajax/docente.php',
            type: 'POST',
            data: { op: 'read_prof', busqueda },
            success: function (response) {
                let profes = JSON.parse(response);
                let template = '';
                console.log(profes)
                if (profes == '' && $('#prof_guia').attr('name') == '') {
                    template = '<li class="list-group-item text-rosado">Ingrese un nombre válido</li>';
                } else {
                    $('#prof_guia').attr('name', '');
                    profes.forEach(list => {
                        prof = list[1] + ' ' + list[2] + ' ' + list[3];
                        prof = cadenaMay(prof);
                        template += `
                <li class='list-group-item listProf' id='${list[0]}' name='${prof}'> ${prof}</li>`
                    });
                }
                $('#list_prof').html(template);
                $('#list_prof').show();
            }
        })
    }
})
//cargar profesor desde la lista
$('body').on('click', '.listProf', function () {
    id = $(this).attr('id');
    prof = $(this).attr('name');
    $('#prof_guia').val(prof);
    $('#prof_guia').attr('name', id);
    $('#list_prof').hide();

})
$("body").on("click", ".editarPers", function () {
    $("html, body").animate({ scrollTop: 0 }, 100);
    editPersDoc()
    llenarFormPersEst($('#info_est').attr('name'))
    ocultarPasswordFichaEstudiante();
});


//cargar informacion estudiante
//eliminar estudiante
$('body').on('click', '.eliminarEst', function () {
    id_login = $(this).attr('id');
    $.ajax({
        url: '../ajax/estudiante.php',
        type: 'POST',
        data: { id_login, op: 'delete' },
        success: function (response) {
            let mensaje = JSON.parse(response);
            console.log(mensaje)
            $('#mnsj_row_encabezado').show();
            $('#mnsj_encabezado').removeClass('alert-danger');
            $('#mnsj_encabezado').addClass('alert-success');
            $('#mnsj_encabezado').html(mensaje);
            setTimeout(function () {
            $("#mnsj_row_encabezado").fadeOut(1500);
            cargarEst()
        }, 3000);
        }
    })
})
$('.volverTablaEst').click(function () {
    listaEst()
    $('#datos_fichaest').html('');
    $('#ant_acad_est').html('');
})
$('.volverInfo').click(function () {
    fichaEst()
    $("#ant_acad_est").html('')
    $("#datos_fichaest").html('')
    cargarFichaEst($('#id_usuario').attr('name'))
})
$('#form_edit_prog').submit(function (e) {
    e.preventDefault();
    let promedio = $('#promedio').val();
    let inst = $('#inst_unid').val();
    let trabaja = $('input:radio[name=trab]:checked').attr("value");
    let profesor = $('#prof_guia').attr('name');
    let orientacion = $('#orient').val();
    let sit_ocup = guardar($('#sit_ocup').val());
    let fech_ing = $('#fech_ing').val();
    let fech_grad = $('#fech_grad').val();
    // let tipo_est = $('#tipo_est_edit').val();
    // let permisos = []
    // if (tipo_est == 1 || tipo_est == 4) {
    //     permisos = [5]
    // } else if (tipo_est == 2 || tipo_est == 3) {
    //     permisos = [3,5]
    // } 
    // console.log(permisos)
    let lineaInv = []

    $("input:checkbox:checked").each(function () {
        lineaInv.push($(this).val())
    });
    console.log(lineaInv)
    datProg = {  promedio, inst, trabaja, profesor, orientacion, sit_ocup, fech_ing, fech_grad, lineaInv}
    console.log(datProg)
    datProg.id_usu = $('#info_est').attr('name')
    datProg.id_login = $('#login').attr('name')
    // actualizo la variable op para editar los valores en el modelo
    datProg.op = 'update-inf-prog'
    console.log(datProg)

    $.post('../ajax/estudiante.php', datProg, function (response) {
        console.log(response)
        let dato = JSON.parse(response);
        console.log(dato)
        $('#mnsj_row_acad_est').show();
        $('#mnsj_acad_est').removeClass('alert-danger');
        $('#mnsj_acad_est').addClass('alert-success');
        $('#mnsj_acad_est').html(dato);
        setTimeout(function () {
            $("#mnsj_row_acad_est").fadeOut(1500);
            reiniciarInfoEst()
            cargarEst()
        }, 3000);
    })


})
$("body").on("click", ".editarProgEst", function () {
    $("html, body").animate({ scrollTop: 0 }, 100);
    editProgEst()
    llenarFormProgEst($('#info_est').attr('name'))
    $('#mnsj_row_acad_est').hide()

});
//submit para guardar datos personales
$('#form_edit_pers').submit(function (e) {
    e.preventDefault();
    const datos = leerDatPers();
    datos.id_usu = $('#info_est').attr('name');
    datos.id_login = $('#login').attr('name');
    datos.pueblo = datos.pueblo == null ? 1 : datos.pueblo;
    datos.op = 'update-inf-pers';
    delete datos.pass;
    delete datos.pass2;
    if ($('#correo').prop('readonly')) {
        delete datos.correo;
    }
    const botones = $(this).find(':submit').prop('disabled', true);
    $.ajax({
        url: '../ajax/estudiante.php',
        type: 'POST',
        dataType: 'json',
        data: datos,
        success: function (mensaje) {
            $('#mnsj_row_per_est').show();
            $('#mnsj_per_est').removeClass('alert-danger').addClass('alert-success').text(mensaje);
            setTimeout(function () {
                $('#mnsj_row_per_est').fadeOut(1500);
                reiniciarInfoEst();
            }, 3000);
        },
        error: function (xhr) {
            $('#mnsj_row_per_est').show();
            $('#mnsj_per_est').removeClass('alert-success').addClass('alert-danger').text(mensajeErrorFichaEstudiante(xhr));
        },
        complete: function () {
            botones.prop('disabled', false);
        }
    });
});
//submmit para editar los dayos asociados a programa de estudiantes
$('#form_edit_prog_est').submit(function (e) {
    e.preventDefault();
    datProg = leerDatProgEst()
    console.log(datProg)
    // datProg.id_usu=$('#info_doc').attr('name')
    // datProg.id_login=$('#login').attr('name')
    // // actualizo la variable op para editar los valores en el modelo
    // datProg.op='update-inf-prog'
    console.log(datProg)
    // $.ajax({
    //     url:'../ajax/docente.php',
    //     type: 'POST',
    //     async:false,
    //     data: {op:'read_prof_prog',id_usu:datProg['id_usu']},
    //     success: function(response){ 
    //       console.log('usu cargado con ajax')
    //      let usu = JSON.parse(response);
    //      console.log(usu)
    //      // creo un array para guardar los permisos que no han sido actualizados 
    //      datProg['instTrab']=datProg['instTrab']==0?usu[0]['inst_usuario']:datProg['instTrab']
    //      datProg['catAcad']=datProg['catAcad']==0?usu[0]['cat_academica']:datProg['catAcad']
    //      datProg['anio_ing']=datProg['anio_ing']==0?usu[0]['anio_ingreso']:datProg['anio_ing']
    //      datProg['vinculo']=datProg['vinculo']==0?usu[0]['vinculo']:datProg['vinculo']
    //     //  datProg['permisos']=datProg['vinculo']==0?usu[0]['vinculo']:datProg['vinculo']

    //     }
    //   })
    //   console.log(datProg)

    //     $('#permiso').removeClass('is-invalid')
    //     $.post('../ajax/docente.php',datProg,function(response){
    //       let dato = JSON.parse(response);
    //       console.log(dato)
    //       $('#mnsj_row_prog_doc').show();
    //       $('#mnsj_prog_doc').removeClass('alert-danger');
    //       $('#mnsj_prog_doc').addClass('alert-success');
    //       $('#mnsj_prog_doc').html(dato);
    //       setTimeout(function() {
    //           $("#mnsj_row_prog_doc").fadeOut(1500);
    //           reiniciarInfoDoc()
    //       },3000);
    //    }) 


})
