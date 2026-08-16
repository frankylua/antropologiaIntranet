
function editarLista(id, nombre) {
  $('#agregar_lista').prop('value', 'Editar');
  $('#oculto').attr('value', id);
  $('#dato_lista').val(nombre);
}
function click() {
  $('#canc_edit').remove();
  $('#dato_lista').val('');
}
function cargarListaAdministrativa(tipo) {
  if (tipo == 'inst') {
    ajaxListas('#listas', '../ajax/institucion.php', 'read', undefined, 'id_inst', 'inst', true);
    return;
  }
  cargarListas(tipo);
}
function insertUpdate(url, dato_lista) {// funcion para llamar ajax;
  $.post(url, dato_lista, function (response) {
    console.log(response)
    cargarListaAdministrativa(dato_lista.tipo);
    if ($('#agregar_lista').val() == 'Editar') {
      $('#agregar_lista').val('Agregar');
    }
    click();
  }).fail(function (xhr) {
    const esTitulo = ['lic', 'un', 'mag', 'doc'].includes(dato_lista.tipo);
    const esInstitucion = dato_lista.tipo == 'inst';
    if ((dato_lista.tipo == 'pueb' || esTitulo || esInstitucion) && xhr.status == 403) {
      const mensaje = xhr.responseJSON && xhr.responseJSON.mensaje
        ? xhr.responseJSON.mensaje
        : esTitulo
          ? 'Usuario sin permisos para crear o editar titulos academicos'
          : esInstitucion
            ? 'Usuario sin permisos para crear o editar instituciones'
            : 'Usuario sin permisos para crear o editar';
      mostrarMensajeCRUD({
        contenedor: '#mnsj_row_listas',
        mensaje: mensaje,
        tipo: 'danger'
      });
    }
  })
}
function clickListas(nom) {
  $('#agregar_lista').prop('value', 'Agregar');
  $('#tabla-listas').show();
  $('#dato_lista').attr('name', nom);
  click();

  if (nom == 'pueb') {
    cargarListas('pueb');
  }
  if (nom == 'lic') {
    cargarListas('lic');
  }
  if (nom == 'un') {
    cargarListas('un');
  }
  if (nom == 'mag') {
    cargarListas('mag');
  }
  if (nom == 'doc') {
    cargarListas('doc');
  }
  if (nom == 'inst') {
    cargarListaAdministrativa('inst');
  }
  if (nom == 'bec_int') {
    cargarListas('bec_int');
  }
  if (nom == 'bec_ext') {
    cargarListas('bec_ext');
  }
  if (nom == 'financ') {
    cargarListas('financ');
  }
}

function eliminarInstitucion(id, descripcion) {
  confirmarEliminacion({
    tipo: 'Institución',
    nombre: typeof descripcion === 'string' ? descripcion.trim() : '',
    onConfirm: function () {
      $.ajax({
        url: '../ajax/institucion.php',
        type: 'POST',
        data: { id: id, op: 'delete' },
        dataType: 'json',
        success: function (response) {
          mostrarMensajeCRUD({
            contenedor: '#mnsj_row_listas',
            mensaje: response.mensaje,
            tipo: response.ok ? 'success' : 'danger'
          });
          if (response.ok) {
            cargarListaAdministrativa('inst');
          }
        },
        error: function (xhr) {
          const response = xhr.responseJSON;
          mostrarMensajeCRUD({
            contenedor: '#mnsj_row_listas',
            mensaje: response && response.mensaje ? response.mensaje : 'No fue posible eliminar la institución',
            tipo: 'danger'
          });
        }
      });
    }
  });
}

// function eliminarLista(id, n_input) {

//   op = 'delete';
//   const dato_lista = {
//     id: id,
//     op: op
//   }
//   if (n_input == 'pueb') {
//     $.post('../ajax/pueblo.php', dato_lista, function (response) {
//       cargarListas(n_input);
//       console.log(response)
//     })
//   }
//   if (n_input == 'lic' || n_input == 'un' || n_input == 'mag' || n_input == 'doc') {
//     $.post('../ajax/titulo.php', dato_lista, function (response) {
//       cargarListas(n_input);
//       console.log(response)
//     })
//   }
//   if (n_input == 'inst') {
//     console.log('entra a inst' + n_input)
//     $.post('../ajax/institucion.php', dato_lista, function (response) {
//       cargarListas(n_input);
//       console.log('respuesta....' + response)
//     })
//   }


//   // btn pueblos







// }

$(document).ready(function () {
  $('#tabla-listas').hide();
  $('#btn_pueblos').click(function () {
    // hacer scroll hasta la lista seleccionada (investigar mas)
    // $("html, body").animate({
    //   scrollTop: $('#btn_institucion').offset().top
    // });
    clickListas('pueb');
  });
  // btn licenciatura
  $('#btn_lic').click(function () {
    clickListas('lic');
  })
  //btn titulo universitario
  $('#btn_un').click(function () {
    clickListas('un');
  })
  //btn magister
  $('#btn_mag').click(function () {
    clickListas('mag');
  })
  //btn doctorado
  $('#btn_doc').click(function () {
    clickListas('doc');
  })
  // btn institucion
  $('#btn_institucion').click(function () {
    clickListas('inst');
  })
  //btn beca interna
  $('#btn_bec_int').click(function () {
    clickListas('bec_int');
  })
  //btn beca externa
  $('#btn_bec_ext').click(function () {
    clickListas('bec_ext');
  })
  //btn fuente de financiamiento
  $('#btn_financ').click(function () {
    clickListas('financ');
  })
  // link editar por id
  $('body').on('click', '.editar_lista', function () {
    id_edit = $(this).attr('id');
    nombre = $(this).attr('name');
    editarLista(id_edit, nombre);
    $('#canc_edit').remove();
    $('#grp-btn').append("<input type='button' class='btn btn-outline-dark input-group-text' id='canc_edit' value='Cancelar'></input>");      // '<input type="button" class='btn btn-outline-dark input-group-text' id="canc_edit" value="Cancelar"></input>'
    $('#canc_edit').click(function () {
      $('#agregar_lista').prop('value', 'Agregar');
      $('#canc_edit').remove();
      $('#dato_lista').val('');


    })

  })
  //link eliminar por id
  $('body').on('click', '.eliminar_lista', function () {
    id_elim = $(this).attr('id');
    n_lista = $('#dato_lista').attr('name');
    const descripcion_elim = $(this).closest('li').find('.col-auto').first().text().trim();
    console.log('id eliminar' + n_lista)
    if (n_lista == 'inst') {
      eliminarInstitucion(id_elim, descripcion_elim);
      return;
    }
    eliminarLista(id_elim, n_lista, descripcion_elim);
  })
  $('#form_lista').submit(function (e) {
    e.preventDefault();
    if($('#dato_lista').val()==''){

    }else{
      n_input = $('#dato_lista').attr('name');
      nombre = espacios($('#dato_lista').val());
      id = $('#oculto').val();
      op = 'insert-update';// op po metodo get, variable que define operacion crud
      const dato_lista = {
        nombre: nombre,
        id: id,
        op: op,
        tipo: n_input
      }
      // llamando a los datos a travez de ajax metodo .post
      if (n_input == 'pueb') {
        insertUpdate('../ajax/pueblo.php', dato_lista);
  
      }
      if (n_input == 'inst') {
        insertUpdate(ruta+'ajax/institucion.php', dato_lista);
      }
      if (n_input == 'lic' || n_input == 'un' || n_input == 'mag' || n_input == 'doc') {
        insertUpdate(ruta+'ajax/titulo.php', dato_lista);
      }
      if (n_input == 'financ' ) {
        insertUpdate(ruta+'ajax/financiamiento.php', dato_lista);
      }
      if (n_input == 'bec_ext' || n_input == 'bec_int' ) {
        insertUpdate(ruta+'ajax/beca.php', dato_lista);
      }

    }

  })
})







































