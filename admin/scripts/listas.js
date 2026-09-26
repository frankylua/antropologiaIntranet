
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
  if (tipo == 'financ') {
    ajaxListas('#listas', '../ajax/financiamiento.php', 'read-admin', undefined, 'id_financ', 'financiamiento', true);
    return;
  }
  cargarListas(tipo);
}

let tipoCatalogoBecaActivo = 1;
let filasCatalogoBeca = [];
let filaCatalogoBecaEnEdicion = null;

function mostrarMensajeCatalogoBeca(mensaje, tipo) {
  $('#beca_catalogo_mensaje')
    .removeClass('d-none alert-success alert-danger')
    .addClass(tipo == 'success' ? 'alert-success' : 'alert-danger')
    .text(mensaje);
}

function respuestaValidaCatalogoBeca(response) {
  return response
    && response.ok === true
    && typeof response.codigo == 'string'
    && typeof response.mensaje == 'string'
    && Object.prototype.hasOwnProperty.call(response, 'datos');
}

function solicitarCatalogoBeca(datos, escritura, onSuccess) {
  const opciones = {
    url: '../ajax/beca.php',
    type: 'POST',
    dataType: 'json',
    data: datos,
    success: function (response) {
      if (!respuestaValidaCatalogoBeca(response)) {
        const mensaje = response && typeof response.mensaje == 'string'
          ? response.mensaje
          : 'La respuesta del catálogo de Becas no es válida.';
        mostrarMensajeCatalogoBeca(mensaje, 'danger');
        return;
      }

      onSuccess(response);
    },
    error: function (xhr) {
      const response = xhr.responseJSON;

      mostrarMensajeCatalogoBeca(
        response && typeof response.mensaje == 'string'
          ? response.mensaje
          : 'No fue posible completar la operación del catálogo de Becas.',
        'danger'
      );
    }
  };

  if (escritura) {
    opciones.headers = {
      'X-CSRF-Token': $('#beca_catalogo').attr('data-csrf') || ''
    };
  }

  $.ajax(opciones);
}

function agregarOpcionCatalogoBeca($select, valor, etiqueta) {
  $select.append($('<option>').val(valor).text(etiqueta));
}

function etiquetaTipoCatalogoBeca(tipo) {
  return Number(tipo) === 1 ? 'Interna' : 'Externa';
}

function nombreVisibleCatalogoBeca(fila) {
  return typeof fila.beca == 'string' && fila.beca.trim() !== ''
    ? fila.beca
    : '(Sin nombre)';
}

function cancelarEdicionCatalogoBeca() {
  filaCatalogoBecaEnEdicion = null;
  $('#beca_catalogo_nombre').val('');
  $('#beca_catalogo_tipo').val(String(tipoCatalogoBecaActivo));
  $('#beca_catalogo_guardar').text('Agregar');
  $('#beca_catalogo_cancelar').addClass('d-none');
}

function cargarCatalogoBeca() {
  solicitarCatalogoBeca({
    op: 'catalog-list',
    tipo_beca: String(tipoCatalogoBecaActivo)
  }, false, function (response) {
    if (!Array.isArray(response.datos)) {
      mostrarMensajeCatalogoBeca(
        'El listado del catálogo de Becas no es válido.',
        'danger'
      );
      return;
    }

    filasCatalogoBeca = response.datos;
    renderizarCatalogoBeca();
  });
}

function operarCatalogoBeca(fila, operacion, confirmacion, datosAdicionales) {
  if (!window.confirm(confirmacion)) {
    return;
  }

  const datos = $.extend({
    op: operacion,
    id_nombre_beca: String(fila.id_nom_beca)
  }, datosAdicionales || {});

  solicitarCatalogoBeca(datos, true, function (response) {
    mostrarMensajeCatalogoBeca(response.mensaje, 'success');
    cancelarEdicionCatalogoBeca();
    cargarCatalogoBeca();
  });
}

function editarCatalogoBeca(fila) {
  filaCatalogoBecaEnEdicion = fila;
  $('#beca_catalogo_nombre').val(fila.beca);
  $('#beca_catalogo_tipo').val(String(fila.tipo_beca));
  $('#beca_catalogo_guardar').text('Guardar');
  $('#beca_catalogo_cancelar').removeClass('d-none');
}

function renderizarCatalogoBeca() {
  const $lista = $('#beca_catalogo_lista').empty();

  const destinosAprobados = filasCatalogoBeca.filter(function (fila) {
    return fila.estado_catalogo === 'APROBADA';
  });

  if (filasCatalogoBeca.length === 0) {
    $lista.append(
      $('<li>', { class: 'list-group-item text-muted' })
        .text('No hay entradas de Beca para este tipo.')
    );
    return;
  }

  filasCatalogoBeca.forEach(function (fila) {
    const id = Number(fila.id_nom_beca);
    const nombre = nombreVisibleCatalogoBeca(fila);
    const estado = String(fila.estado_catalogo || '');
    const referencias = Number(fila.referencias) || 0;

    const proponente = typeof fila.proponente == 'string'
      && fila.proponente.trim() !== ''
      ? fila.proponente.trim()
      : 'Sin proponente';

    const $item = $('<li>', { class: 'list-group-item' });

    const $cabecera = $('<div>', {
      class: 'd-flex justify-content-between gap-2 flex-wrap'
    });

    const $acciones = $('<div>', {
      class: 'd-flex gap-2 flex-wrap mt-2'
    });

    $cabecera
      .append($('<span>', { class: 'fw-bold' }).text(nombre))
      .append(
        $('<span>', { class: 'badge bg-secondary' })
          .text(etiquetaTipoCatalogoBeca(fila.tipo_beca) + ' · ' + estado)
      );

    $item
      .append($cabecera)
      .append(
        $('<div>', { class: 'small text-muted mt-1' }).text(
          'Proponente: ' + proponente
          + ' · Becas referenciadas: ' + String(referencias)
        )
      );

    $('<button>', {
      type: 'button',
      class: 'btn btn-sm btn-outline-dark'
    })
      .text('Editar')
      .on('click', function () {
        editarCatalogoBeca(fila);
      })
      .appendTo($acciones);

    if (estado === 'PENDIENTE') {
      $('<button>', {
        type: 'button',
        class: 'btn btn-sm btn-outline-success'
      })
        .text('Aprobar')
        .on('click', function () {
          operarCatalogoBeca(
            fila,
            'catalog-approve',
            '¿Aprobar la entrada "' + nombre + '"?'
          );
        })
        .appendTo($acciones);
    }

    if (estado !== 'INACTIVA') {
      $('<button>', {
        type: 'button',
        class: 'btn btn-sm btn-outline-danger'
      })
        .text('Inactivar')
        .on('click', function () {
          operarCatalogoBeca(
            fila,
            'catalog-inactivate',
            '¿Inactivar la entrada "' + nombre + '"? '
              + 'Se conservarán sus ' + String(referencias)
              + ' referencia(s) histórica(s).'
          );
        })
        .appendTo($acciones);
    }

    const destinos = destinosAprobados.filter(function (destino) {
      return Number(destino.id_nom_beca) !== id;
    });

    if (destinos.length > 0) {
      const $destino = $('<select>', {
        class: 'form-select form-select-sm',
        'aria-label': 'Destino aprobado para unificar'
      });

      agregarOpcionCatalogoBeca($destino, '', 'Destino aprobado…');

      destinos.forEach(function (destino) {
        agregarOpcionCatalogoBeca(
          $destino,
          String(destino.id_nom_beca),
          nombreVisibleCatalogoBeca(destino)
        );
      });

      $acciones.append($destino);

      $('<button>', {
        type: 'button',
        class: 'btn btn-sm btn-outline-primary'
      })
        .text('Unificar')
        .on('click', function () {
          const idDestino = $destino.val();

          if (idDestino === '' || Number(idDestino) === id) {
            mostrarMensajeCatalogoBeca(
              'Seleccione un destino aprobado distinto del origen.',
              'danger'
            );
            return;
          }

          const destino = destinos.find(function (filaDestino) {
            return Number(filaDestino.id_nom_beca) === Number(idDestino);
          });

          operarCatalogoBeca(
            fila,
            'catalog-unify',
            '¿Unificar "' + nombre + '" con "'
              + nombreVisibleCatalogoBeca(destino) + '"? '
              + 'Se reasignarán ' + String(referencias) + ' Beca(s).',
            { destino: String(idDestino) }
          );
        })
        .appendTo($acciones);
    }

    $item.append($acciones);
    $lista.append($item);
  });
}

function abrirCatalogoBeca(tipo) {
  tipoCatalogoBecaActivo = tipo;
  filasCatalogoBeca = [];
  filaCatalogoBecaEnEdicion = null;

  const $contenedor = $('#beca_catalogo')
    .empty()
    .removeClass('d-none')
    .show();

  const $formulario = $('<form>', {
    id: 'beca_catalogo_form',
    class: 'mb-3'
  });

  const $grupo = $('<div>', {
    class: 'input-group'
  });

  const $tipo = $('<select>', {
    id: 'beca_catalogo_tipo',
    class: 'form-select',
    'aria-label': 'Tipo de Beca'
  });

  agregarOpcionCatalogoBeca($tipo, '1', 'Interna');
  agregarOpcionCatalogoBeca($tipo, '2', 'Externa');

  $tipo.val(String(tipo));

  $grupo
    .append(
      $('<input>', {
        type: 'text',
        id: 'beca_catalogo_nombre',
        class: 'form-control',
        maxlength: 80,
        placeholder: 'Nombre de Beca'
      })
    )
    .append($tipo)
    .append(
      $('<button>', {
        type: 'submit',
        id: 'beca_catalogo_guardar',
        class: 'btn btn-outline-dark'
      }).text('Agregar')
    )
    .append(
      $('<button>', {
        type: 'button',
        id: 'beca_catalogo_cancelar',
        class: 'btn btn-outline-secondary d-none'
      })
        .text('Cancelar')
        .on('click', cancelarEdicionCatalogoBeca)
    );

  $formulario
    .append($grupo)
    .on('submit', function (event) {
      event.preventDefault();

      const nombre = $('#beca_catalogo_nombre').val();
      const tipoSeleccionado = $('#beca_catalogo_tipo').val();
      const editando = filaCatalogoBecaEnEdicion !== null;

      const datos = {
        op: editando ? 'catalog-update' : 'catalog-create',
        nombre: nombre,
        tipo_beca: tipoSeleccionado
      };

      let confirmacion = '¿Crear esta entrada oficial de Beca?';

      if (editando) {
        datos.id_nombre_beca = String(
          filaCatalogoBecaEnEdicion.id_nom_beca
        );

        confirmacion =
          '¿Actualizar esta entrada? La normalización afecta a '
          + String(Number(filaCatalogoBecaEnEdicion.referencias) || 0)
          + ' Beca(s) referenciada(s).';
      }

      if (!window.confirm(confirmacion)) {
        return;
      }

      solicitarCatalogoBeca(datos, true, function (response) {
        mostrarMensajeCatalogoBeca(response.mensaje, 'success');
        cancelarEdicionCatalogoBeca();
        cargarCatalogoBeca();
      });
    });

  $contenedor
    .append(
      $('<h4>', {
        class: 'mb-3'
      }).text(
        tipo === 1 ? 'Becas Internas' : 'Becas Externas'
      )
    )
    .append(
      $('<div>', {
        id: 'beca_catalogo_mensaje',
        class: 'alert d-none',
        role: 'alert'
      })
    )
    .append($formulario)
    .append(
      $('<ul>', {
        id: 'beca_catalogo_lista',
        class: 'list-group'
      })
    );

  cargarCatalogoBeca();
}

function insertUpdate(url, dato_lista) {// funcion para llamar ajax;
  const esFinanciamiento = dato_lista.tipo == 'financ';
  $.ajax({
    url: url,
    type: 'POST',
    data: dato_lista,
    dataType: esFinanciamiento ? 'json' : undefined,
    headers: esFinanciamiento ? {
      'X-CSRF-Token': $('#financiamiento_catalogo').attr('data-csrf') || ''
    } : {},
    success: function (response) {
      if (esFinanciamiento && (!response || response.ok !== true)) {
        mostrarMensajeCRUD({
          contenedor: '#mnsj_row_listas',
          mensaje: response && response.mensaje ? response.mensaje : 'No fue posible guardar el Financiamiento.',
          tipo: 'danger'
        });
        return;
      }
      console.log(response)
      cargarListaAdministrativa(dato_lista.tipo);
      if (esFinanciamiento) {
        mostrarMensajeCRUD({
          contenedor: '#mnsj_row_listas',
          mensaje: response.mensaje,
          tipo: 'success'
        });
      }
      if ($('#agregar_lista').val() == 'Editar') {
        $('#agregar_lista').val('Agregar');
      }
      click();
    },
    error: function (xhr) {
      const esTitulo = ['lic', 'un', 'mag', 'doc'].includes(dato_lista.tipo);
      const esInstitucion = dato_lista.tipo == 'inst';
      if (esFinanciamiento) {
        const response = xhr.responseJSON;
        mostrarMensajeCRUD({
          contenedor: '#mnsj_row_listas',
          mensaje: response && response.mensaje ? response.mensaje : 'No fue posible guardar el Financiamiento.',
          tipo: 'danger'
        });
      } else if ((dato_lista.tipo == 'pueb' || esTitulo || esInstitucion) && xhr.status == 403) {
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
    }
  });
}

function eliminarFinanciamiento(id, descripcion) {
  confirmarEliminacion({
    tipo: 'Financiamiento',
    nombre: typeof descripcion == 'string' ? descripcion.trim() : '',
    onConfirm: function () {
      $.ajax({
        url: '../ajax/financiamiento.php',
        type: 'POST',
        dataType: 'json',
        headers: {
          'X-CSRF-Token': $('#financiamiento_catalogo').attr('data-csrf') || ''
        },
        data: { op: 'delete', id: id },
        success: function (response) {
          mostrarMensajeCRUD({
            contenedor: '#mnsj_row_listas',
            mensaje: response.mensaje,
            tipo: response.ok ? 'success' : 'danger'
          });
          if (response.ok) cargarListaAdministrativa('financ');
        },
        error: function (xhr) {
          const response = xhr.responseJSON;
          mostrarMensajeCRUD({
            contenedor: '#mnsj_row_listas',
            mensaje: response && response.mensaje
              ? response.mensaje
              : 'No fue posible eliminar el Financiamiento.',
            tipo: 'danger'
          });
        }
      });
    }
  });
}
function clickListas(nom) {
  $('#agregar_lista').prop('value', 'Agregar');
  $('#dato_lista').attr('name', nom);
  click();

  if (nom == 'bec_int' || nom == 'bec_ext') {
    $('#tabla-listas').hide();
    abrirCatalogoBeca(nom == 'bec_int' ? 1 : 2);
    return;
  }

  $('#beca_catalogo').addClass('d-none').hide().empty();
  $('#tabla-listas').show();

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
  if (nom == 'financ') {
    cargarListaAdministrativa('financ');
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
    if (n_lista == 'financ') {
      eliminarFinanciamiento(id_elim, descripcion_elim);
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

    }

  })
})







































