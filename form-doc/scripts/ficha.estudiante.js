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
//vista de antecedentes academicos
function cargarDatosAcademicos(){
    $('#grados_card').html('')
    $('#postdoc_card').html('')
    $('#publicacion_card').html('')
    $('#congreso_card').html('')
    $('#pasantia_card').html('')
    $('#proyecto_card').html('')
    $('#tesis_card').html('')
    cargarGrado($('#id_usuario').attr('name'), '#grados_card')
    cargarPostdoc($('#id_usuario').attr('name'), '#postdoc_card')
    cargarPub($('#id_usuario').attr('name'), '#publicacion_card')
    cargarCong($('#id_usuario').attr('name'), '#congreso_card')
    cargarPasantia($('#id_usuario').attr('name'), '#pasantia_card')
    cargarProy($('#id_usuario').attr('name'), '#proyecto_card')
    cargarTesis($('#id_usuario').attr('name'), '#tesis_card')
}
function AcadEst() {
    $('html, body').stop().animate({
        scrollTop: $('#acad_est').offset().top
      }, 1000);
    $('#lista_est').fadeOut();
    $('#acad_est').fadeIn();
    $('#info_est').fadeOut();
    cargarDatosAcademicos()
    
}
  //llenar ficha estudiante
  
//cargar informacion estudiante
// function cargarFichaEst(id_usu) {
//     $.ajax({
//         async: false,
//         url: '../ajax/estudiante.php',
//         type: 'POST',
//         data: { op: 'read_est_id', id_usu },
//         success: function (response) {
//             let usu = JSON.parse(response);
//             console.log(usu)
//             let template;
//             nombre = cadenaMay(usu[0]['nombres']) + ' ' + letraMay(usu[0]['ap_pat']) + ' ' + letraMay(usu[0]['ap_mat'])
//             cat_acad = usu[0]['cat_academica'] == 1 ? 'Profesor(a) instructor(a)' : usu[0]['cat_academica'] == 2 ? 'Profesor(a) asistente' : usu[0]['cat_academica'] == 3 ? 'Profesor(a) asociado(a)' : usu[0]['cat_academica'] == 4 ? 'Profesor(a) titular' : 'Sin Jerarquía'
//             $('#info_est').attr('name', usu[0]['id_usuario'])
//             $('#id_usuario').attr('name', usu[0]['id_usuario'])
//             template = `
//         <tr>
//         <td class="col-4" ><h4 class="pt-1 mt-3">ANTECEDENTES PERSONALES</h4></td>
//         <td class="col-8 ps-0"><h4 class=" mt-3"><button type="button" class="btn btn-dark btn-sm  ps-1    editarPers" id="${usu[0]['id_usuario']}">Editar Datos Personales</button></h4></td>
//         </tr>
//         <tr>
//         <td class="col-4" >Nombre</td>
//         <td class="col-8">${nombre}</td>
//         </tr>
//         <tr>
//         <td class="col-4" >País Nacionalidad</td>
//         <td class="col-8">${letraMay(usu[0]['p_nac'])}</td>
//         </tr>
//         <td class="col-4" >${usu[0]['tipo_doc'] == 1 ? 'Rut' : 'Pasaporte'}</td>
//         <td class="col-8">${usu[0]['nro_doc']}</td>
//         </tr>
//         <tr>
//         <td class="col-4" >fecha de Nacimiento</td>
//         <td class="col-8">${mostrarFecha(usu[0]['fecha_nac'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4" >Género</td>
//         <td class="col-8">${usu[0]['genero'] == 1 ? 'Femenino' : usu[0]['genero'] == 2 ? 'Masculino' : usu[0]['genero'] == 3 ? 'No Binario' : 'Otro'}</td>
//         </tr>
//         <tr>
//         <td class="col-4" >Pueblo Indígena</td>
//         <td class="col-8">${letraMay(usu[0]['pueblo'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4"><h5 class=" mt-3">DATOS DE CONTACTO</h5></td>
//         <td class="col-8"></td>
//         </tr>
//         <tr>
//         <td class="col-4">Correo Electrónico</td>
//         <td class="col-8">${usu[0]['correo']}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Dirección</td>
//         <td class="col-8">${cadenaMay(usu[0]['direccion'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Comuna</td>
//         <td class="col-8">${cadenaMay(usu[0]['comuna'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Región</td>
//         <td class="col-8">${cadenaMay(usu[0]['region'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4" >País Residencia</td>
//         <td class="col-8">${letraMay(usu[0]['p_resi'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Teléfono</td>
//         <td class="col-8">${usu[0]['telefono']}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Contacto de Emergencia</td>
//         <td class="col-8">${cadenaMay(usu[0]['cont_em'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Teléfono Contacto Emergencia</td>
//         <td class="col-8">${usu[0]['tel_em']}</td>
//         </tr>
//         <tr>
//         <td class="col-4"><h4 class="mt-3">ANTECEDENTES PROGRAMA</h4></td>
//         <td class="col-8 ps-0"><h4 class="mt-3"><button type="button" class="btn btn-dark ps-1 btn-sm   editarProgEst text-center" id="${usu[0]['id_usuario']}">Editar Datos Programa</button></h4></td>
//         </tr>
//         <tr>
//         <td class="col-4">Estado</td>
//         <td class="col-8">${cadenaMay(usu[0]['tipo'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Fecha de Ingreso</td>
//         <td class="col-8">${mostrarFecha(usu[0]['fech_ingr'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Fecha de Egreso</td>
//         <td class="col-8">${usu[0]['fecha_egr'] == null ? '------' : mostrarFecha(usu[0]['fecha_egr'])}</td>
//         </tr>
//         <tr>
//         <td class="col-4">Promedio de Notas Pregrado</td>
//         <td class="col-8">${usu[0]['prom_notas']}</td>
//         </tr>
//         `
//             //profesor guia
//             $.ajax({
//                 async: false,
//                 type: 'POST',
//                 url: '../ajax/docente.php',
//                 data: { op: 'read_prof_id', id_usu: usu[0]['prof_guia'] },
//                 success: function (response) {
//                     let prof = JSON.parse(response);
//                     console.log(prof)
//                     nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
//                     docente = nombre

//                     template += `<tr>
//                     <td>Profesor Guía</td>
//                     <td>${docente}</td>
//                     </tr>`
//                 }
//             })



//             $('#datos_fichaest').append(template);
//         }
//     })
//     $('#ant_acad_est').append('<div class="row m-2"><div class="col-md-4 m-0 p-2 ps-0 ms-0 mt-2 "><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div><div class="col-md-8 m-0 p-2"><h4><button type="button" class="btn btn-dark btn-sm text-center agrAcadEst" id="">Agregar Datos Académicos</button></h4></div></div>')
//     cargarGrado($('#info_est').attr('name'), '#ant_acad_est')
//     cargarPostdoc($('#info_est').attr('name'), '#ant_acad_est')
//     cargarPub($('#info_est').attr('name'), '#ant_acad_est')
//     cargarCong($('#info_est').attr('name'), '#ant_acad_est')
//     cargarPasantia($('#info_est').attr('name'), '#ant_acad_est')
//     cargarProy($('#info_est').attr('name'), '#ant_acad_est')
//     cargarTesis($('#info_est').attr('name'), '#ant_acad_est')
//     cargarBeca($('#info_est').attr('name'), '#ant_acad_est')
// }
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
function llenarFormPersEst(id_usu) {
    $.ajax({
        async: false,
        url: "../ajax/estudiante.php",
        type: "POST",
        data: { op: "read_est_id", id_usu },
        success: function (response) {
            console.log(response)
            let usu = JSON.parse(response);
            console.log('editar usu')
            console.log(usu)
            $('#login').attr('name', usu[0]['id_login'])
            $('#nombres').val(cadenaMay(usu[0]['nombres']));
            $('#ap_pat').val(cadenaMay(usu[0]['ap_pat']));
            $('#ap_mat').val(cadenaMay(usu[0]['ap_mat']));
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
                ajaxSelect('#select_pueb', ruta + 'ajax/pueblo.php', 'Seleccione', 'read');
                $('#select_pueb').val(usu[0]['id_pueblo']);
            }
            $('#pais_res').val(usu[0]['pais_res']);
            $('#region').val(cadenaMay(usu[0]['region']));
            $('#comuna').val(cadenaMay(usu[0]['comuna']));
            $('#telefono').val(usu[0]['telefono']);
            $('#direccion').val(cadenaMay(usu[0]['direccion']));
            $('#cont_em').val(cadenaMay(usu[0]['cont_em']));
            $('#tel_em').val(usu[0]['tel_em']);
            $('#genero').val(usu[0]['genero']);
            $('#correo').val(usu[0]['correo']);
            $('#pass').val('');
            $('#pass2').val('');
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
                async: false,
                url: "../ajax/estudiante.php",
                type: "POST",
                data: { op: "read_nom_prof", id_usu_prof: usu[0]['prof_guia'] },
                success: function (response) {
                    let prof = JSON.parse(response);
                    let nombre = prof[0]['nombres'] + ' ' + prof[0]['ap_pat'] + ' ' + prof[0]['ap_mat']
                    $('#prof_guia').val(cadenaMay(nombre));
                    $('#prof_guia').attr('name', prof[0]['id_usuario']);
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
    $('#box-pass').show()
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
$('body').on('click','#agrAcadEst',function(){
    AcadEst();  
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
    $('#btn_cambiar_pass').show();
});


//cargar informacion estudiante
$('body').on('click', '.agrAcadEst', function () {
    id_usu = $(this).attr('id');
    AcadEst();
})
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
$('#btn_cambiar_pass').click(function () {
    $("#box-pass").show();
    $('#btn_cambiar_pass').hide();
    $('#box-pass').attr('name', 1);
})
//submit para guardar datos personales
$('#form_edit_pers').submit(function (e) {
    e.preventDefault();
    usuario = leerDatPers()
    usuario.id_usu = $('#info_est').attr('name')
    usuario.id_login = $('#login').attr('name')
    console.log(usuario)
    if ($('#box-pass').attr('name') == "1" && usuario['pass'] != usuario['pass2']) {
        $('#pass').addClass('is-invalid')
        $('#pass2').addClass('is-invalid')
        $('#mnsj_row_per_doc').show();
        $('#mnsj_per_doc').addClass('alert-danger');
        $('#mnsj_per_doc').html('Las contraseñas no coniciden');
    } else if ($('#box-pass').attr('name') == "1" && usuario['pass'] == '') {
        $('#pass').addClass('is-invalid')
        $('#pass2').addClass('is-invalid')
        $('#mnsj_row_per_doc').show();
        $('#mnsj_per_doc').addClass('alert-danger');
        $('#mnsj_per_doc').html('Rellene los campos con su nueva contraseña');

    }
    else {
        $('#pass').removeClass('is-invalid')
        $('#pass2').removeClass('is-invalid')

        $.ajax({
            url: '../ajax/estudiante.php',
            type: 'POST',
            async: false,
            data: { op: 'read_est_id', id_usu: usuario['id_usu'] },

            success: function (response) {
                console.log(response)
                let usu = JSON.parse(response);
                console.log(usu)
                usuario['nombres'] = usuario['nombres'] == '' ? usu[0]['nombres'] : usuario['nombres']
                usuario['ap_pat'] = usuario['ap_pat'] == '' ? usu[0]['ap_pat'] : usuario['ap_pat']
                usuario['ap_mat'] = usuario['ap_mat'] == '' ? usu[0]['ap_mat'] : usuario['ap_mat']
                usuario['ap_mat'] = usuario['ap_mat'] == '' ? usu[0]['ap_mat'] : usuario['ap_mat']
                usuario['comuna'] = usuario['comuna'] == '' ? usu[0]['comuna'] : usuario['comuna']
                usuario['cont_em'] = usuario['cont_em'] == '' ? usu[0]['cont_em'] : usuario['cont_em']
                usuario['correo'] = usuario['correo'] == '' ? usu[0]['correo'] : usuario['correo']
                usuario['direccion'] = usuario['direccion'] == '' ? usu[0]['direccion'] : usuario['direccion']
                usuario['nro_doc'] = usuario['nro_doc'] == '' ? usu[0]['nro_doc'] : usuario['nro_doc']
                usuario['pais_nac'] = usuario['pais_nac'] == 0 ? usu[0]['pais_nac'] : usuario['pais_nac']
                usuario['genero'] = usuario['genero'] == '' ? usu[0]['genero'] : usuario['genero']
                usuario['pais_res'] = usuario['pais_res'] == '' ? usu[0]['pais_res'] : usuario['pais_res']
                usuario['pass'] = usuario['pass'] == '' ? usu[0]['pass'] : usuario['pass']
                usuario['pueblo'] = usuario['pueblo'] == null ? 1 : usuario['pueblo']
                usuario['region'] = usuario['region'] == '' ? usu[0]['region'] : usuario['region']
                usuario['tel_em'] = usuario['tel_em'] == '' ? usu[0]['tel_em'] : usuario['tel_em']
                usuario['telefono'] = usuario['telefono'] == '' ? usu[0]['telefono'] : usuario['telefono']
            }
        })
        usuario.op = 'update-inf-pers'
        console.log(usuario)
        $.post('../ajax/estudiante.php', usuario, function (response) {
            let dato = JSON.parse(response);
            $('#mnsj_row_per_est').show();
            $('#mnsj_per_est').removeClass('alert-danger');
            $('#mnsj_per_est').addClass('alert-success');
            $('#mnsj_per_est').html(dato);
            setTimeout(function () {
                $("#mnsj_row_per_est").fadeOut(1500);
                reiniciarInfoEst()
            }, 3000);
        })


    }
})
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