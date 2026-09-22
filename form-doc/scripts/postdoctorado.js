// CRUD integral de Postdoctorado
function configuracionPostdoctorado() {
    const app = $('#postdoctorado-app');
    return {
        contexto: app.attr('data-contexto') || 'sin-acceso',
        csrf: app.attr('data-csrf') || ''
    };
}

function usuarioContextoPostdoctorado(valor) {
    const usuario = Number(valor || $('#id_usuario').attr('name'));
    return Number.isSafeInteger(usuario) && usuario > 0 ? usuario : null;
}

function agregarUsuarioObjetivoPostdoctorado(datos, usuario) {
    if (configuracionPostdoctorado().contexto !== 'global') {
        return datos;
    }
    const objetivo = usuarioContextoPostdoctorado(usuario);
    if (objetivo !== null) {
        datos.usuario = objetivo;
    }
    return datos;
}

function headersCsrfPostdoctorado() {
    return { 'X-CSRF-Token': configuracionPostdoctorado().csrf };
}

function resolverContenedorPostdoctorado(contenedor) {
    if (
        contenedor === '#ficha_postest'
        && !$(contenedor).length
        && $('#ficha_postdoc').length
    ) {
        return '#ficha_postdoc';
    }
    return contenedor;
}

function mensajeErrorPostdoctorado(xhr) {
    return xhr.responseJSON && xhr.responseJSON.mensaje
        ? xhr.responseJSON.mensaje
        : 'No fue posible completar la operación de Postdoctorado.';
}

function mostrarMensajePostdoctorado(mensaje, esError) {
    const texto = mensaje || (esError
        ? 'No fue posible completar la operación de Postdoctorado.'
        : 'Operación de Postdoctorado completada correctamente.');

    if ($('#mnsj_postdoc').length) {
        $('#mnsj_row_postdoc').show();
        $('#mnsj_postdoc')
            .removeClass(esError ? 'alert-success' : 'alert-danger')
            .addClass(esError ? 'alert-danger' : 'alert-success')
            .text(texto);
        return;
    }

    const esDocente = $('#ficha_acad').attr('name') === 'doc';
    const fila = esDocente ? $('#mnsj_row_acad_doc') : $('#mnsj_row_acad_est');
    const destino = esDocente ? $('#mnsj_acad_doc') : $('#mnsj_acad_est');
    if (fila.length && destino.length) {
        fila.show();
        destino
            .removeClass(esError ? 'alert-success' : 'alert-danger')
            .addClass(esError ? 'alert-danger' : 'alert-success')
            .text(texto);
        return;
    }

    window.alert(texto);
}

function escaparHtmlPostdoctorado(valor) {
    return $('<div>').text(String(valor ?? '')).html();
}

function textoPostdoctorado(valor) {
    const decodificador = document.createElement('textarea');
    decodificador.innerHTML = String(valor ?? '');
    const texto = decodificador.value.replace(/\s+/g, ' ').trim();
    return texto === '' ? '' : cadenaMay(texto);
}

function formPostdoc(contenedor, valorPorDefecto = null) {
    $(contenedor).append('<div class="row " id="postdoc_row"><div class="col-md-6 mb-3"><label class="form-label" for="prof_postdoc">Profesor/a Patrocinante</label><input class="form-control" type="text" id="prof_postdoc" maxlength="60"></div><div class="col-md-6 mb-3" id="id_instpostdoc" name="0"><label class="form-label" for="inst_postdoc">Institución</label><select id="inst_postdoc" class="form-select"></select></div></div>');
    $(contenedor).append('<div class="row "> <div class="col-md-6 mb-3"><label for="fech_in_postdoc" class="form-label">Fecha Inicio</label><input type="date" class="form-control" id="fech_in_postdoc" ></div><div class="col-md-6 mb-3"><label for="fech_ter_postdoc" class="form-label">Fecha Término</label><input type="date" class="form-control" id="fech_ter_postdoc" name="fecha_ter"> </div></div>');
    $(contenedor).append('<div class="row justify-content-center " id="mnsj_row_postdoc"><div class="col-lg-8 alert text-center alert-danger" role="alert" id="mnsj_postdoc"></div></div>');
    $('#mnsj_row_postdoc').hide();
    ajaxSelect(
        '#inst_postdoc',
        ruta + 'ajax/institucion.php',
        'Seleccione',
        'read',
        undefined,
        'id_inst',
        'inst'
    );
    if (valorPorDefecto !== null) {
        $('#inst_postdoc').val(String(valorPorDefecto));
    }
}

function zonaPostdoctorado() { return $('#form_postdoc').closest('.oa-zone'); }
function listadoPostdoctorado() { const id = zonaPostdoctorado().find('.oa-list').first().attr('id'); return id ? '#' + id : '#postdoc_card'; }
function abrirEditorPostdoctorado($editor) {
    const $zona = zonaPostdoctorado();
    $zona.find('.oa-list,.oa-add,.oa-editor').hide();
    $editor.show();
}
function cerrarEditorPostdoctorado() {
    const $zona = zonaPostdoctorado();
    $('#ingresar_postdoc,#campos_postdoc').empty();
    if ($zona.length) { $zona.find('.oa-editor').hide(); $zona.find('.oa-list,.oa-add').show(); }
    else { $('#boton_postdoc').show(); }
}

$('#btn_postdoc').click(function () {
    abrirEditorPostdoctorado($('#form_postdoc'));
    $('#ingresar_postdoc').remove();
    $('#boton_postdoc').hide();
    $('#postdoctorado').append('<div class="card mb-3" id="ingresar_postdoc"><div class="card-body" id="card_postdoc"> </div></div>');
    $('#card_postdoc').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Postdoctorado</h4></div><div class="col-auto"><button type="button" name="add" id="close_postdoc" class="btn btn-close btn-sm"></button></div></div>');
    formPostdoc('#card_postdoc');
    $('#card_postdoc').append('<div class="row justify-content-center"><div class="col-md-6 d-grid gap-2"><button type="submit" class="btn btn-dark mt-3">Guardar Postdoctorado</button></div></div>');
    const editorPostdoc = document.getElementById('ingresar_postdoc'); if (editorPostdoc) editorPostdoc.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

$(document).on('change', '#inst_postdoc', function () {
    $('#input_instpostdoc').remove();
    $('#id_instpostdoc').attr('name', 0);
    if ($('#inst_postdoc').val() === 'otro') {
        $('#postdoc_row').append('<div class="col-md-6 mb-3" id="input_instpostdoc"><input type="text" class="form-control" id="nuevo_inst" placeholder="Institución" maxlength="80"></div>');
    }
});

$(document).on('click', '#close_postdoc', function () {
    $('#ingresar_postdoc').remove();
    cerrarEditorPostdoctorado();
});

function cargarPostdoc(usuario, contenedor) {
    const destino = resolverContenedorPostdoctorado(contenedor);
    const datos = agregarUsuarioObjetivoPostdoctorado({ op: 'read' }, usuario);
    $.ajax({
        type: 'POST',
        url: '../ajax/postdoctorado.php',
        dataType: 'json',
        data: datos,
        success: function (response) {
            if (!response || response.ok !== true || !Array.isArray(response.datos)) {
                $(destino).empty();
                mostrarMensajePostdoctorado(response && response.mensaje, true);
                return;
            }

            let template = '';
            response.datos.forEach(function (postdoctorado) {
                const idPostdoctorado = Number(postdoctorado.id_postdoc);
                if (!Number.isSafeInteger(idPostdoctorado) || idPostdoctorado <= 0) {
                    return;
                }

                const acciones = configuracionPostdoctorado().contexto === 'sin-acceso'
                    ? ''
                    : '<button type="button" class="col-auto btn btn-link link-success ps-1 editarPostdoc" '
                        + 'data-id-postdoc="' + idPostdoctorado + '" data-usuario="' + escaparHtmlPostdoctorado(usuario) + '" '
                        + 'data-contenedor="' + escaparHtmlPostdoctorado(destino) + '">Editar</button>'
                        + '<button type="button" class="col-auto btn btn-link link-danger ps-1 eliminarPostdoc" '
                        + 'data-id-postdoc="' + idPostdoctorado + '" data-usuario="' + escaparHtmlPostdoctorado(usuario) + '" '
                        + 'data-contenedor="' + escaparHtmlPostdoctorado(destino) + '">Eliminar</button>';

                template += `
                    <div class="col-12" id="postdoctorado-${idPostdoctorado}">
                        <div class="card mb-3 postdoctorado_card">
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="col-md-4 titulo_acad"><h5>POSTDOCTORADO</h5></th>
                                            <th class="row justify-content-end ps-0 botones">${acciones}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Profesor/a Patrocinante</td>
                                            <td>${escaparHtmlPostdoctorado(textoPostdoctorado(postdoctorado.prof))}</td>
                                        </tr>
                                        <tr>
                                            <td>Institución</td>
                                            <td>${escaparHtmlPostdoctorado(textoPostdoctorado(postdoctorado.inst))}</td>
                                        </tr>
                                        <tr>
                                            <td>Fecha Inicio</td>
                                            <td>${escaparHtmlPostdoctorado(fechaCivil(postdoctorado.fecha_inicio).presentacion)}</td>
                                        </tr>
                                        <tr>
                                            <td>Fecha de Término</td>
                                            <td>${escaparHtmlPostdoctorado(fechaCivil(postdoctorado.fecha_termino).presentacion)}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            });
            if (response.datos.length === 0) {
                template = '<p class="text-muted mb-3">No hay Postdoctorados registrados.</p>';
            }
            $(destino).empty().append(template);
        },
        error: function (xhr) {
            $(destino).empty();
            mostrarMensajePostdoctorado(mensajeErrorPostdoctorado(xhr), true);
        }
    });
}

function leerDatosPostdoc() {
    const profesor = guardar(String($('#prof_postdoc').val() || ''));
    const institucion = $('#inst_postdoc').val() === 'otro'
        ? espacios(String($('#nuevo_inst').val() || ''))
        : $('#inst_postdoc').val();
    return {
        prof: profesor,
        inst: institucion,
        fech_in: $('#fech_in_postdoc').val(),
        fech_ter: $('#fech_ter_postdoc').val()
    };
}

function validarFormularioPostdoctorado(datos) {
    if (
        datos.prof === ''
        || datos.fech_in === ''
        || datos.fech_ter === ''
        || datos.inst === '0'
        || datos.inst === 0
        || datos.inst === ''
        || datos.inst === null
    ) {
        validCampoVacio('#prof_postdoc');
        validSelect('#inst_postdoc');
        validCampoVacio('#fech_in_postdoc');
        validCampoVacio('#fech_ter_postdoc');
        mostrarMensajePostdoctorado('Rellene todos los campos requeridos.', true);
        return false;
    }
    return true;
}

$('body').on('click', '.editarPostdoc', function () {
    const idPostdoctorado = $(this).attr('data-id-postdoc');
    const usuario = usuarioContextoPostdoctorado($(this).attr('data-usuario'));
    const contenedor = resolverContenedorPostdoctorado($(this).attr('data-contenedor'));

    $('#campos_postdoc').empty();
    abrirEditorPostdoctorado($('#form_edit_postdoc'));

    $('#form_edit_postdoc')
        .attr('data-id-postdoctorado', idPostdoctorado)
        .data('postdoctorado-usuario', usuario)
        .data('postdoctorado-contenedor', contenedor);
    $('#campos_postdoc').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h3 id="text-tit">EDITAR POSTDOCTORADO</h3></div><div class="col-auto"><button type="button" class="btn btn-close btn-sm cerrar-editor-postdoctorado" aria-label="Cerrar"></button></div></div>');

    const datosLectura = agregarUsuarioObjetivoPostdoctorado(
        { op: 'read_postdoc_id', id_postdoc: idPostdoctorado },
        usuario
    );
    $.ajax({
        url: '../ajax/postdoctorado.php',
        type: 'POST',
        dataType: 'json',
        data: datosLectura,
        success: function (response) {
            if (!response || response.ok !== true || !response.datos) {
                mostrarMensajePostdoctorado(response && response.mensaje, true);
                return;
            }

            const postdoctorado = response.datos;
            formPostdoc('#campos_postdoc', postdoctorado.id_inst);
            $('#campos_postdoc').append('<div class="row mt-5 justify-content-end"><div class="col-6 col-md-4 mb-3"><button type="submit" class="col-12 btn btn-dark">Editar</button></div></div>');
            $('#inst_postdoc').val(String(postdoctorado.id_inst));
            $('#prof_postdoc').val(textoPostdoctorado(postdoctorado.prof));
            $('#fech_in_postdoc').val(postdoctorado.fecha_inicio);
            $('#fech_ter_postdoc').val(postdoctorado.fecha_termino);
            const editorPostdoc = document.getElementById('form_edit_postdoc'); if (editorPostdoc) editorPostdoc.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        error: function (xhr) {
            mostrarMensajePostdoctorado(mensajeErrorPostdoctorado(xhr), true);
        }
    });
});
$(document).on('click', '.cerrar-editor-postdoctorado', cerrarEditorPostdoctorado);

$('body').on('click', '.eliminarPostdoc', function () {
    const idPostdoctorado = $(this).attr('data-id-postdoc');
    if (!window.confirm('¿Confirma que desea eliminar este Postdoctorado?')) {
        return;
    }

    const usuario = usuarioContextoPostdoctorado($(this).attr('data-usuario'));
    const contenedor = resolverContenedorPostdoctorado($(this).attr('data-contenedor'));
    const datosDelete = agregarUsuarioObjetivoPostdoctorado(
        { id_postdoc: idPostdoctorado, op: 'delete' },
        usuario
    );
    $.ajax({
        url: '../ajax/postdoctorado.php',
        type: 'POST',
        dataType: 'json',
        headers: headersCsrfPostdoctorado(),
        data: datosDelete,
        success: function (response) {
            if (!response || response.ok !== true) {
                mostrarMensajePostdoctorado(response && response.mensaje, true);
                return;
            }
            cargarPostdoc(usuario, contenedor);
            mostrarMensajePostdoctorado(response.mensaje, false);
        },
        error: function (xhr) {
            mostrarMensajePostdoctorado(mensajeErrorPostdoctorado(xhr), true);
        }
    });
});

$('#form_postdoc').submit(function (event) {
    event.preventDefault();
    const datosPostdoctorado = leerDatosPostdoc();
    if (!validarFormularioPostdoctorado(datosPostdoctorado)) {
        return;
    }

    const usuario = usuarioContextoPostdoctorado();
    if ($('#inst_postdoc').val() === 'otro') {
        datosPostdoctorado.institucion_nueva = datosPostdoctorado.inst;
        datosPostdoctorado.inst = 0;
    }

    datosPostdoctorado.op = 'insert';
    agregarUsuarioObjetivoPostdoctorado(datosPostdoctorado, usuario);
    $.ajax({
        url: '../ajax/postdoctorado.php',
        type: 'POST',
        dataType: 'json',
        headers: headersCsrfPostdoctorado(),
        data: datosPostdoctorado,
        success: function (response) {
            if (!response || response.ok !== true) {
                mostrarMensajePostdoctorado(response && response.mensaje, true);
                return;
            }

            mostrarMensajePostdoctorado(response.mensaje, false);
            setTimeout(function () {
                $('#ingresar_postdoc').remove();
                cerrarEditorPostdoctorado();
                cargarPostdoc(usuario, listadoPostdoctorado());
            }, 1500);
        },
        error: function (xhr) {
            mostrarMensajePostdoctorado(mensajeErrorPostdoctorado(xhr), true);
        }
    });
});

$('#form_edit_postdoc').submit(function (event) {
    event.preventDefault();
    const datosPostdoctorado = leerDatosPostdoc();
    if (!validarFormularioPostdoctorado(datosPostdoctorado)) {
        return;
    }

    const usuario = usuarioContextoPostdoctorado(
        $('#form_edit_postdoc').data('postdoctorado-usuario')
    );
    if ($('#inst_postdoc').val() === 'otro') {
        datosPostdoctorado.institucion_nueva = datosPostdoctorado.inst;
        datosPostdoctorado.inst = 0;
    }

    datosPostdoctorado.op = 'update';
    datosPostdoctorado.id_postdoc = $('#form_edit_postdoc').attr('data-id-postdoctorado');
    agregarUsuarioObjetivoPostdoctorado(datosPostdoctorado, usuario);
    $.ajax({
        url: '../ajax/postdoctorado.php',
        type: 'POST',
        dataType: 'json',
        headers: headersCsrfPostdoctorado(),
        data: datosPostdoctorado,
        success: function (response) {
            if (!response || response.ok !== true) {
                mostrarMensajePostdoctorado(response && response.mensaje, true);
                return;
            }

            mostrarMensajePostdoctorado(response.mensaje, false);
            setTimeout(function () {
                const contenedor = $('#form_edit_postdoc').data('postdoctorado-contenedor');
                cerrarEditorPostdoctorado();
                cargarPostdoc(usuario, contenedor);
            }, 1500);
        },
        error: function (xhr) {
            mostrarMensajePostdoctorado(mensajeErrorPostdoctorado(xhr), true);
        }
    });
});
