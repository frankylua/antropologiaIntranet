function mensajeRespuesta(xhr, mensajePredeterminado){
  if(xhr.responseJSON && xhr.responseJSON.mensaje){
    return xhr.responseJSON.mensaje;
  }
  try {
    const respuesta = JSON.parse(xhr.responseText);
    return respuesta.mensaje || mensajePredeterminado;
  } catch (error) {
    return mensajePredeterminado;
  }
}

function textoSeguro(valor){
  return $('<div>').text(valor == null ? '' : valor).html();
}

function mostrarErrorLista(mensaje){
  $('#mnsj_elim').show();
  $('#eliminado').removeClass('alert-success').addClass('alert-danger');
  $('#eliminado').text(mensaje);
}

function mostrarAdmin(){
  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{op:'read'},
    success:function(cuentas){
      if(!Array.isArray(cuentas)){
        mostrarErrorLista('No fue posible cargar las cuentas administrativas.');
        return;
      }
      let template='';
      cuentas.forEach(function(cuenta){
        const conDocencia=cuenta.representacion==='con_docencia';
        const accionDocencia=conDocencia
          ? '<span class="text-muted">Incorporada</span>'
          : `<button type="button" class="btn btn-link link-success btn-sm agregarDocencia" data-login="${cuenta.id_login}">Agregar Docencia</button>`;
        const accionEditar=conDocencia
          ? (cuenta.es_actor
              ? '<a class="btn btn-link link-success btn-sm" href="../form-doc/info.docente.php">Mi Perfil</a>'
              : `<button type="button" class="btn btn-link link-success btn-sm editarAdmin" data-login="${cuenta.id_login}">Perfil Docente</button>`)
          : `<button type="button" class="btn btn-link link-success btn-sm editarAdmin" data-login="${cuenta.id_login}">Editar</button>`;
        const accionEliminar=cuenta.es_actor
          ? '<span class="text-muted">No disponible</span>'
          : `<button type="button" class="btn btn-link link-danger btn-sm eliminarAdmin" data-login="${cuenta.id_login}" data-accion="${conDocencia?'retiro':'eliminacion'}" data-rol="${textoSeguro(cuenta.rol)}">${conDocencia?'Retirar rol':'Eliminar'}</button>`;
        template += `
          <tr>
            <td>${textoSeguro(cadenaMay(cuenta.nombre))}</td>
            <td>${textoSeguro(cuenta.correo)}</td>
            <td>${textoSeguro(cuenta.rol)}</td>
            <td>${conDocencia?'Docente':'Administrativo'}</td>
            <td>${accionDocencia}</td>
            <td>${accionEditar}</td>
            <td>${accionEliminar}</td>
          </tr>`;
      });
      $('#datos_admin').html(template);
    },
    error:function(xhr){
      mostrarErrorLista(mensajeRespuesta(xhr, 'No fue posible cargar las cuentas administrativas.'));
    }
  });
}

function mensajeError(mensaje){
  $('#mnsj_row').show();
  $('#mnsj').removeClass('alert-success').addClass('alert-danger');
  $('#mnsj').text(mensaje);
}

function limpiar(){
  $('#nom_admin').val('');
  $('#correo').val('');
  $('#pass').val('');
  $('#pass2').val('');
  $('#permiso').val('0');
  $('#id_login').val('');
  $('#box-pass').attr('name',0);
  limpiarInput('#nom_admin','#col_nom_admin');
  limpiarInput('#correo','#col_correo');
  limpiarInput('#pass','#col_pass');
  limpiarInput('#pass2','#col_pass2');
  limpiarSelect('#permiso','#col_tipo');
}

function mostrarform(flag){
  if(flag){
    $('#list_admin').hide();
    $('#form_admin').show();
    $('.titulo_comite').hide();
  }else{
    $('#list_admin').show();
    $('#form_admin').hide();
    $('.titulo_comite').show();
  }
}

function mostrarExitoFormulario(mensaje){
  $('#mnsj_row').show();
  $('#mnsj').removeClass('alert-danger').addClass('alert-success');
  $('#mnsj').text(mensaje);
  setTimeout(function(){
    $('#mnsj_row').fadeOut(1500);
    mostrarform(false);
    mostrarAdmin();
    limpiar();
  },3000);
}

$('#btn-agr-admin').click(function(){
  limpiar();
  $('#col_tipo').show();
  $('#btn_cambiar_pass').hide();
  $('#box-pass').show().attr('name',1);
  mostrarform(true);
});

$('#btn-ver-comite').click(function(){
  limpiar();
  mostrarform(false);
});

$('body').click(function(){
  $('#mnsj_row').hide();
});

$('#form_admin').submit(function(e){
  e.preventDefault();
  const nombre=guardar($('#nom_admin').val());
  const correo=guardar($('#correo').val());
  const pass=$('#pass').val();
  const pass2=$('#pass2').val();
  const permiso=Number($('#permiso').val());
  const id_login=$('#id_login').val()===''?0:Number($('#id_login').val());
  const editado=id_login>0;

  if(nombre==='' || correo===''){
    mensajeError('Nombre y correo electrónico son obligatorios.');
    return;
  }
  if(!editado && ![1,2].includes(permiso)){
    mensajeError('Seleccione Admin o Comité.');
    return;
  }
  if(!editado && pass===''){
    mensajeError('La contraseña es obligatoria para crear la cuenta.');
    return;
  }
  if(pass!==pass2){
    $('#pass').addClass('is-invalid');
    $('#pass2').addClass('is-invalid');
    mensajeError('Las contraseñas no coinciden.');
    return;
  }

  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{nombre,correo,pass,permiso:editado?0:permiso,id_login,op:'insert-update'},
    success:function(respuesta){
      if(!respuesta.ok){
        mensajeError(respuesta.mensaje || 'No fue posible guardar la cuenta.');
        return;
      }
      mostrarExitoFormulario(respuesta.mensaje);
    },
    error:function(xhr){
      mensajeError(mensajeRespuesta(xhr, 'No fue posible guardar la cuenta.'));
    }
  });
});

$('body').on('click','.editarAdmin',function(){
  const id_login=$(this).data('login');
  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{id_login,op:'query_id'},
    success:function(cuenta){
      if(!cuenta || !cuenta.id_login){
        mostrarErrorLista('No fue posible cargar la cuenta seleccionada.');
        return;
      }
      if(cuenta.perfil==='docente'){
        window.location.href=`ver.docente.php?id_usuario=${encodeURIComponent(cuenta.id_usuario)}`;
        return;
      }
      limpiar();
      mostrarform(true);
      $('#col_tipo').hide();
      $('#box-pass').hide().attr('name',0);
      $('#btn_cambiar_pass').show();
      $('#id_login').val(cuenta.id_login);
      $('#nom_admin').val(letraMay(cuenta.nombre));
      $('#correo').val(cuenta.correo);
    },
    error:function(xhr){
      mostrarErrorLista(mensajeRespuesta(xhr, 'No fue posible cargar la cuenta seleccionada.'));
    }
  });
});

$('body').on('click','.agregarDocencia',function(){
  const id_login=$(this).data('login');
  window.location.href=`../form-doc/docente.php?agregar_docencia=${encodeURIComponent(id_login)}`;
});

$('body').on('click','.eliminarAdmin',function(){
  const boton=$(this);
  const id_login=boton.data('login');
  const accion=boton.data('accion');
  const rol=boton.data('rol');
  const pregunta=accion==='retiro'
    ? `Se retirará únicamente el rol ${rol}; el perfil Docente permanecerá. ¿Desea continuar?`
    : `Se eliminará completamente la cuenta ${rol} sin Docencia. ¿Desea continuar?`;
  if(!window.confirm(pregunta)){
    return;
  }
  $.ajax({
    url:'../ajax/admin.php',
    type:'POST',
    dataType:'json',
    data:{id_login,op:'delete'},
    success:function(respuesta){
      if(!respuesta.ok){
        mostrarErrorLista(respuesta.mensaje || 'No fue posible completar la operación.');
        return;
      }
      $('#mnsj_elim').show();
      $('#eliminado').removeClass('alert-danger').addClass('alert-success');
      $('#eliminado').text(respuesta.mensaje);
      mostrarAdmin();
      setTimeout(function(){
        $('#mnsj_elim').fadeOut(1500);
      },3000);
    },
    error:function(xhr){
      mostrarErrorLista(mensajeRespuesta(xhr, 'No fue posible completar la operación.'));
    }
  });
});

$('#btn_cambiar_pass').click(function(){
  $('#box-pass').show().attr('name',1);
  $('#btn_cambiar_pass').hide();
});

function init(){
  $('#mnsj_row').hide();
  $('#mnsj_elim').hide();
  mostrarform(false);
  mostrarAdmin();
  $('.loadPage').fadeOut();
}

init();
