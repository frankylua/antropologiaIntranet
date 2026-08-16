function mensajePerfilRespuesta(xhr, mensajePredeterminado){
  if(xhr.responseJSON && xhr.responseJSON.mensaje){
    return xhr.responseJSON.mensaje;
  }
  try {
    const respuesta=JSON.parse(xhr.responseText);
    return respuesta.mensaje || mensajePredeterminado;
  } catch(error) {
    return mensajePredeterminado;
  }
}

function mostrarMensajePerfil(mensaje, esError){
  $('#mnsj_row_edit').show();
  $('#mnsj_edit').toggleClass('alert-danger',esError);
  $('#mnsj_edit').toggleClass('alert-success',!esError);
  $('#mnsj_edit').text(mensaje);
}

function mostrarperfil(){
  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{op:'read-perfil'},
    success:function(admin){
      if(!admin || !admin.id_login){
        mostrarMensajePerfil('No fue posible cargar el perfil administrativo.',true);
        return;
      }
      if(admin.perfil==='docente'){
        window.location.href='../form-doc/info.docente.php';
        return;
      }
      const template=`<table class="table">
        <thead>
          <tr><th class="col-5"></th><th></th></tr>
        </thead>
        <tbody>
          <tr><td>Nombre</td><td>${cadenaMay(admin.nombre)}</td></tr>
          <tr><td>Correo Electrónico</td><td>${admin.correo}</td></tr>
          <tr><td>Rol</td><td>${admin.rol}</td></tr>
        </tbody>
      </table>`;
      $('#tabla_perfil').html(template);
    },
    error:function(xhr){
      mostrarMensajePerfil(mensajePerfilRespuesta(xhr,'No fue posible cargar el perfil administrativo.'),true);
    }
  });
}

function verEditAdmin(){
  $('#perfil_admin').hide();
  $('#form_admin_edit').show();
}

function verPerfil(){
  $('#perfil_admin').show();
  $('#form_admin_edit').hide();
}

$('#editar_perfil_admin').click(function(){
  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{op:'read-perfil'},
    success:function(admin){
      if(!admin || !admin.id_login){
        mostrarMensajePerfil('No fue posible cargar el perfil administrativo.',true);
        return;
      }
      if(admin.perfil==='docente'){
        window.location.href='../form-doc/info.docente.php';
        return;
      }
      verEditAdmin();
      $('#box-pass').hide().attr('name',0);
      $('#btn_cambiar_pass').show();
      $('#id_login').val(admin.id_login);
      $('#id_admin').val(admin.id_admin);
      $('#nom_admin').val(letraMay(admin.nombre));
      $('#correo').val(admin.correo);
      $('#pass').val('');
      $('#pass2').val('');
    },
    error:function(xhr){
      mostrarMensajePerfil(mensajePerfilRespuesta(xhr,'No fue posible cargar el perfil administrativo.'),true);
    }
  });
});

$('#btn_cambiar_pass').click(function(){
  $('#box-pass').show().attr('name',1);
  $('#btn_cambiar_pass').hide();
});

$('#btn-ver-comite').click(function(){
  verPerfil();
});

$('#form_admin_edit').submit(function(e){
  e.preventDefault();
  const nombre=guardar($('#nom_admin').val());
  const correo=guardar($('#correo').val());
  const pass=$('#pass').val();
  const pass2=$('#pass2').val();
  if(nombre==='' || correo===''){
    mostrarMensajePerfil('Nombre y correo electrónico son obligatorios.',true);
    return;
  }
  if(pass!==pass2){
    $('#pass').addClass('is-invalid');
    $('#pass2').addClass('is-invalid');
    mostrarMensajePerfil('Las contraseñas no coinciden.',true);
    return;
  }

  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{nombre,correo,pass,op:'update-perfil'},
    success:function(respuesta){
      if(!respuesta.ok){
        mostrarMensajePerfil(respuesta.mensaje || 'No fue posible editar el perfil.',true);
        return;
      }
      mostrarMensajePerfil(respuesta.mensaje,false);
      mostrarperfil();
      setTimeout(function(){
        $('#mnsj_row_edit').fadeOut(1500);
        verPerfil();
      },3000);
    },
    error:function(xhr){
      mostrarMensajePerfil(mensajePerfilRespuesta(xhr,'No fue posible editar el perfil.'),true);
    }
  });
});

verPerfil();
mostrarperfil();