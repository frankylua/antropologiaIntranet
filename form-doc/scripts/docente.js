
//ajaxSelect('#cat_acad',ruta+'ajax/docente.php','Seleccione','read_cat');
function valAcadProf(linea){
    const i=validSelect('#inst_unid_trabajo');
    const c=validSelect('#cat_acad');
    const a=validSelect('#anio_ing');
    const v=validSelect('#vinculo');
    const l=!Array.isArray(linea)||linea.length===0;
    if(i||c||a||v||l){
        if(l){
            $('.linea-inv').addClass('is-invalid');
        }
        $('#mnsj_row_acad_prof').show();
        $('#mnsj_acad_prof').addClass('alert-danger');
        $('#mnsj_acad_prof').html('Por favor, rellene todos los campos');
        return false;
    }else{
        return true;    
    }
}
function limpiarClickProf(){
    $('#inst_unid_trabajo').click(function(){limpiarSelect('#inst_unid_trabajo');})
    $('#cat_acad').click(function(){limpiarSelect('#cat_acad');})
    $('#anio_ing').click(function(){limpiarSelect('#anio_ing');})
    $('#vinculo').click(function(){limpiarSelect('#vinculo');})
    $('.linea-inv').click(function(){$('.linea-inv').removeClass('is-invalid');})    
}
function init(){
    infoPers();
    //AntecAcad();
    limpiarValClick();
    limpiarClickProf()
    $('#btn_cambiar_pass').hide();
    $('#mnsj_row_acad_prof').hide();
    $('#mnsj_row_prog_doc').hide();
    
    $('#loadPageEst').fadeOut();
    $('#tipo_ing').html('<div class="d-flex justify-content-end">Ingreso Docente</div>');
}
$(document).on('change','#inst_unid_trabajo',function(){
    $('#inst_trab').remove();
    if($('#inst_unid_trabajo').val() == 'otro'){
    $('#inst_doc').append('<div class="col-md-6 mb-3" id="inst_trab"><input type="text" class="form-control" id="otro_inst_doc" placeholder="Nombre Institución"  maxlength="80"></div>');
    }
});

$('#form_usuario').submit(function(e){
    e.preventDefault();
    const usuario=leerDatPers();
    const profProg=leerDatProgDoc();
    const esTransicion=window.contextoAgregarDocencia&&window.contextoAgregarDocencia.listo;
    if(valAcadProf(profProg['lineaInv'])){
        const nuevo_inst=$('#inst_unid_trabajo').val()==='otro';
        if(nuevo_inst){
            if(esTransicion){
                usuario.nueva_institucion=profProg.instTrab;
                profProg.instTrab=0;
            }else{
                const altaInstitucion=crearInstitucionContextual(profProg['instTrab'],0,'registro');
                if(!altaInstitucion.ok){
                    mostrarErrorInstitucionContextual('#mnsj_row_prog_doc','#mnsj_prog_doc',altaInstitucion);
                    return;
                }
                $('#id_inst_doc').attr('name',altaInstitucion.id);
            }
        }
        if(!esTransicion || !nuevo_inst){
            profProg['instTrab']=$('#id_inst_doc').attr('name')==0?profProg['instTrab']:$('#id_inst_doc').attr('name');
        }
        jQuery.extend(usuario,profProg);
        usuario.op=esTransicion?'agregar-docencia':'insert-update';
        $.ajax({
            url:'../ajax/docente.php',
            type:'POST',
            dataType:'json',
            data:usuario,
            success:function(respuesta){
                if(!respuesta || respuesta.ok!==true){
                    $('#mnsj_row_prog_doc').show();
                    $('#mnsj_prog_doc').removeClass('alert-success').addClass('alert-danger');
                    $('#mnsj_prog_doc').text(
                        respuesta&&respuesta.mensaje
                            ? respuesta.mensaje
                            : (esTransicion?'No fue posible agregar Docencia.':'No fue posible guardar el perfil Docente.')
                    );
                    return;
                }
                $('#id_usuario').attr('name',respuesta.id_usuario);
                $('#mnsj_row_prog_doc').show();
                $('#mnsj_prog_doc').removeClass('alert-danger').addClass('alert-success');
                $('#mnsj_prog_doc').text(respuesta.mensaje);
                setTimeout(function(){
                    $('#mnsj_row_prog_prof').fadeOut(1500);
                    AntecAcad();
                },3000);
            },
            error:function(xhr){
                let mensaje='No fue posible guardar el perfil Docente.';
                if(xhr.responseJSON&&xhr.responseJSON.mensaje){
                    mensaje=xhr.responseJSON.mensaje;
                }else{
                    try{
                        const respuesta=JSON.parse(xhr.responseText);
                        mensaje=respuesta.mensaje||mensaje;
                    }catch(error){
                    }
                }
                $('#mnsj_row_prog_doc').show();
                $('#mnsj_prog_doc').removeClass('alert-success').addClass('alert-danger');
                $('#mnsj_prog_doc').text(mensaje);
            }
        });
    }
})
init()
