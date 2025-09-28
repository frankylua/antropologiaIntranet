

$.post('../ajax/institucion.php', {op:'read',tipo:'inst'}, function (response) {
    data = JSON.parse(response);
    templateSelect(data,'#inst_unid')
  })
  $.post('../ajax/institucion.php', {op:'read',tipo:'inst'}, function (response) {
    data = JSON.parse(response);
    templateSelect(data,'#inst_unid_trabajo')
  })
  $.post('../ajax/pais.php', {op:'read'}, function (response) {
    data = JSON.parse(response);
    templateSelect(data,'#pais_nac')
  })
  $.post('../ajax/institucion.php', {op:'read'}, function (response) {
    data = JSON.parse(response);
    templateSelect(data,'#pais_res')
  })


  
function infoPers(){
    $('#mnsj_row_per').hide();
    $('#info_acad').hide();
    $('#antec_acad').hide();
    $('#info_pers').show();
    
}
function infoAcad(){
    $('#antec_acad').hide();
    $('#info_pers').hide();
    $('#info_acad').show();
    limpiarCampos();
} 
function AntecAcad(){
    $('#antec_acad').show();
    $('#info_pers').hide();
    $('#info_acad').hide();
}
//validar datos personales

$('#salir_form').click(function(){
    location.href='../index.php' 
})
// volver de datos personales
$('#volver_index').click(function(){
location.href='../index.php'
})
//lista lectura datos peronales
function leerDatPers(){
    let nombres=guardar($('#nombres').val())
    let ap_pat=guardar($('#ap_pat').val());
    let ap_mat=guardar($('#ap_mat').val());
    let fech_nac=$('#fech_nac').val();
    let documento=$('#documento').val();
    let nro_doc=$('#nro_doc').val();
    let pais_nac=$('#pais_nac').val();
    let genero=$('#genero').val();
    let pais_res=$('#pais_res').val();
    let region=guardar($('#region').val());
    let comuna=guardar($('#comuna').val());
    let telefono=$('#telefono').val();
    let direccion=guardar($('#direccion').val());
    let cont_em=guardar($('#cont_em').val());
    let tel_em=$('#tel_em').val();
    //datos tabla login
    let correo=guardar($('#correo').val());
    let pass=$('#pass').val();
    let pass2=$('#pass2').val();
    //pueblo
    let pertenece_pueblo=$('input:radio[name=pueblo]:checked').val();
    let pueblo=null;
    if(pertenece_pueblo == 'si'){
        pueblo=$('#select_pueb').val();
        // dato tabla pueblo
        if(pueblo == 'otro'){
            pueblo=guardar($('#otro_pueb').val());
        }
    }
    usuario={nombres,ap_pat,ap_mat,fech_nac,documento,nro_doc,pais_nac,genero,pais_res,region,comuna,telefono,direccion,cont_em,tel_em,correo,pass,pass2,pueblo}
    return usuario;
}
//lista lectura datos programa docente
function leerDatProgDoc(){
    let instTrab=$('#inst_unid_trabajo').val()=='otro'?espacios($('#otro_inst_doc').val()):$('#inst_unid_trabajo').val();
    let catAcad=$('#cat_acad').val();
    let anio_ing=$('#anio_ing').val();;
    let vinculo=$('#vinculo').val();
    let lineaInv=[]
    let permiso= $('#permiso').val();  
    let permisos=permiso==1?[1,4]:permiso==2?[2,4]:permiso==3?[3,4]:0;
    // let id_login=$('#id_login').val()==''?0:$('#id_login').val();
    $("input:checkbox:checked").each(function() {
        lineaInv.push($(this).val())
    });
    datProg={instTrab,catAcad,anio_ing,vinculo,lineaInv,permisos}
    return datProg

}

function valMail(){
    let correo=$('#correo').val();
    const lista={correo,op:'val-mail'};
    let respuesta=false;
    $.ajax({
        url: '../ajax/estudiante.php',
        type: 'POST',
        data: lista,
        async: false,
        success: function(response){
        respuesta=JSON.parse(response);
        if(respuesta.length>0){
            respuesta= true;
            console.log('respuesta de ajax')
        }else{
            respuesta=false;
        }
        }
          
    })
    return respuesta;

}

// validar datos personales
function validarDatosPers(){//campos variable para dar accion de cambiar clases de validacion
    let mensaje='';        
    validCampoVacio('#ap_mat');
    validCampoVacio('#ap_pat');
    validCampoVacio('#fech_nac');
    validCampoVacio('#nro_doc');
    validSelect('#pais_nac');
    validSelect('#genero');
    validSelect('#pais_res');
    validCampoVacio('#region');
    validCampoVacio('#comuna');
    validCampoVacio('#telefono');
    validCampoVacio('#direccion');
    validCampoVacio('#cont_em');
    validCampoVacio('#tel_em');
    validCampoVacio('#correo');
    validCampoVacio('#otro_pueb');
    validSelect('#select_pueb');
    validCampoVacio('#pass2')
    validCampoVacio('#nombres')
    validCampoVacio('#pass2')
    // if(valMail()){
    //     mensaje+='el correo ya existe '
    // }else{
    //     mensaje+='no existe'
    // }
     if(validCampoVacio('#pass')||validCampoVacio('#pass2')||validCampoVacio('#nombres') || validCampoVacio('#ap_mat') || validCampoVacio('#ap_pat') || validCampoVacio('#fech_nac') || validCampoVacio('#nro_doc') ||validSelect('#pais_nac') || validSelect('#genero') || validSelect('#pais_res') || validCampoVacio('#region')|| validCampoVacio('#comuna') || validCampoVacio('#telefono') || validCampoVacio('#direccion') || validCampoVacio('#cont_em') || validCampoVacio('#tel_em') || validCampoVacio('#correo')  || validSelect('#pueblo')){
        mensaje+='Rellene todos los campos';
        console.log('rellene todos los campos')
        $('#mnsj_row_per').show();
        $('#mnsj_per').addClass('alert-danger');
        $('#mnsj_per').html(mensaje);
        return false;
    }else if($('#pass').val()!==$('#pass2').val()){
        $('#mnsj_row_per').show();
        $('#mnsj_per').addClass('alert-danger');
        $('#pass').addClass('is-invalid');
        $('#pass2').addClass('is-invalid');
        mensaje ='Las contraseñas no coinciden';
        $('#mnsj_per').html(mensaje);
        return false;
    }else if(valMail()){
        mensaje='El correo electrónico ya se encuentra registrado';
        $('#correo').addClass('is-invalid');
        $('#mnsj_row_per').show();
        $('#mnsj_per').addClass('alert-danger');
        $('#mnsj_per').html(mensaje);
        return false;
    }else{
        return true;
    }

   
}       
//limpia validaciones
function limpiarValClick(){
    //limpiar datos personales
    $('#nombres').click(function(){limpiarInput('#nombres');})
    $('#ap_pat').click(function(){limpiarInput('#ap_pat');})
    $('#ap_mat').click(function(){limpiarInput('#ap_mat');})
    $('#fech_nac').click(function(){limpiarInput('#fech_nac');})
    $('#nro_doc').click(function(){limpiarInput('#nro_doc');})
    $('#pais_nac').click(function(){limpiarSelect('#pais_nac');})
    $('#genero').click(function(){limpiarSelect('#genero');})
    $('#pais_res').click(function(){limpiarSelect('#pais_res');})
    $('#region').click(function(){limpiarInput('#region');})
    $('#comuna').click(function(){limpiarInput('#comuna');})
    $('#telefono').click(function(){limpiarInput('#telefono');})
    $('#direccion').click(function(){limpiarInput('#direccion');})
    $('#cont_em').click(function(){limpiarInput('#cont_em');})
    $('#tel_em').click(function(){limpiarInput('#tel_em');})
    $('#correo').click(function(){limpiarInput('#correo');})
    $('#pass').click(function(){limpiarInput('#pass');})
    $('#pass2').click(function(){limpiarInput('#pass2');})
    $('#select_pueb').click(function(){limpiarSelect('#select_pueb');})
    $('#otro_pueb').click(function(){limpiarInput('#otro_pueb');})
    //limpiar grado academico
    $('#grad_acad').click(function(){limpiarSelect('#grad_acad');})
    $('#grado').click(function(){limpiarSelect('#grado');})
    $('#inst_grado').click(function(){limpiarSelect('#inst_grado');})
    $('#s_tit').click(function(){limpiarSelect('#s_tit');})
    //limpiar publicacion
    $('#anio').click(function(){limpiarSelect('#anio');})
    $('#autores').click(function(){limpiarInput('#autores');})
    $('#nombre').click(function(){limpiarInput('#nombre');})
    $('#estado').click(function(){limpiarSelect('#estado');})
    $('#titulo').click(function(){limpiarInput('#titulo');})
    $('#indizacion').click(function(){limpiarSelect('#indizacion');})
    $('#issn').click(function(){limpiarInput('#issn');})
    $('#tipo_libro').click(function(){limpiarSelect('#tipo_libro');})
    $('#rol_libro').click(function(){limpiarSelect('#rol_libro');})
    $('#lugar').click(function(){limpiarInput('#lugar');})
    $('#tipo_pub').click(function(){limpiarSelect('#tipo_pub');})
    $('#desc_pub').click(function(){limpiarSelect('#desc_pub');})













}
function limpiarCampos(){
    limpiarInput('#nombres');
    limpiarInput('#ap_pat');
    limpiarInput('#ap_mat');
    limpiarInput('#fech_nac');
    limpiarInput('#nro_doc');
    limpiarSelect('#pais_nac');
    limpiarSelect('#pais_res');
    limpiarSelect('#genero');
    limpiarInput('#region')
    limpiarInput('#comuna');
    limpiarInput('#telefono');
    limpiarInput('#direccion');
    limpiarInput('#cont_em');
    limpiarInput('#tel_em');
    limpiarInput('#correo');
    limpiarInput('#pass');
    limpiarSelect('#select_pueb');
    limpiarInput('#otro_pueb');
}

$('body').click(function(){
    $('#mnsj_row_grad').hide()
    $('#mnsj_row_pub').hide()
    $('#mnsj_row_cong').hide()
    $('#mnsj_row_postdoc').hide()
    $('#mnsj_row_proy').hide()
    $('#mnsj_row_tesis').hide()
    //$('#mnsj_row_per').hide()
    //$('#list_titulos').hide();
    //$('#list_proy').hide();
    $('#list_editorial').hide();
    $('#list_cong').hide();
    $('#list_part').hide();
    //$('#list_prof').hide();
    $('#list_est').hide();
    $('#list_prof_curso').hide();
})
//agregar botones editar a los formularios de antecedenetes academicos
$('#btn_pers').click(function(){
    if(validarDatosPers()){
        usuario=leerDatPers();
        infoAcad();
    }
    
})
function btn_editar_acad(contenedor,id){
    $(contenedor).append(' <div class="row mt-5 justify-content-between "><div class="col-md-4 mb-3"><button type="button" class="col-12 btn btn-dark col-6 verFicha" id="'+id+'" >Volver</button></div><div class="col-md-4 mb-3"><button type="submit" class="col-12 btn btn-dark col-6">Editar</button></div></div>')
}





        

    


    






        
        

