
function mostrarCurso(){
    $.ajax({
        url:'../ajax/curso.php',
        type: 'POST',
        data: {op:'read'},
        success: function(response){ 
         let listas = JSON.parse(response);      
        let template ='';
        listas.forEach(list => {
        template += `
        <tr>
        <td >${cadenaMay(list['nom_curso'])}</td>
        <td>${list['periodo']==1?'I Semestre':list['periodo']==2?'II Semestre':'III Semestre'}</td>
        <td >${list['anio_curso']}</td>
        <td class="text-center"><button type="button" class="btn btn-link link-success btn-sm verCurso text-center" id="${list[0]}">Ver</button></td>
        <td class="text-center"><button type="button" class="btn btn-link link-danger btn-sm eliminarCurso" id="${list[0]}" >Eliminar</button></td>
        </tr>
        
        `});
      $('#datos_curso').html(template);
    }
  })
}    
function  mensajeError(mensaje){
$('#mnsj_row').show();
$('#mnsj').addClass('alert-danger');
$('#mnsj').html(mensaje);
}
// dejar input vacios
function limpiar(){
    $('#creditos').val('');
    $('#docente_curso').val('');
    $('#car_hor').val('');
    $('#id_curso').val('');
    $('#nom_curso').val(0);
    $('#caracter').val(0);
    $('#periodo').val(0);
    $('#anio_curso').val(0);
    $('#prog_curso').val('');
}
function limpiarValidacion(){
limpiarSelect('#nom_curso');
limpiarInput('#creditos');
limpiarInput('#docente_curso');
limpiarInput('#car_hor');
limpiarSelect('#caracter');
limpiarSelect('#periodo');
limpiarSelect('#anio_curso');
}
function mostrarform(flag){
if (flag)
{
    limpiarValidacion();
    // $("#btn_cambiar_pass").hide();
    // $("#box-pass").show();
    $("#list_curso").hide();
    $("#form_curso").show();
    // $("#btnGuardar").prop("disabled",false);
    $(".titulo_curso").hide();
    }
    else
    {
    $("#list_curso").show();
    $("#form_curso").hide();
    $(".titulo_curso").show();
    }
}
function cancelarform(){
    mostrarform(false);
    //limpiar();
}
function detalleCurso(flag){
    if(flag){
        mostrarform(false);
        $('#list_curso').hide();
        $('#det_curso').show();
    }else{
        $('#det_curso').hide();  
    }

}
//ir a formulario ingreso
$('#btn-agr-curso').click(function(){
    mostrarform(true);
})
// ir a tabla cursos
$('#btn-ver-curso').click(function(){
    cancelarform(true);
    limpiar();
})
// ver detalle de curso por id
$('.detalleCurso').click(function(){
    cancelarform(true);
    detalleCurso(false);
})
$('body').click(function(){
    $('#mnsj_row').hide();
})
$('#form_curso').submit(function(e){
    e.preventDefault();
    let campos_llenos;
    let mensaje='';
    let nombre=$('#nom_curso').val();
    let creditos=$('#creditos').val();
    let docente=$('#docente_curso').attr('name');
    let carga_hor=$('#car_hor').val();
    let caracter=$('#caracter').val();
    let periodo=$('#periodo').val();
    let anio_curso=$('#anio_curso').val();
    let prog_curso=$('#prog_curso')[0].files[0];   
    let id_curso=$('#id_curso').val();
    let editado=id_curso==0?false:true;
    if(nombre==0||creditos==''||docente==0||car_hor==''||caracter==0 || periodo==0 || anio_curso==0||$('#prog_curso').val()==''){
        if(editado){
            limpiarValidacion()
            $.ajax({
            url:'../ajax/curso.php',
            type: 'POST',
            data: {op:'query_id',id_curso:id_curso},
            async:false,
            success: function(response){ 
                let dato = JSON.parse(response);
                nombre=nombre==0?dato['nombre_curso']:nombre;
                creditos=creditos==''?dato['creditos']:creditos;
                caracter=caracter==0?dato['caracter']:caracter;
                periodo=periodo==0?dato['periodo']:periodo;
                anio_curso=anio_curso==0?dato['anio_curso']:anio_curso;
                carga_hor=carga_hor==''?dato['carga_hor']:carga_hor;
                docente=docente==''?dato['profesor']:docente;
                nombre=nombre==''?dato['nombre_curso']:nombre;
                prog_curso=$('#prog_curso').val()==''?$('#prog_curso').attr('name'):dato['arch_prog'];
            }
            })
        }else{
            validSelect('#nom_curso');
            validCampoVacio('#creditos');
            validCampoVacio('#docente_curso');
            validSelect('#caracter');
            validSelect('#periodo');
            validSelect('#anio_curso');
            mensajeError('<p>Por favor rellene todos los campos requeridos</p>');
            campos_llenos=false;
        }           
    }else{
        //console.log('extencion de archivo....'+prog_curso.type)
        if($('#prog_curso').val()!=''){
            if(prog_curso.type == "application/pdf"){
                campos_llenos=true;
            }else{
                $('#prog_curso').removeClass('is-valid');
                $('#prog_curso').addClass('is-invalid');
                mensajeError('<p>Suba un archivo con extención .pdf</p>')
                campos_llenos=false;
            }
        }
    }
    //limpiar campos
    $('#nom_curso').click(function(){limpiarSelect('#nom_curso')});
    $('#creditos').click(function(){limpiarInput('#creditos')});
    $('#docente_curso').click(function(){limpiarInput('#docente_curso')});
    $('#car_hor').click(function(){limpiarInput('#car_hor')});
    $('#prog_curso').click(function(){limpiarInput('#prog_curso')});
    $('#caracter').click(function(){limpiarSelect('#caracter')});
    $('#periodo').click(function(){limpiarSelect('#periodo')});
    $('#anio_curso').click(function(){limpiarSelect('#anio_curso')});

    //valido los campos vacios
    if((campos_llenos || editado)){
        op='insert-update';
        curso=new FormData();
        curso.append('nombre',nombre);
        curso.append('creditos',creditos);
        curso.append('docente',docente);
        curso.append('carga_hor',carga_hor);
        curso.append('caracter',caracter);
        curso.append('periodo',periodo);
        curso.append('anio_curso',anio_curso);
        curso.append('prog_curso',prog_curso); 
        curso.append('id_curso',id_curso);
        curso.append('op',op); 
        curso.append('arch_actual',$('#prog_curso').attr('name'));    

        $.ajax({
            type: "POST",           
            contentType: false,
            processData: false,
            data: curso,
            url: "../ajax/curso.php",
            success: function(response) {
                mensaje=JSON.parse(response);
                $('#mnsj_row').show();
                $('#mnsj').removeClass('alert-danger');
                $('#mnsj').addClass('alert-success');
                $('#mnsj').html(mensaje);
                setTimeout(function() {
                    $("#mnsj_row").fadeOut(1500);
                    cancelarform();
                    mostrarCurso();
                    limpiar();
            },3000);
            }
        })
    }  
});
//ver curso 
$('body').on('click','.verCurso',function(){
id_curso = $(this).attr('id');
$.ajax({
url:'../ajax/curso.php',
type: 'POST',
data: {id_curso,op:'query_id'},
success: function(response){
    let curso = JSON.parse(response); 
    nombre=curso['nombres']+' '+curso['ap_pat']+' '+curso['ap_pat'];
    nombre=cadenaMay(nombre);
    $('#id_curso').attr('value',curso['id_curso']);
    $('#nom').html(curso['nom_curso']);
    $('#cred').html(curso['creditos']);
    $('#car').html(curso['caracter']==1?'Obligatorio':'Seminario');
    $('#per').html(curso['periodo']==1?'I Semestre':curso['periodo']==2?'II Semestre':'III Semestre');
    $('#year').html(curso['anio_curso']);
    $('#carg').html(curso['carga_hor']+'  horas cronológicas');
    $('#prof').html(nombre);
    $('#prog').html('<a href="../files/prog_curso/'+curso['arch_prog']+'" class="link-secondary" download="'+curso['arch_prog']+'">Descargar Programa</a>')
    detalleCurso(true);
}
})
})
// editar curso
$('body').on('click','#editar_curso',function(){
    mostrarform(true);
    detalleCurso(false);
    id_curso = $('#id_curso').val();
    $.ajax({
    url:'../ajax/curso.php',
    type: 'POST',
    data: {id_curso:id_curso,op:'query_id'},
    success: function(response){
        let curso = JSON.parse(response); 
        $('#id_curso').attr('value',curso['id_curso']);
        $('#nom_curso').val(curso['id_nom_curso']);
        $('#creditos').val(curso['creditos']);
        $('#caracter').val(curso['caracter']);
        $('#periodo').val(curso['periodo']);
        $('#anio_curso').val(curso['anio_curso']);
        $('#car_hor').val(curso['carga_hor']);
        $('#docente').val(curso['profesor']);
        $('#prog_curso').attr('name',curso['arch_prog'])
       
    }
    })
    })
//eliminar curso
$('body').on('click','.eliminarCurso',function(){
id_curso = $(this).attr('id');
$.ajax({
    url:'../ajax/curso.php',
    type: 'POST',
    data: {id_curso,op:'delete'},
    success: function(response){
    let mensaje = JSON.parse(response);
    $('#mnsj_elim').show();
    $('#eliminado').addClass('alert-secondary');
    $('#eliminado').html(mensaje);
    mostrarCurso();
    setTimeout(function() {
        $("#mnsj_elim").fadeOut(1500);
    },1000);
    }
})
})

$('#docente_curso').keyup(function(){    
    $('#list_prof_curso').hide();
    busqueda=$('#docente_curso').val();
    if(busqueda!==''){       
        $.ajax({
            url: '../ajax/docente.php',
            type: 'POST',
            data: {op:'read_prof',busqueda},
            success: function(response){
            let profes_curso = JSON.parse(response);
            let template ='';            
            if(profes_curso=='' && $('#docente_curso').attr('name')==''){
            template='<li class="list-group-item text-rosado">Ingrese un nombre válido</li>';                
            }else{
                $('#docente_curso').attr('name','');
                profes_curso.forEach(list => {
                prof=list[1]+' '+list[2]+' '+list[3];
                prof=cadenaMay(prof);
                template += `
                
                <li class='list-group-item listProfCurso' id='${list[0]}' name='${prof}'> ${prof}</li>`});

            }  
          $('#list_prof_curso').html(template);
          $('#list_prof_curso').show();
             }
         })

    }
        

})
$('body').on('click','.listProfCurso',function(){
    id=$(this).attr('id');
    prof=$(this).attr('name');
    $('#docente_curso').val(prof);
    $('#docente_curso').attr('name',id);
    $('#list_prof_curso').hide();

})
$('#creditos').keyup(function(){
    car_hor=parseInt($('#creditos').val())*28;
    $('#car_hor').val(car_hor);    
})

$('#creditos').click(function(){
    car_hor=parseInt($('#creditos').val())*28;
    $('#car_hor').val(car_hor);    
})

function init(){
    $('#mnsj_row').hide();
    $('#det_curso').hide();
    mostrarform(false);
    mostrarCurso();
    $('.loadPage').fadeOut();
    $('#mnsj_elim').hide();
    ajaxSelect('#nom_curso','../ajax/curso.php','Seleccione','read_cursos');
    anios('#anio_curso',2006);
    $('#car_hor').prop('disabled',true)
    
  }
  init();

    
        
        
    
    
            
        
       
        
        
        
        
        
    




