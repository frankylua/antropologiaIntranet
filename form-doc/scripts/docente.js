
//ajaxSelect('#cat_acad',ruta+'ajax/docente.php','Seleccione','read_cat');
function valAcadProf(linea){
    i=validSelect('#inst_unid_trabajo')
    c=validSelect('#cat_acad')
    a=validSelect('#anio_ing')
    v=validSelect('#vinculo')
    l=linea=='';
    p=validSelect('#permiso') 
    if(i||c||a||v||l||p){
        if(l){
            $('.linea-inv').addClass('is-invalid');
        }
        $('#mnsj_row_acad_prof').show();
        $('#mnsj_acad_prof').addClass('alert-danger');
        $('#mnsj_acad_prof').html('Porfavor, rellene todos los campos');
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
    $('#permiso').click(function(){limpiarSelect('#permiso');})
    $('.linea-inv').click(function(){$('.linea-inv').removeClass('is-invalid');})    
}
function init(){
    infoPers();
    //AntecAcad();
    limpiarValClick();
    limpiarClickProf()
    console.log('iniciando...')
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
    infoAcad();
    let usuario=leerDatPers();
    let profProg=leerDatProgDoc()
    console.log('usuario que lee el formulario')
    console.log(usuario)
    //ingresar docente
    if(valAcadProf(profProg['lineaInv'])){
        nuevo_inst=$('#inst_unid_trabajo').val()=='otro'?true:false;
        if(nuevo_inst){
            op='insert';
            $.ajax({
                async: false,
                type : 'POST',
                url : '../ajax/institucion.php',
                data : {op,nombre:profProg['instTrab']},
                success : function(response) {
                    let dato = JSON.parse(response);
                    $('#id_inst_doc').attr('name',dato);
                }
            })
        }
        profProg['instTrab']=$('#id_inst_doc').attr('name')==0?profProg['instTrab']:$('#id_inst_doc').attr('name')
        jQuery.extend(usuario,profProg);
        usuario.op='insert-update'
        $.post('../ajax/docente.php',usuario,function(response){
            let dato = JSON.parse(response);
            console.log(dato);
            $('#id_usuario').attr('name',dato[1]);
            $('#mnsj_row_acad_prof').show();
            $('#mnsj_acad_prof').removeClass('alert-danger');
            $('#mnsj_acad_prof').addClass('alert-success');
            $('#mnsj_acad_prof').html(dato[0]);
            setTimeout(function() {
                $("#mnsj_row_acad_prof").fadeOut(1500);
                AntecAcad();
            },3000);
        })       
    }
})
init()