//cargar informacion estudiante
function cargarFichaEst() {
    $.ajax({
        async: false,
        url: '../ajax/estudiante.php',
        type: 'POST',
        data: { op: 'read_est_perfil'
            },
        success: function (response) {
            console.log(response)
            let usu = JSON.parse(response);
            console.log(usu)
            let template;
            nombre = cadenaMay(usu[0]['nombres']) + ' ' + letraMay(usu[0]['ap_pat']) + ' ' + letraMay(usu[0]['ap_mat'])
            cat_acad = usu[0]['cat_academica'] == 1 ? 'Profesor(a) instructor(a)' : usu[0]['cat_academica'] == 2 ? 'Profesor(a) asistente' : usu[0]['cat_academica'] == 3 ? 'Profesor(a) asociado(a)' : usu[0]['cat_academica'] == 4 ? 'Profesor(a) titular' : 'Sin Jerarquía'
            $('#info_est').attr('name', usu[0]['id_usuario'])
            $('#id_usuario').attr('name', usu[0]['id_usuario'])
            template = `
        <tr>
        <td class="col-4" ><h4 class="pt-1 mt-3">ANTECEDENTES PERSONALES</h4></td>
        <td class="col-8 ps-0"><h4 class=" mt-3"><button type="button" class="btn btn-dark btn-sm  ps-1    editarPers" id="${usu[0]['id_usuario']}">Editar Datos Personales</button></h4></td>
        </tr>
        <tr>
        <td class="col-4" >Nombre</td>
        <td class="col-8">${nombre}</td>
        </tr>
        <tr>
        <td class="col-4" >País Nacionalidad</td>
        <td class="col-8">${letraMay(usu[0]['p_nac'])}</td>
        </tr>
        <td class="col-4" >${usu[0]['tipo_doc'] == 1 ? 'Rut' : 'Pasaporte'}</td>
        <td class="col-8">${usu[0]['nro_doc']}</td>
        </tr>
        <tr>
        <td class="col-4" >fecha de Nacimiento</td>
        <td class="col-8">${mostrarFecha(usu[0]['fecha_nac'])}</td>
        </tr>
        <tr>
        <td class="col-4" >Género</td>
        <td class="col-8">${usu[0]['genero'] == 1 ? 'Masculino' : usu[0]['genero'] == 2 ? 'Femenino' : usu[0]['genero'] == 3 ? 'No Binario' : 'Otro'}</td>
        </tr>
        <tr>
        <td class="col-4" >Pueblo Indígena</td>
        <td class="col-8">${letraMay(usu[0]['pueblo'])}</td>
        </tr>
        <tr>
        <td class="col-4"><h5 class=" mt-3">DATOS DE CONTACTO</h5></td>
        <td class="col-8"></td>
        </tr>
        <tr>
        <td class="col-4">Correo Electrónico</td>
        <td class="col-8">${usu[0]['correo']}</td>
        </tr>
        <tr>
        <td class="col-4">Dirección</td>
        <td class="col-8">${cadenaMay(usu[0]['direccion'])}</td>
        </tr>
        <tr>
        <td class="col-4">Comuna</td>
        <td class="col-8">${cadenaMay(usu[0]['comuna'])}</td>
        </tr>
        <tr>
        <td class="col-4">Región</td>
        <td class="col-8">${cadenaMay(usu[0]['region'])}</td>
        </tr>
        <tr>
        <td class="col-4" >País Residencia</td>
        <td class="col-8">${letraMay(usu[0]['p_resi'])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono</td>
        <td class="col-8">${usu[0]['telefono']}</td>
        </tr>
        <tr>
        <td class="col-4">Contacto de Emergencia</td>
        <td class="col-8">${cadenaMay(usu[0]['cont_em'])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono Contacto Emergencia</td>
        <td class="col-8">${usu[0]['tel_em']}</td>
        </tr>
        <tr>
        <td class="col-4"><h4 class="mt-3">ANTECEDENTES PROGRAMA</h4></td>
        <td class="col-8 ps-0"><h4 class="mt-3"><button type="button" class="btn btn-dark ps-1 btn-sm   editarProgEst text-center" id="${usu[0]['id_usuario']}">Editar Datos Programa</button></h4></td>
        </tr>
        <tr>
        <td class="col-4">Estado</td>
        <td class="col-8">${cadenaMay(usu[0]['tipo'])}</td>
        </tr>
        <tr>
        <td class="col-4">Fecha de Ingreso</td>
        <td class="col-8">${mostrarFecha(usu[0]['fech_ingr'])}</td>
        </tr>
        <tr>
        <td class="col-4">Fecha de Egreso</td>
        <td class="col-8">${usu[0]['fecha_egr'] == null ? '------' : mostrarFecha(usu[0]['fecha_egr'])}</td>
        </tr>
        <tr>
        <td class="col-4">Promedio de Notas Pregrado</td>
        <td class="col-8">${usu[0]['prom_notas']}</td>
        </tr>
        `
            //profesor guia
            $.ajax({
                
                type: 'POST',
                url: '../ajax/docente.php',
                data: { op: 'read_prof_id', id_usu: usu[0]['prof_guia'] },
                success: function (response) {
                    let prof = JSON.parse(response);
                    console.log(prof)
                    nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
                    docente = nombre

                    template += `<tr>
                    <td>Profesor Guía</td>
                    <td>${docente}</td>
                    </tr>`
                }
            })

            template += `<tr>
            <td class="col-4">Linea(s) de Investigación</td>
            <td class="col-8">
            <ul class="list-group list-group-flush">
            `
            usu.forEach(usuario => {
                template += `
          <li class="list-group-item">${usuario['linea']}</li>
        `
            });
            template += `</ul></td></tr>`
            $('#datos_fichaest').html(template);
            $("#ant_acad_est").html('')
        }
    })
    $('#ant_acad_est').append('<div class="row m-2"><div class="col-md-4 m-0 p-2 ps-0 ms-0 mt-2 "><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div><div class="col-md-8 m-0 p-2"><h4><button type="button" class="btn btn-dark btn-sm text-center agrAcadEst" id="">Agregar Datos Académicos</button></h4></div></div>')
    $("#grado_est").html('')
    $('#postdoc_est').html('')
    $('#publi_est').html('')
    $('#cong_est').html('')
    $('#pasant_est').html('')
    $('#proy_est').html('')
    $('#beca_est').html('')
    cargarGrado($('#info_est').attr('name'), '#grado_est')
    cargarPostdoc($('#info_est').attr('name'), '#postdoc_est')
    cargarPub($('#info_est').attr('name'), '#publi_est')
    cargarCong($('#info_est').attr('name'), '#cong_est')
    cargarPasantia($('#info_est').attr('name'), '#pasant_est')
    cargarProy($('#info_est').attr('name'), '#proy_est')
    cargarBeca($('#info_est').attr('name'), '#beca_est')
}
function fichaEst() {
    $("#info_est").fadeIn();
    $("#ficha_acad").fadeOut();
    $("#lista_est").fadeOut();
    $("#acad_est").fadeOut();
    $("#edit_pers_est").fadeOut();
    $("#edit_prog_est").fadeOut();
    $("#edit_acad_est").fadeOut();
    
}
function init() {
    // $('#login').attr('name',2)
    // cargarFichaEst(2)
    
    fichaEst()
    ajaxSelect('#tipo', '../ajax/estudiante.php', 'Seleccione', 'read_tipo');
    $('#vol-infoest').hide()
    $('#vol').hide()
    $("#box-pass").hide();
    $("#mnsj_row_per").hide();
    $("#mnsj_row_per_est").hide();
    $("#mnsj_row_prog_est").hide();
    $("mnsj_row_acad_est").hide();
    $('.loadPage').fadeOut();
    cargarFichaEst()
   



}
init()