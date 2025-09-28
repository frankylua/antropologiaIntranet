otroCampo('otro_instit','#otra_inst','#inst_unid','Institucion/Unidad Académica'); 
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
//buscador de profesor guia
$('#prof_guia').keyup(function(){
    
    $('#list_prof').hide();
    busqueda=$('#prof_guia').val();
    if(busqueda!==''){       
        $.ajax({
            url: '../ajax/docente.php',
            type: 'POST',
            data: {op:'read_prof',busqueda},
            success: function(response){
            let profes = JSON.parse(response);
            let template ='';
             console.log(profes)
            if(profes=='' && $('#prof_guia').attr('name')==''){
            template='<li class="list-group-item text-rosado">Ingrese un nombre válido</li>';
                
            }else{
                $('#prof_guia').attr('name','');
                profes.forEach(list => {
                    prof=list[1]+' '+list[2]+' '+list[3];
                    prof=cadenaMay(prof);
                    template += `
                
                <li class='list-group-item listProf' id='${list[0]}' name='${prof}'> ${prof}</li>`});

            }  
          $('#list_prof').html(template);
          $('#list_prof').show();
             }
         })

    }
        

})
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
    p=$('#prof_guia').attr('name')==0
    l=linea=='';//lineas de investigación
    if(p||i||o||s||f||t||l||r){
        if(l){
            $('.linea-inv').addClass('is-invalid');
        }
        if(p){
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
    //AntecAcad();
    let usuario=leerDatPers();
    let promedio=$('#promedio').val();
    let inst=$('#inst_unid').val();
    let trabaja=$('input:radio[name=trab]:checked').attr("value");
    let profesor=$('#prof_guia').attr('name');
    let orientacion=$('#orient').val();
    let sit_ocup=guardar($('#sit_ocup').val());
    let fech_ing=$('#fech_ing').val();
    let fech_grad=$('#fech_grad').val();
    let tipo_est=$('#tipo_est').val();
    let permisos=[5]
    if(tipo_est==2||tipo_est==3){
        permisos.push(3)
    }
    let lineaInv=[]
    
    $("input:checkbox:checked").each(function() {
        lineaInv.push($(this).val())
    });
    console.log(lineaInv)
    
    let id_login=$('#id_login').val()==''?0:$('#id_login').val();
    //ingresar grado academico
    if(valDatAcad(lineaInv)){
        nuevo_inst=$('#inst_unid').val()=='otro'?true:false;
        if(nuevo_inst){
            op='insert';
            $.ajax({
                async: false,
                type : 'POST',
                url : '../ajax/institucion.php',
                data : {op,nombre:inst},
                success : function(response) {
                    let dato = JSON.parse(response);
                    $('#id_inst_est').attr('name',dato);
                }
            })
        }
        inst=$('#id_inst_est').attr('name')==0?inst:$('#id_inst_est').attr('name')
        op='insert-update';
        // nota: para editar datos personales ocultar botones de validacion y aparecer submit del form
        let usuAcad={inst,trabaja,orientacion,sit_ocup,fech_ing,fech_grad,tipo_est,id_login,promedio,op,profesor,lineaInv,permisos}
        jQuery.extend(usuario,usuAcad);
        console.log('usuario:')
        console.log(usuario)
        $.post('../ajax/estudiante.php',usuario,function(response){
            console.log(response)
            let dato = JSON.parse(response);
            $('#id_usuario').attr('name',dato[1]);
            $('#mnsj_row_acad_est').show();
            $('#mnsj_acad_est').removeClass('alert-danger');
            $('#mnsj_acad_est').addClass('alert-success');
            $('#mnsj_acad_est').html(dato[0]);
            setTimeout(function() {
                $("#mnsj_row_acad_est").fadeOut(1500);
                AntecAcad();
            },3000);
        })   

    }
    //if(editado){
    //         $.ajax({
    //         url:'../ajax/curso.php',
    //         type: 'POST',
    //         data: {op:'query_id',id_curso:id_curso},
    //         async:false,
    //         success: function(response){ 
    //             let dato = JSON.parse(response);
    //             console.log(dato)
    //             nombre=nombre==0?dato['nombre_curso']:nombre;
    //             creditos=creditos==''?dato['creditos']:creditos;
    //             caracter=caracter==0?dato['caracter']:caracter;
    //             periodo=periodo==0?dato['periodo']:periodo;
    //             anio_curso=anio_curso==0?dato['anio_curso']:anio_curso;
    //             carga_hor=carga_hor==''?dato['carga_hor']:carga_hor;
    //             docente=docente==''?dato['profesor']:docente;
    //             nombre=nombre==''?dato['nombre_curso']:nombre;
    //             prog_curso=prog_curso==''?dato['prog_curso']:prog_curso;
    //         }
    //         })
    //}else{
        if(validarDatosPers()){
            let editado=id_login==0?false:true;
            console.log('campos llenos!!!!!ehehehehe')
            // $.post('../ajax/estudiante.php', usuario, function(response){
            //     let dato = JSON.parse(response);
            //     console.log(dato);
            // })
        }
    //}
    
    //valDatAcad();
    //if(nombre==0||creditos==''||docente==''||car_hor==''||caracter==0 || periodo==0 || anio_curso==0){
    //         validSelect('#nom_curso');
    //         validCampoVacio('#creditos');
    //         validCampoVacio('#docente');
    //         validCampoVacio('#car_hor');
    //         validSelect('#caracter');
    //         validSelect('#periodo');
    //         validSelect('#anio_curso');
    //         mensajeError('<p>Por favor rellene todos los campos requeridos</p>');
    //         campos_llenos=false;
    //     }           
    // }else{
    //     campos_llenos=true;
    // }
    // //limpiar campos
    // $('#nom_curso').click(function(){limpiarSelect('#nom_curso')});
    // $('#creditos').click(function(){limpiarInput('#creditos')});
    // $('#docente').click(function(){limpiarInput('#docente')});
    // $('#car_hor').click(function(){limpiarInput('#car_hor')});
    // $('#caracter').click(function(){limpiarSelect('#caracter')});
    // $('#periodo').click(function(){limpiarSelect('#periodo')});
    // $('#anio_curso').click(function(){limpiarSelect('#anio_curso')});

    // //valido los campos vacios
    // if((campos_llenos || editado)){
    //     op='insert-update';
    //     console.log('campos llenos/editado');
    //     const lista_curso = {
    //         nombre,creditos,docente,carga_hor,caracter,periodo,anio_curso,id_curso,prog_curso,op
    //     }
            // mensaje=JSON.parse(response);
            // limpiar();
            // $('#mnsj_row').show();
            // $('#mnsj').removeClass('alert-danger');
            // $('#mnsj').addClass('alert-success');
            // $('#mnsj').html(mensaje);
            // setTimeout(function() {
            //     $("#mnsj_row").fadeOut(1500);
            //     cancelarform();
            //     mostrarCurso();
            // },3000);
            // }  
})        
function init(){
    $('#mnsj_row_acad_est').hide();

      $.post('../ajax/estudiante.php', {op:'_tipo'}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#tipo_est')
        })
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
    






    


 
    
        

        
