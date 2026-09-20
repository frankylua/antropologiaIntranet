(function ($) {
    'use strict';

    const $app = $('#beca_app');
    if ($app.length === 0) {
        return;
    }

    const endpoint = '../ajax/beca.php';
    const csrf = String($app.attr('data-csrf') || '');
    const esGlobal = String($app.attr('data-global') || '') === '1';
    let destinoActual = '#beca_card';
    let usuarioActual = null;
    let temporizadorMensaje = null;

    function mensaje(texto, tipo, temporal = false) {
        clearTimeout(temporizadorMensaje);
        temporizadorMensaje = null;
        $('#mnsj_row_beca').stop(true, true);

        $('#mnsj_beca')
            .removeClass('alert-success alert-danger alert-warning')
            .addClass(`alert-${tipo || 'danger'}`)
            .text(texto || 'No fue posible completar la operación.');

        $('#mnsj_row_beca').removeClass('d-none').show();

        if (temporal && tipo === 'success') {
            temporizadorMensaje = setTimeout(function () {
                temporizadorMensaje = null;
                $('#mnsj_row_beca').fadeOut(1500);
            }, 3000);
        }
    }

    function ocultarMensaje() {
        clearTimeout(temporizadorMensaje);
        temporizadorMensaje = null;
        $('#mnsj_row_beca').stop(true, true).addClass('d-none').hide();
    }

    function mensajeError(xhr) {
        if (xhr && xhr.responseJSON && xhr.responseJSON.mensaje) {
            return xhr.responseJSON.mensaje;
        }

        return 'No fue posible comunicarse con el servicio de Becas.';
    }

    function usuarioObjetivo() {
        if (!esGlobal) {
            return null;
        }

        const usuario = String(usuarioActual || $('#id_usuario').attr('name') || '');
        return /^[1-9]\d*$/.test(usuario) ? usuario : null;
    }

    function solicitud(datos, escritura) {
        return $.ajax({
            url: endpoint,
            type: 'POST',
            dataType: 'json',
            data: datos,
            headers: escritura ? { 'X-CSRF-Token': csrf } : {}
        });
    }

    function textoFecha(fecha) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(String(fecha || ''))) {
            return 'Sin fecha';
        }

        return String(fecha).split('-').reverse().join('-');
    }

    function agregarDato($tabla, etiqueta, valor) {
        const $fila = $('<tr>').appendTo($tabla);
        $('<td>').text(etiqueta).appendTo($fila);
        $('<td>')
            .text(valor == null ? '' : String(valor))
            .appendTo($fila);
    }

    function cargarInstituciones(seleccionada) {
        const $selector = $('#beca_institucion')
            .empty()
            .append($('<option>', { value: '', text: 'Seleccione' }));

        $.ajax({
            url: '../ajax/institucion.php',
            type: 'POST',
            dataType: 'json',
            data: { op: 'read' }
        }).done(function (respuesta) {
            const instituciones = Array.isArray(respuesta)
                ? respuesta
                : (respuesta && Array.isArray(respuesta.datos) ? respuesta.datos : []);

            instituciones.forEach(function (institucion) {
                if (institucion.id_inst === undefined || institucion.inst === undefined) {
                    return;
                }

                $('<option>', {
                    value: String(institucion.id_inst),
                    text: String(institucion.inst)
                }).appendTo($selector);
            });

            if (seleccionada) {
                $selector.val(String(seleccionada));
            }
        }).fail(function () {
            mensaje('No fue posible cargar las Instituciones disponibles.', 'danger');
        });
    }

    function cargarOpciones(tipo, seleccionada) {
        const $selector = $('#beca_nombre')
            .empty()
            .append($('<option>', { value: '', text: 'Seleccione' }));

        if (!['1', '2'].includes(String(tipo))) {
            return $.Deferred().resolve().promise();
        }

        return solicitud({
            op: 'options',
            tipo_beca: String(tipo)
        }, false).done(function (respuesta) {
            if (!respuesta.ok) {
                mensaje(respuesta.mensaje, 'danger');
                return;
            }

            (respuesta.datos || []).forEach(function (fila) {
                const sufijo = fila.estado_catalogo === 'PENDIENTE'
                    ? ' (pendiente)'
                    : '';

                $('<option>', {
                    value: String(fila.id_nom_beca),
                    text: `${String(fila.beca)}${sufijo}`
                }).appendTo($selector);
            });

            if (seleccionada && seleccionada.id) {
                const id = String(seleccionada.id);

                if ($selector.find(`option[value="${id}"]`).length === 0) {
                    $('<option>', {
                        value: id,
                        text: `${String(seleccionada.nombre || 'Beca histórica')} (histórica)`
                    }).appendTo($selector);
                }

                $selector.val(id);
            }
        }).fail(function (xhr) {
            mensaje(mensajeError(xhr), 'danger');
        });
    }

    function cerrarEditor(ocultarAlCerrar) {
        const $zona = $('#beca_editor').closest('.oa-zone');
        $('#beca_editor').empty().hide();
        if ($zona.length) $zona.find('.oa-list,.oa-add').show(); else $('#boton_beca').show();

        if (ocultarAlCerrar !== false) {
            ocultarMensaje();
        }
    }

    function crearEditor(beca) {
        const edicion = Boolean(beca);
        const $editor = $('#beca_editor').empty();

        const $tarjeta = $('<div>', { class: 'card mb-3' });
        const $cuerpo = $('<div>', { class: 'card-body' }).appendTo($tarjeta);

        const $encabezado = $('<div>', { class: 'd-flex justify-content-between' }).appendTo($cuerpo);
        $('<h4>', {
            class: 'card-title',
            text: edicion ? 'Editar Beca' : 'Agregar Beca'
        }).appendTo($encabezado);
        $('<button>', {
            type: 'button',
            class: 'btn-close',
            id: 'beca_cerrar',
            'aria-label': 'Cerrar'
        }).appendTo($encabezado);

        const $formulario = $('<form>', {
            id: 'beca_formulario_editor',
            novalidate: true
        }).appendTo($cuerpo);

        $('<input>', {
            type: 'hidden',
            id: 'beca_id',
            value: edicion ? String(beca.id_beca) : ''
        }).appendTo($formulario);

        const $filaCatalogo = $('<div>', { class: 'row' }).appendTo($formulario);
        const $colTipo = $('<div>', { class: 'col-md-6 mb-3' }).appendTo($filaCatalogo);

        $('<label>', {
            class: 'form-label',
            for: 'beca_tipo',
            text: 'Tipo de Beca'
        }).appendTo($colTipo);

        const $tipo = $('<select>', {
            class: 'form-select',
            id: 'beca_tipo',
            required: true
        }).append(
            $('<option>', { value: '', text: 'Seleccione' }),
            $('<option>', { value: '1', text: 'Interna' }),
            $('<option>', { value: '2', text: 'Externa' })
        ).appendTo($colTipo);

        const $colNombre = $('<div>', { class: 'col-md-6 mb-3' }).appendTo($filaCatalogo);

        $('<label>', {
            class: 'form-label',
            for: 'beca_nombre',
            text: 'Nombre de Beca'
        }).appendTo($colNombre);

        $('<select>', {
            class: 'form-select',
            id: 'beca_nombre',
            required: true
        }).append(
            $('<option>', { value: '', text: 'Seleccione un tipo primero' })
        ).appendTo($colNombre);

        if (!edicion && !esGlobal) {
            const $check = $('<div>', { class: 'form-check mt-2' }).appendTo($colNombre);

            $('<input>', {
                class: 'form-check-input',
                type: 'checkbox',
                id: 'beca_propuesta_activa'
            }).appendTo($check);

            $('<label>', {
                class: 'form-check-label',
                for: 'beca_propuesta_activa',
                text: 'Agregar otra Beca'
            }).appendTo($check);

            const $filaPropuesta = $('<div>', {
                class: 'row d-none',
                id: 'beca_propuesta_fila'
            }).appendTo($formulario);

            const $colPropuesta = $('<div>', {
                class: 'col-md-6 mb-3'
            }).appendTo($filaPropuesta);

            $('<label>', {
                class: 'form-label',
                for: 'beca_propuesta',
                text: 'Nombre propuesto'
            }).appendTo($colPropuesta);

            $('<input>', {
                class: 'form-control',
                type: 'text',
                id: 'beca_propuesta',
                maxlength: 80
            }).appendTo($colPropuesta);
        }

        const $filaDatos = $('<div>', { class: 'row' }).appendTo($formulario);
        const $colInstitucion = $('<div>', {
            class: 'col-md-6 mb-3'
        }).appendTo($filaDatos);

        $('<label>', {
            class: 'form-label',
            for: 'beca_institucion',
            text: 'Institución'
        }).appendTo($colInstitucion);

        $('<select>', {
            class: 'form-select',
            id: 'beca_institucion',
            required: true
        }).append(
            $('<option>', { value: '', text: 'Seleccione' })
        ).appendTo($colInstitucion);

        const $colInicio = $('<div>', {
            class: 'col-md-3 mb-3'
        }).appendTo($filaDatos);

        $('<label>', {
            class: 'form-label',
            for: 'beca_fecha_inicio',
            text: 'Fecha de inicio'
        }).appendTo($colInicio);

        $('<input>', {
            class: 'form-control',
            type: 'date',
            id: 'beca_fecha_inicio',
            required: true
        }).appendTo($colInicio);

        const $colTermino = $('<div>', {
            class: 'col-md-3 mb-3'
        }).appendTo($filaDatos);

        $('<label>', {
            class: 'form-label',
            for: 'beca_fecha_termino',
            text: 'Fecha de término'
        }).appendTo($colTermino);

        $('<input>', {
            class: 'form-control',
            type: 'date',
            id: 'beca_fecha_termino',
            required: true
        }).appendTo($colTermino);

        const $acciones = $('<div>', {
            class: 'd-flex justify-content-end gap-2'
        }).appendTo($formulario);

        $('<button>', {
            type: 'button',
            class: 'btn btn-outline-secondary',
            id: 'beca_cancelar',
            text: 'Cancelar'
        }).appendTo($acciones);

        $('<button>', {
            type: 'submit',
            class: 'btn btn-dark',
            id: 'beca_guardar',
            text: edicion ? 'Guardar cambios' : 'Guardar Beca'
        }).appendTo($acciones);

        $editor.append($tarjeta).show();
        const $zona = $editor.closest('.oa-zone');
        if ($zona.length) $zona.find('.oa-list,.oa-add').hide(); else $('#boton_beca').hide();
        const editorBeca = document.getElementById('beca_editor'); if (editorBeca) editorBeca.scrollIntoView({ behavior: 'smooth', block: 'start' });

        cargarInstituciones(edicion ? beca.inst_beca : null);

        if (edicion) {
            $tipo.val(String(beca.tipo_beca));
            $('#beca_fecha_inicio').val(String(beca.fech_in || ''));
            $('#beca_fecha_termino').val(String(beca.fech_ter || ''));

            cargarOpciones(String(beca.tipo_beca), {
                id: beca.nom_beca,
                nombre: beca.beca
            });
        }
    }

    function renderizarBecas(becas, destino) {
        const $destino = $(destino).empty();

        if (!Array.isArray(becas) || becas.length === 0) {
            $('<p>', {
                class: 'text-muted mb-3',
                text: 'No hay Becas registradas.'
            }).appendTo($destino);
            return;
        }

        becas.forEach(function (beca) {
            const $contenedor = $('<div>', { class: 'col-12' });
            const $tarjeta = $('<div>', { class: 'card mb-3 beca-registro' })
                .appendTo($contenedor);
            const $cuerpo = $('<div>', { class: 'card-body' }).appendTo($tarjeta);
            const $tabla = $('<table>', {
                class: 'table table-striped'
            }).appendTo($cuerpo);
            const $encabezado = $('<tr>').appendTo($('<thead>').appendTo($tabla));
            const $celdaTitulo = $('<th>', {
                class: 'col-md-4 titulo_acad'
            }).appendTo($encabezado);

            const $titulo = $('<h5>', {
                text: 'BECA'
            }).appendTo($celdaTitulo);

            if (beca.estado_catalogo && beca.estado_catalogo !== 'APROBADA') {
                $('<span>', {
                    class: 'badge bg-secondary ms-2',
                    text: String(beca.estado_catalogo)
                }).appendTo($titulo);
            }

            const $botones = $('<th>', {
                class: 'row justify-content-end ps-0 botones'
            }).appendTo($encabezado);

            $('<button>', {
                type: 'button',
                class: 'col-auto btn btn-link link-success ps-1 beca-editar',
                text: 'Editar'
            }).data('idBeca', String(beca.id_beca)).appendTo($botones);

            $('<button>', {
                type: 'button',
                class: 'col-auto btn btn-link link-danger ps-1 beca-eliminar',
                text: 'Eliminar'
            }).data('idBeca', String(beca.id_beca)).appendTo($botones);

            const $datos = $('<tbody>').appendTo($tabla);

            agregarDato($datos, 'Tipo', Number(beca.tipo_beca) === 1 ? 'Interna' : 'Externa');
            agregarDato($datos, 'Nombre', beca.beca);
            agregarDato($datos, 'Institución', beca.inst);
            agregarDato($datos, 'Inicio', textoFecha(beca.fech_in));
            agregarDato($datos, 'Término', textoFecha(beca.fech_ter));

            $contenedor.appendTo($destino);
        });
    }

    window.cargarBeca = function (usuario, destino) {
        const destinoSeguro = destino || '#beca_card';
        const datos = { op: 'list' };

        if (esGlobal) {
            const objetivo = String(usuario || $('#id_usuario').attr('name') || '');

            if (!/^[1-9]\d*$/.test(objetivo)) {
                $(destinoSeguro).empty();
                return $.Deferred().resolve().promise();
            }

            datos.usuario = objetivo;
            usuarioActual = objetivo;
        } else {
            usuarioActual = null;
        }

        destinoActual = destinoSeguro;

        return solicitud(datos, false).done(function (respuesta) {
            if (!respuesta.ok) {
                mensaje(respuesta.mensaje, 'danger');
                return;
            }

            renderizarBecas(respuesta.datos, destinoSeguro);
        }).fail(function (xhr) {
            mensaje(mensajeError(xhr), 'danger');
            $(destinoSeguro).empty();
        });
    };

    function cargarDetalle(idBeca) {
        const datos = {
            op: 'detail',
            id_beca: idBeca
        };

        if (esGlobal && usuarioObjetivo()) {
            datos.usuario = usuarioObjetivo();
        }

        solicitud(datos, false).done(function (respuesta) {
            if (!respuesta.ok) {
                mensaje(respuesta.mensaje, 'danger');
                return;
            }

            crearEditor(respuesta.datos);
        }).fail(function (xhr) {
            mensaje(mensajeError(xhr), 'danger');
        });
    }

    function datosFormulario() {
        const tipo = String($('#beca_tipo').val() || '');
        const propuestaActiva = $('#beca_propuesta_activa').is(':checked');

        const datos = {
            tipo_beca: tipo,
            institucion: String($('#beca_institucion').val() || ''),
            fecha_inicio: String($('#beca_fecha_inicio').val() || ''),
            fecha_termino: String($('#beca_fecha_termino').val() || '')
        };

        if (!['1', '2'].includes(tipo)) {
            mensaje('Seleccione Beca Interna o Externa.', 'danger');
            return null;
        }

        if (!datos.institucion || !datos.fecha_inicio || !datos.fecha_termino) {
            mensaje('Complete Institución y ambas fechas.', 'danger');
            return null;
        }

        if (datos.fecha_inicio > datos.fecha_termino) {
            mensaje('La fecha de inicio no puede ser posterior a la fecha de término.', 'danger');
            return null;
        }

        if (propuestaActiva) {
            datos.nuevo_nombre = String($('#beca_propuesta').val() || '').trim();

            if (!datos.nuevo_nombre || Array.from(datos.nuevo_nombre).length > 80) {
                mensaje('Ingrese un nombre propuesto de hasta 80 caracteres.', 'danger');
                return null;
            }
        } else {
            datos.id_nombre_beca = String($('#beca_nombre').val() || '');

            if (!/^[1-9]\d*$/.test(datos.id_nombre_beca)) {
                mensaje('Seleccione un nombre de Beca válido.', 'danger');
                return null;
            }
        }

        if (esGlobal) {
            const objetivo = usuarioObjetivo();

            if (!objetivo) {
                mensaje('Seleccione primero un Estudiante válido.', 'danger');
                return null;
            }

            datos.usuario = objetivo;
        }

        return datos;
    }

    $('#btn_beca').on('click', function () {
        crearEditor(null);
    });

    $(document).on('change', '#beca_tipo', function () {
        $('#beca_propuesta_activa').prop('checked', false).trigger('change');
        cargarOpciones(String($(this).val() || ''));
    });

    $(document).on('change', '#beca_propuesta_activa', function () {
        const activa = $(this).is(':checked');

        $('#beca_propuesta_fila').toggleClass('d-none', !activa);
        $('#beca_nombre').prop('disabled', activa);

        if (activa) {
            $('#beca_nombre').val('');
            $('#beca_propuesta').trigger('focus');
        } else {
            $('#beca_propuesta').val('');
        }
    });

    $(document).on('click', '#beca_cancelar', function () {
        cerrarEditor(true);
    });
    $(document).on('click', '#beca_cerrar', function () {
        cerrarEditor(true);
    });

    $(document).on('submit', '#beca_formulario_editor', function (evento) {
        evento.preventDefault();

        const datos = datosFormulario();
        if (!datos) {
            return;
        }

        const idBeca = String($('#beca_id').val() || '');

        if (idBeca) {
            datos.op = 'update';
            datos.id_beca = idBeca;
        } else {
            datos.op = 'create';
        }

        $('#beca_guardar').prop('disabled', true);

        solicitud(datos, true).done(function (respuesta) {
            if (!respuesta.ok) {
                mensaje(respuesta.mensaje, 'danger');
                return;
            }

            cerrarEditor(false);
            mensaje(respuesta.mensaje, 'success', true);
            window.cargarBeca(usuarioActual, destinoActual);
        }).fail(function (xhr) {
            mensaje(mensajeError(xhr), 'danger');
        }).always(function () {
            $('#beca_guardar').prop('disabled', false);
        });
    });

    $(document).on('click', '.beca-editar', function () {
        cargarDetalle(String($(this).data('idBeca')));
    });

    $(document).on('click', '.beca-eliminar', function () {
        const idBeca = String($(this).data('idBeca'));

        if (!window.confirm('¿Confirma la eliminación permanente de esta Beca?')) {
            return;
        }

        const datos = {
            op: 'delete',
            id_beca: idBeca
        };

        if (esGlobal && usuarioObjetivo()) {
            datos.usuario = usuarioObjetivo();
        }

        solicitud(datos, true).done(function (respuesta) {
            if (!respuesta.ok) {
                mensaje(respuesta.mensaje, 'danger');
                return;
            }

            mensaje(respuesta.mensaje, 'success');
            window.cargarBeca(usuarioActual, destinoActual);
        }).fail(function (xhr) {
            mensaje(mensajeError(xhr), 'danger');
        });
    });

    ocultarMensaje();
    $('#beca_editor').hide();
}(jQuery));
