
let tokenAutoaltaEstudianteCache;
function tokenAutoaltaEstudiante(){
    if(tokenAutoaltaEstudianteCache!==undefined){
        return tokenAutoaltaEstudianteCache;
    }
    tokenAutoaltaEstudianteCache='';
    $.ajax({
        url:'../ajax/estudiante.php',
        type:'POST',
        dataType:'json',
        async:false,
        data:{op:'contexto_autoalta'},
        success:function(respuesta){
            if(respuesta&&respuesta.ok===true&&respuesta.autoalta===true){
                tokenAutoaltaEstudianteCache=respuesta.token;
            }
        }
    });
    return tokenAutoaltaEstudianteCache;
}
function mostrarErrorCreateEstudiante(mensaje){
    $('#mnsj_row_acad_est').show();
    $('#mnsj_acad_est').removeClass('alert-success');
    $('#mnsj_acad_est').addClass('alert-danger');
    $('#mnsj_acad_est').text(mensaje||'No fue posible guardar el estudiante.');
}
function mostrarAceptacionRequeridaEstudiante(){
    mostrarErrorCreateEstudiante('Debe aceptar los acuerdos y condiciones para completar el registro.');
    const acuerdos=$('#acuerdos-autoalta-check').addClass('is-invalid').attr('aria-invalid','true');
    const control=acuerdos.get(0);
    if(control){
        control.focus({preventScroll:true});
        control.scrollIntoView({block:'center'});
    }
}
$('#acuerdos-autoalta-check').on('invalid',function(event){
    event.preventDefault();
    mostrarAceptacionRequeridaEstudiante();
});
$('#acuerdos-autoalta-check').on('change',function(){
    if($(this).prop('checked')){
        $(this).removeClass('is-invalid').removeAttr('aria-invalid');
    }
    if($(this).prop('checked') && $('#mnsj_acad_est').text()==='Debe aceptar los acuerdos y condiciones para completar el registro.'){
        $('#mnsj_row_acad_est').hide();
        $('#mnsj_acad_est').text('');
    }
});
// datos estudiante 
$('#volver_acad').click(function(){
    infoPers();
})

$('#sig_acad').click(function(){
    infoProg();
})
// imput para agregar otra institucion estudiante
$(document).on('change','#inst_unid',function(){
    $('#inst_unid_col').remove();
    if($('#inst_unid').val() == 'otro'){
    $('#row_otro_inst').append('<div class="col-md-6 order-1 mb-3" id="inst_unid_col"><input type="text" class="form-control" id="otro_inst_est" placeholder="Nombre Institución"  maxlength="80"></div>');
    }
});
// Selector contextual: solo guarda el identificador de una opción elegida.
let consultaGuiaAutoalta=0;
$('#prof_guia').keyup(function(){
    $('#list_prof').hide().empty();
    $('#prof_guia').attr('name','');
    const busqueda=$(this).val();
    const consulta=++consultaGuiaAutoalta;
    if (!busqueda) return;
    const tokenAutoalta=tokenAutoaltaEstudiante();
    $.ajax({
        url:tokenAutoalta?'../ajax/estudiante.php':'../ajax/docente.php',type:'POST',dataType:'json',
        data:tokenAutoalta?{op:'read_prof_autoalta',busqueda,autoalta_token:tokenAutoalta}:{op:'read_prof',busqueda},
        success:function(profes){
            if (consulta!==consultaGuiaAutoalta) return;
            const lista=$('#list_prof').empty();
            profes.forEach(guia=>{
                const nombre=[guia.nombres,guia.ap_pat,guia.ap_mat].filter(Boolean).join(' ');
                lista.append($('<li>').addClass('list-group-item listProf').attr('id',guia.id_usuario).attr('name',nombre).text(nombre));
            });
            if (!profes.length) lista.append($('<li>').addClass('list-group-item').text('Seleccione un Profesor aceptado.'));
            lista.show();
        },error:function(){ mostrarErrorCreateEstudiante('No fue posible consultar los Profesores.'); }
    });
});
//cargar profesor desde la lista
$('body').on('click','.listProf',function(){
    id=$(this).attr('id');
    prof=$(this).attr('name');
    $('#prof_guia').val(prof);
    $('#prof_guia').attr('name',id);
    $('#list_prof').hide();

})

function limpiarClick(){
    $('#promedio').click(function(){limpiarInput('#promedio');})
    $('#inst_unid').click(function(){limpiarSelect('#inst_unid');})
    $('#orient').click(function(){limpiarSelect('#orient');})
    $('#prof_guia').click(function(){limpiarInput('#prof_guia');})
    $('#sit_ocup').click(function(){limpiarInput('#sit_ocup');})
    $('#fech_ing').click(function(){limpiarInput('#fech_ing');})
    $('#fech_grad').click(function(){limpiarInput('#fech_grad');})
    $('#tipo_est').click(function(){limpiarSelect('#tipo_est');})
    $('.trabaja').click(function(){$('.trabaja').removeClass('is-invalid');})
    $('.linea-inv').click(function(){$('.linea-inv').removeClass('is-invalid');})
    
}    
function valDatAcad(linea){
    p=validCampoVacio('#promedio'); 
    i=validSelect('#inst_unid');
    o=validSelect('#orient');
    s=validCampoVacio('#sit_ocup');
    f=validCampoVacio('#fech_ing');
    t=validSelect('#tipo_est');
    r=validRadio("trab",'.trabaja');
    const guiaInvalido=!/^[1-9][0-9]*$/.test($('#prof_guia').attr('name') || '');
    l=linea=='';//lineas de investigación
    if(p||guiaInvalido||i||o||s||f||t||l||r){
        if(l){
            $('.linea-inv').addClass('is-invalid');
        }
        if(guiaInvalido){
            $('#prof_guia').removeClass('is-valid')
            $('#prof_guia').addClass('is-invalid')
        }else{
            $('#prof_guia').removeClass('is-invalid')
            $('#prof_guia').addClass('is-valid')

        }

        $('#mnsj_row_acad_est').show();
        $('#mnsj_acad_est').addClass('alert-danger');
        $('#mnsj_acad_est').html('Porfavor, rellene todos los campos');
        return false;
    }else{
        
            return true;
        
    }

    
}
$('#form_usuario').submit(function(e){
    e.preventDefault();
    const acuerdos=$('#acuerdos-autoalta-check');
    if(acuerdos.length && !acuerdos.prop('checked')){
        mostrarAceptacionRequeridaEstudiante();
        return;
    }
    //AntecAcad();
    let usuario=window.usuario && window.usuario.correo ? {...window.usuario} : leerDatPers();
    usuario.pueblo=usuario.pueblo==null?1:usuario.pueblo;
    let promedio=$('#promedio').val();
    let inst=$('#inst_unid').val();
    let trabaja=$('input:radio[name=trab]:checked').attr("value");
    let profesor=$('#prof_guia').attr('name');
    let orientacion=$('#orient').val();
    let sit_ocup=guardar($('#sit_ocup').val());
    let fech_ing=$('#fech_ing').val();
    let fech_grad=$('#fech_grad').val();
    let tipo_est=$('#tipo_est').length ? $('#tipo_est').val() : 1;

    let lineaInv=[]
    
    $("input:checkbox[name='linea']:checked").each(function() {
        lineaInv.push($(this).val())
    });

    
    let id_login=$('#id_login').val()==''?0:$('#id_login').val();
    //ingresar grado academico
    if(valDatAcad(lineaInv)){
        const estadoCorreo=valMail(usuario.correo);
        if(estadoCorreo!=='DISPONIBLE'){
            mostrarErrorCreateEstudiante(estadoCorreo==='OCUPADO'
                ? 'El correo electrónico ya se encuentra registrado'
                : 'No fue posible validar el correo electrónico. Inténtelo nuevamente.');
            return;
        }
        const nuevaInstitucion=inst==='otro';
        let usuAcad={inst:nuevaInstitucion?0:inst,trabaja,orientacion,sit_ocup,fech_ing,fech_grad,tipo_est,id_login,promedio,op:'insert-update',profesor,lineaInv};
        if (nuevaInstitucion) usuAcad.nueva_institucion=$('#otro_inst_est').val();
        const tokenAutoalta=tokenAutoaltaEstudiante();
        if(tokenAutoalta){
            usuAcad.autoalta_token=tokenAutoalta;
            usuAcad.acuerdos_muestra=acuerdos.prop('checked')?'1':'0';
        }
        jQuery.extend(usuario,usuAcad);

        $.ajax({
            url:'../ajax/estudiante.php',
            type:'POST',
            dataType:'json',
            data:usuario,
            success:function(respuesta){
                if(!respuesta||respuesta.ok!==true){
                    mostrarErrorCreateEstudiante(respuesta&&respuesta.mensaje);
                    return;
                }
                $('#id_usuario').attr('name',respuesta.id_usuario);
                $('#mnsj_row_acad_est').show();
                $('#mnsj_acad_est').removeClass('alert-danger');
                $('#mnsj_acad_est').addClass('alert-success');
                $('#mnsj_acad_est').text(respuesta.mensaje);
                setTimeout(function() {
                    $('#mnsj_row_acad_est').fadeOut(1500);
                    if(tokenAutoalta){
                        location.href='../index.php';
                    }else{
                        AntecAcad();
                    }
                },3000);
            },
            error:function(xhr){
                const mensaje=xhr.responseJSON&&xhr.responseJSON.mensaje
                    ? xhr.responseJSON.mensaje
                    : 'No fue posible guardar el estudiante.';
                mostrarErrorCreateEstudiante(mensaje);
            }
        });

    }
});
function init(){
    $('#mnsj_row_acad_est').hide();
    ajaxSelect('#tipo_est','../ajax/estudiante.php','Seleccione','read_tipo', undefined, 'id_tipo_est', 'tipo');
    //AntecAcad();
    $('.loadPage').fadeOut();
    infoPers();
    $('#btn_cambiar_pass').hide();
    //infoAcad()
    limpiarValClick();
    limpiarClick();
    $('#list_prof').hide();
}
init();
    






    


 
    
        

        
