function mostrarperfil(id_login){
    $.ajax({
        url:'../ajax/admin.php',
        type: 'POST',
        data: {op:'query_id',id_login},
        success: function(response){ 
         let listas = JSON.parse(response);
         console.log(listas);       
        let template ='';
        template=` <table class="table">
                      <thead>
                        <tr>
                          <th class="col-5"></th>
                          <th class=""></th>
                        </tr>
                      </thead>
                      <tbody>
                      <tr>
                      <td>Nombre</td>
                      <td>${cadenaMay(listas['nombre'])}</td>
                    </td>
                    <tr>
                      <td>Correo Electrónico</td>
                      <td>${listas['correo']}</td>
                    </td>
                    <tr>
                      <td>Rol</td>
                      <td>${listas['id_permiso']==1?'Administrador/a':'Comite Académico'}</td>
                    </td>
                      </tbody>
                    </table>
                 
        `
      $('#tabla_perfil').append(template);
    }
  })
}
mostrarperfil($('#id_usuario').attr('name'))

$('#editar_perfil_admin').click(function(){
  verEditAdmin()
  id_login = $(this).attr('name');
  console.log(id_login)
    $.ajax({
      url:'../ajax/admin.php',
      type: 'POST',
      data: {id_login,op:'query_id'},
      success: function(response){
        console.log(response);
         let admin = JSON.parse(response); 
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

function verEditAdmin(){
  $('#perfil_admin').hide()
  $('#form_admin_edit').show()
}

function verPerfil(){
  $('#perfil_admin').show()
  $('#form_admin_edit').hide()
  
}

$('#btn_cambiar_pass').click(function(){
  $("#box-pass").show();
  $('#btn_cambiar_pass').hide();
  $('#box-pass').attr('name',1);
})

$('#form_admin_edit').submit(function(e){
  e.preventDefault();
  let mensaje='';
  let nombre=guardar($('#nom_admin').val());
  let correo=guardar($('#correo').val());
  let pass=$('#pass').val();
  let pass2=$('#pass2').val();
  let permiso=$('#permiso').val();
  let id_login=$('#id_login').val()==''?0:$('#id_login').val();
  console.log('login es: '+id_login);           
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
      //valido los campos vacios
    if( ($('#box-pass').attr('name')==1&&pass!== pass2)){
      $('#col_pass').remove();
      $('#col_pass2').remove();
      $('#pass').addClass('is-invalid');
      $('#pass2').addClass('is-invalid');
      $('#mnsj_edit').html('<p>Las Contraseñas no coinciden</p>');
    }  else{
      op='insert-update';
      const lista_admin = {
        nombre,correo,pass,permiso,id_login,op
      }  
     
      console.log(lista_admin)
      $.post('../ajax/admin.php', lista_admin, function(response){
        console.log(response);
        mensaje=JSON.parse(response);
        //limpiar();
        $('#mnsj_row_edit').show();
        $('#mnsj_edit').removeClass('alert-danger');
        $('#mnsj_edit').addClass('alert-success');
        $('#mnsj_edit').html(mensaje);
        setTimeout(function() {
          $("#mnsj_row_edit").fadeOut(1500);
         
          verPerfil()
          },3000);
          
          
          
        })

    }   
      
  
});

verPerfil()