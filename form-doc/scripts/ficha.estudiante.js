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
        INACTIVACION_NO_DISPONIBLE: 'La inactivación aún no está habilitada. Solicite a administración que complete la actualización.',
        PERFIL_INACTIVO: 'El perfil está inactivo. Solicite su reactivación a Admin o Comité.',
        LINEAS_INVALIDAS: 'Seleccione al menos una línea de investigación válida.',
        GUIA_NO_ELEGIBLE: 'Seleccione un Profesor aceptado para una nueva asignación.',
        ESTUDIANTE_CON_DEPENDENCIAS: 'El Estudiante tiene antecedentes o relaciones que deben conservarse.',
        CUENTA_CON_OTROS_ROLES: 'La cuenta tiene otros roles y no puede eliminarse desde Estudiantes.',
        ESQUEMA_DELETE_INCOMPATIBLE: 'La eliminación requiere revisar las relaciones de la base de datos.',
        ELIMINACION_PROPIA_NO_AUTORIZADA: 'No puede eliminar su propia cuenta.',
        CONFIRMACION_REQUERIDA: 'Debe confirmar la eliminación.',
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
function gestionaProgramaEstudiante() {
    return $('#reglas_estudiante').attr('data-gestiona') === '1';
}
function llenarFormProgEst(id_usu) {
    if (!gestionaProgramaEstudiante()) return;
    $('#form_edit_prog :submit').prop('disabled',true);
    $('#prof_guia').val('').attr('name','');
    $.ajax({
        async:false, url:'../ajax/estudiante.php',type:'POST',dataType:'json',
        data:{op:'read_est_prog',id_usu},
        success:function(usu){
            if (!Array.isArray(usu) || !usu.length) { fichaEst(); return; }
            const u=usu[0];
            $('#login_est').attr('name',u.id_login);
            $('#inst_unid').val(u.inst_usuario).trigger('change');
            $('#estado_estudiante_programa').val(u.tipo_est);
            $('#promedio').val(u.prom_notas);
            $('input[name="trab"]').prop('checked',false);
            $('input[name="trab"][value="'+u.trabaja+'"]').prop('checked',true);
            $('#orient').val(u.orient_disc);
            $('#sit_ocup').val(u.sit_previa);
            $('#fech_ing').val(u.fech_ingr);
            $('#fech_grad').val(u.fecha_egr || '');
            $("input[type='checkbox'][name='linea']").prop('checked',false);
            usu.forEach(linea => {
                if (linea.linea_inv != null) $("input[type='checkbox'][name='linea'][value='"+Number(linea.linea_inv)+"']").prop('checked',true);
            });
            $.ajax({url:'../ajax/estudiante.php',type:'POST',dataType:'json',
                data:{op:'read_nom_prof',id_estudiante:u.id_usuario},
                success:function(respuesta){
                    if (String($('#info_est').attr('name'))!==String(u.id_usuario) || !respuesta.ok || !respuesta.datos || respuesta.datos.length!==1) return;
                    const guia=respuesta.datos[0];
                    $('#prof_guia').val(cadenaMayFichaEstudiante([guia.nombres,guia.ap_pat,guia.ap_mat].join(' '))).attr('name',guia.id_usuario);
                },
                error:function(xhr){ errorProgramaEstudiante(xhr); },
                complete:function(){ $('#form_edit_prog :submit').prop('disabled',false); }
            });
        },
        error:function(xhr){ errorProgramaEstudiante(xhr); }
    });
}
function errorProgramaEstudiante(xhr) {
    $('#mnsj_row_acad_est').show();
    $('#mnsj_acad_est').removeClass('alert-success').addClass('alert-danger').text(mensajeErrorFichaEstudiante(xhr));
}
$(document).on('change','#inst_unid',function(){
    $('#inst_unid_col').remove();
    if ($(this).val()==='otro') $('#row_otro_inst').append('<div class="col-md-6 mb-3" id="inst_unid_col"><label for="otro_inst_est" class="form-label">Nombre Institución</label><input id="otro_inst_est" class="form-control" maxlength="80" required></div>');
});
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

let consultaGuiaEstudiante=0;
$('#prof_guia').keyup(function () {
    $('#prof_guia').attr('name','');
    $('#list_prof').hide().empty();
    const busqueda=$(this).val();
    const consulta=++consultaGuiaEstudiante;
    if (!busqueda) return;
    $.ajax({url:'../ajax/docente.php',type:'POST',dataType:'json',data:{op:'read_prof',busqueda},
        success:function(profes){
            if (consulta!==consultaGuiaEstudiante) return;
            const lista=$('#list_prof').empty();
            profes.forEach(guia => {
                const nombre=cadenaMayFichaEstudiante([guia.nombres,guia.ap_pat,guia.ap_mat].join(' '));
                lista.append($('<li>').addClass('list-group-item listProf').attr('id',guia.id_usuario).attr('name',nombre).text(nombre));
            });
            if (!profes.length) lista.append($('<li>').addClass('list-group-item').text('Seleccione un Profesor aceptado.'));
            lista.show();
        },error:function(xhr){ errorProgramaEstudiante(xhr); }
    });
});
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
    $.ajax({url:'../ajax/estudiante.php',type:'POST',dataType:'json',
        data:{id_login:$(this).attr('id'),op:'delete',confirmar:1},
        success:function(mensaje){
            $('#mnsj_row_encabezado').show();
            $('#mnsj_encabezado').removeClass('alert-danger').addClass('alert-success').text(mensaje);
            cargarEst();
        },error:function(xhr){
            $('#mnsj_row_encabezado').show();
            $('#mnsj_encabezado').removeClass('alert-success').addClass('alert-danger').text(mensajeErrorFichaEstudiante(xhr));
        }
    });
});
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
$('#form_edit_prog').submit(function(e){
    e.preventDefault();
    if (!gestionaProgramaEstudiante()) return;
    const lineaInv=[];
    $("input:checkbox[name='linea']:checked").each(function(){ lineaInv.push($(this).val()); });
    const inst=$('#inst_unid').val();
    const datos={op:'update-inf-prog',id_usu:$('#info_est').attr('name'),id_login:$('#login_est').attr('name'),
        promedio:$('#promedio').val(),inst:inst==='otro'?0:inst,trabaja:$('input:radio[name=trab]:checked').val(),
        profesor:$('#prof_guia').attr('name'),orientacion:$('#orient').val(),sit_ocup:$('#sit_ocup').val(),
        fech_ing:$('#fech_ing').val(),fech_grad:$('#fech_grad').val(),lineaInv};
    if ($('#estado_estudiante_programa').length) datos.tipo_est=$('#estado_estudiante_programa').val();
    if (inst==='otro') datos.nueva_institucion=$('#otro_inst_est').val();
    const botones=$(this).find(':submit').prop('disabled',true);
    $.ajax({url:'../ajax/estudiante.php',type:'POST',dataType:'json',data:datos,
        success:function(mensaje){
            $('#mnsj_row_acad_est').show();
            $('#mnsj_acad_est').removeClass('alert-danger').addClass('alert-success').text(mensaje);
            setTimeout(function(){
                if (String(datos.tipo_est)==='6' && typeof listaEst==='function') {
                    listaEst(); $('#mnsj_row_encabezado').show();
                    $('#mnsj_encabezado').removeClass('alert-danger').addClass('alert-success').text(mensaje);
                } else reiniciarInfoEst();
                if (typeof cargarEst==='function') cargarEst();
            },1500);
        },error:errorProgramaEstudiante,
        complete:function(){ botones.prop('disabled',false); }
    });
});
$("body").on('click','.editarProgEst',function(){
    if (!gestionaProgramaEstudiante()) return;
    $('#mnsj_row_acad_est').hide();
    editProgEst();
    llenarFormProgEst($('#info_est').attr('name'));
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
