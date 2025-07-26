
function mostrarAdmin(){
  $.ajax({
      url:'../ajax/admin.php',
      type: 'POST',
      data: {op:'read'},
      success: function(response){ 
       let listas = JSON.parse(response);
       console.log(listas);       
      let template ='';
      listas.forEach(list => {
        template += `
      
      <tr>
      
      <td >${cadenaMay(list[3])}</td>
      <td>${list[4]}</td>
      <td>${list[5]==1?'Administrador':'C.A.'}</td>
      <td><button type="button" class="btn btn-link link-success btn-sm " id="">Agregar Docencia</button></td>
      <td><button type="button" class="btn btn-link link-success btn-sm editarAdmin" id="${list[0]}">Editar</button></td>
      <td><button type="button" class="btn btn-link link-danger btn-sm eliminarAdmin" id="${list[0]}" >Eliminar</button></td>
      </tr>
   
      `});
    $('#datos_admin').html(template);
  }
})
}
function  mensajeError(mensaje){
  $('#mnsj_row').show();
  $('#mnsj').addClass('alert-danger');
  $('#mnsj').html(mensaje);
}
function limpiar(){
  $('#nom_admin').val('');
  $('#correo').val('');
  $('#pass').val('');
  $('#pass2').val('');
  $('#id_login').val('');
  $('#id_admin').val('');
  $('#id_per_log').val('');
  limpiarInput('#nom_admin','#col_nom_admin');
  limpiarInput('#correo','#col_correo');
  limpiarInput('#pass','#col_pass');
  limpiarInput('#pass2','#col_pass2');
  limpiarSelect('#permiso','#col_tipo');
}
function mostrarform(flag){
  if (flag)
  {
      $("#btn_cambiar_pass").hide();
      $("#box-pass").show();
      $("#list_admin").hide();
      $("#form_admin").show();
      // $("#btnGuardar").prop("disabled",false);
      $(".titulo_comite").hide();
    }
    else
    {
      $("#list_admin").show();
      $("#form_admin").hide();
      $(".titulo_comite").show();
    }
  }
function cancelarform(){
    mostrarform(false);
    
  }
  $('#btn-agr-comite').click(function(){
      mostrarform(true);
  })
  $('#btn-ver-comite').click(function(){
      cancelarform(true);
  })
  $('body').click(function(){
    $('#mnsj_row').hide();
  })
  $('#form_admin').submit(function(e){
      e.preventDefault();
      let campos_llenos;
      let mensaje='';
      let nombre=guardar($('#nom_admin').val());
      let correo=guardar($('#correo').val());
      let pass=$('#pass').val();
      let pass2=$('#pass2').val();
      let permiso=$('#permiso').val();
      let id_login=$('#id_login').val()==''?0:$('#id_login').val();
      let editado=id_login==0?false:true;
      console.log('login es: '+id_login);
      if(nombre==''||correo==''||pass==''||pass2==''||permiso==0){
        if(editado){          
            $.ajax({
              url:'../ajax/admin.php',
              type: 'POST',
              data: {op:'query_id',id_login:id_login},
              async:false,
              success: function(response){ 
               let dato = JSON.parse(response);
               console.log(dato)
               nombre=nombre==''?dato['nombre']:nombre;
               correo=correo==''?dato['correo']:correo;
               pass=pass==''?dato['pass']:pass;              
               permiso=permiso==0?dato['id_permiso']:permiso;
              }
            })          
        }else{
          validCampoVacio('#nom_admin');
          validCampoVacio('#correo');
          validCampoVacio('#pass');
          validCampoVacio('#pass2');
          validSelect('#permiso');
          console.log('entra if')
          mensajeError('<p>Por favor rellene todos los campos requeridos</p>');
          campos_llenos=false;
          //validacion con clases
        }
      }else{
        campos_llenos=true;
      }
      
      
        
        
      //limpiar campos
      $('#nom_admin').click(function(){limpiarInput('#nom_admin','#col_nom_admin')})
      $('#correo').click(function(){limpiarInput('#correo','#col_correo')})
      $('#pass').click(function(){limpiarInput('#pass','#col_pass')})
      $('#pass2').click(function(){limpiarInput('#pass2','#col_pass2')})
      $('#permiso').click(function(){limpiarSelect('#permiso','#col_tipo')})
      //valido los campos vacios
      
      
      if((campos_llenos || editado)){
        if( (pass !== pass2 && !editado) || ($('#box-pass').attr('name')==1&&pass!== pass2)){
          $('#col_pass').remove();
          $('#col_pass2').remove();
          $('#pass').addClass('is-invalid');
          $('#pass2').addClass('is-invalid');
          mensajeError('<p>Las Contraseñas no coinciden</p>');
        }else {         
          op='insert-update';
          const lista_admin = {
            nombre,correo,pass,permiso,id_login,op
          }            
        $.post('../ajax/admin.php', lista_admin, function(response){
          console.log(response);
          mensaje=JSON.parse(response);
          //limpiar();
          $('#mnsj_row').show();
          $('#mnsj').removeClass('alert-danger');
          $('#mnsj').addClass('alert-success');
          $('#mnsj').html(mensaje);
          setTimeout(function() {
            $("#mnsj_row").fadeOut(1500);
             mostrarform(false);
             mostrarAdmin();
             limpiar();
            },3000);
            
            
            
          })
        }  
      }
  });
  //editar admin
  $('body').on('click','.editarAdmin',function(){
    id_login = $(this).attr('id');
    $.ajax({
      url:'../ajax/admin.php',
      type: 'POST',
      data: {id_login:id_login,op:'query_id'},
      success: function(response){
        let admin = JSON.parse(response); 
        console.log(admin);
        mostrarform(true);
        $("#box-pass").hide();
        $('#btn_cambiar_pass').show();
        $('#id_login').attr('value',admin['id_login']);
        $('#id_admin').attr('value',admin['id_admin']);
        $('#id_per_log').attr('value',admin['id_per_log']);
        $('#nom_admin').val(letraMay(admin['nombre']));
        $('#correo').val(admin['correo']);
        $('#permiso').val(admin['id_permiso']);
      }
    })
  })
  //eliminar admin
  $('body').on('click','.eliminarAdmin',function(){
    id_login = $(this).attr('id');
    $.ajax({
      url:'../ajax/admin.php',
      type: 'POST',
      data: {id_login:id_login,op:'delete'},
      success: function(response){
        let mensaje = JSON.parse(response);
        $('#mnsj_elim').show();
        $('#eliminado').addClass('alert-success');
        $('#eliminado').html(mensaje);
        mostrarAdmin();
        setTimeout(function() {
          $("#mnsj_elim").fadeOut(1500);
        },3000);
      }
  })
})

$('#btn_cambiar_pass').click(function(){
  $("#box-pass").show();
  $('#btn_cambiar_pass').hide();
  $('#box-pass').attr('name',1);
})

function init(){
  $('#mnsj_row').hide();
  mostrarform(false);
  mostrarAdmin();
  $('.loadPage').fadeOut();
  $('#mnsj_elim').hide();
  
}
init();
      
 


  

  

        
        
          
          




    

    






