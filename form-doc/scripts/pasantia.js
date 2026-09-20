(function ($) {
    'use strict';
    const $app = $('#pasantia-app');
    if (!$app.length) return;
    const endpoint = '../ajax/pasantia.php';
    const csrf = String($app.data('csrf') || '');
    const esGlobal = String($app.data('contexto') || '') === 'global';
    const puedeOperar = ['propio', 'global'].includes(String($app.data('contexto') || ''));
    let destinoActual = '#pasantia_card', usuarioActual = null, temporizador = null, idInstitucionContextual = null;

    function mensaje(texto, tipo, temporal) {
        clearTimeout(temporizador);
        $('#mnsj_pasant').removeClass('alert-success alert-danger alert-warning').addClass(`alert-${tipo || 'danger'}`).text(texto || 'No fue posible completar la operación de Pasantía.');
        $('#mnsj_row_pasant').stop(true, true).show();
        if (temporal && tipo === 'success') temporizador = setTimeout(() => $('#mnsj_row_pasant').fadeOut(1500), 3000);
    }
    function error(xhr) { return xhr && xhr.responseJSON && xhr.responseJSON.mensaje ? xhr.responseJSON.mensaje : 'No fue posible comunicarse con el servicio de Pasantías.'; }
    function idValido(valor) { return /^[1-9]\d*$/.test(String(valor || '')); }
    function objetivo() { const id = String(usuarioActual || $('#id_usuario').attr('name') || ''); return idValido(id) ? id : null; }
    function solicitud(datos, escritura) { return $.ajax({ url: endpoint, type: 'POST', dataType: 'json', data: datos, headers: escritura ? { 'X-CSRF-Token': csrf } : {} }); }
    function dato($tabla, etiqueta, valor) { const $fila = $('<tr>').appendTo($tabla); $('<td>').text(etiqueta).appendTo($fila); $('<td>').text(valor == null ? '' : String(valor)).appendTo($fila); }
    function fecha(valor) { const texto = String(valor || ''); return /^\d{4}-\d{2}-\d{2}$/.test(texto) ? texto.split('-').reverse().join('-') : ''; }
    function cerrarEditor() { const $zona = $('#pasantia').closest('.oa-zone'); $('#pasantia').empty(); if ($zona.length) $zona.find('.oa-list,.oa-add').show(); else $('#boton_pasantia').show(); idInstitucionContextual = null; }
    function agregarObjetivo(datos) { if (!esGlobal) return true; const id = objetivo(); if (!id) { mensaje('Seleccione primero un usuario válido.', 'danger'); return false; } datos.usuario = id; return true; }

    function catalogo(selector, url, id, texto, seleccionado) {
        const $select = $(selector).empty().append($('<option>', { value: '', text: 'Seleccione' }));
        return $.ajax({ url, type: 'POST', dataType: 'json', data: { op: 'read' } }).done(function (respuesta) {
            const filas = Array.isArray(respuesta) ? respuesta : (respuesta && Array.isArray(respuesta.datos) ? respuesta.datos : []);
            filas.forEach(fila => { if (fila && fila[id] !== undefined && fila[texto] !== undefined) $('<option>', { value: String(fila[id]), text: String(fila[texto]) }).appendTo($select); });
            if (id === 'id_inst') $('<option>', { value: 'otro', text: 'Otra Institución' }).appendTo($select);
            if (seleccionado) $select.val(String(seleccionado));
        }).fail(() => mensaje('No fue posible cargar las opciones requeridas.', 'danger'));
    }

    function editor(pasantia) {
        const editando = Boolean(pasantia), $zona = $('#pasantia').empty().show(), $card = $('<div>', { class: 'card mb-3', id: 'pasantia_editor' }).appendTo($zona), $body = $('<div>', { class: 'card-body' }).appendTo($card), $form = $('<form>', { id: 'pasantia_formulario_editor', novalidate: true }).appendTo($body);
        $body.prepend($('<div>', { class: 'd-flex justify-content-between' }).append($('<h4>', { class: 'card-title', text: editando ? 'Editar Pasantía' : 'Agregar Pasantía' }), $('<button>', { type: 'button', class: 'btn-close', id: 'pasantia_cerrar', 'aria-label': 'Cerrar' })));
        $('<input>', { type: 'hidden', id: 'pasantia_id', value: editando ? pasantia.id_pasantia : '' }).appendTo($form);
        const campos = [
            ['pasantia_institucion', 'Institución', 'select', 'col-md-6 mb-3'], ['pasantia_patrocinante', 'Profesor/a Patrocinante', 'text', 'col-md-6 mb-3'],
            ['pasantia_fondo', 'Fondo', 'text', 'col-md-6 mb-3'], ['pasantia_ciudad', 'Ciudad', 'text', 'col-md-6 mb-3'],
            ['pasantia_pais', 'País', 'select', 'col-md-4 mb-3'], ['pasantia_fecha_inicio', 'Fecha Inicio', 'date', 'col-md-4 mb-3'], ['pasantia_fecha_termino', 'Fecha Término', 'date', 'col-md-4 mb-3']
        ];
        let $fila;
        campos.forEach((campo, indice) => { if (indice === 0 || indice === 2 || indice === 4) $fila = $('<div>', { class: 'row' }).appendTo($form); const $col = $('<div>', { class: campo[3] }).appendTo($fila); $('<label>', { class: 'form-label', for: campo[0], text: campo[1] }).appendTo($col); $('<' + (campo[2] === 'select' ? 'select' : 'input') + '>', { class: 'form-control' + (campo[2] === 'select' ? ' form-select' : ''), type: campo[2] === 'select' ? undefined : campo[2], id: campo[0], maxlength: campo[2] === 'text' ? 45 : undefined, required: true }).appendTo($col); });
        const $acciones = $('<div>', { class: 'd-flex justify-content-end gap-2' }).appendTo($form);
        $('<button>', { type: 'button', class: 'btn btn-outline-secondary', id: 'pasantia_cancelar', text: 'Cancelar' }).appendTo($acciones);
        $('<button>', { type: 'submit', class: 'btn btn-dark', id: 'pasantia_guardar', text: editando ? 'Guardar cambios' : 'Guardar Pasantía' }).appendTo($acciones);
        const $zonaOa = $('#pasantia').closest('.oa-zone');
        if ($zonaOa.length) $zonaOa.find('.oa-list,.oa-add').hide(); else $('#boton_pasantia').hide();
        const editorVisible = document.getElementById('pasantia_editor'); if (editorVisible) editorVisible.scrollIntoView({ behavior: 'smooth', block: 'start' });
        catalogo('#pasantia_institucion', '../ajax/institucion.php', 'id_inst', 'inst', editando ? pasantia.inst_pasant : null);
        catalogo('#pasantia_pais', '../ajax/pais.php', 'id_pais', 'pais', editando ? pasantia.pais_pasant : null);
        if (editando) { $('#pasantia_patrocinante').val(pasantia.prof_patr || ''); $('#pasantia_fondo').val(pasantia.fondo || ''); $('#pasantia_ciudad').val(pasantia.ciudad || ''); $('#pasantia_fecha_inicio').val(pasantia.fech_in || ''); $('#pasantia_fecha_termino').val(pasantia.fech_ter || ''); }
    }

    function renderizar(filas, destino) {
        const $destino = $(destino).empty();
        if (!Array.isArray(filas) || !filas.length) { $('<p>', { class: 'text-muted mb-3', text: 'No hay Pasantías registradas.' }).appendTo($destino); return; }
        filas.forEach(function (p) {
            const $cuerpo = $('<div>', { class: 'card-body' }), $tabla = $('<table>', { class: 'table table-striped' }).appendTo($cuerpo), $encabezado = $('<tr>').appendTo($('<thead>').appendTo($tabla));
            $('<th>', { class: 'col-md-4 titulo_acad' }).append($('<h5>', { text: 'PASANTÍA' })).appendTo($encabezado);
            const $acciones = $('<th>', { class: 'text-end' }).appendTo($encabezado);
            if (puedeOperar) {
                $('<button>', { type: 'button', class: 'btn btn-link link-success pasantia-editar', text: 'Editar' }).data('id', String(p.id_pasantia)).appendTo($acciones);
                $('<button>', { type: 'button', class: 'btn btn-link link-danger pasantia-eliminar', text: 'Eliminar' }).data('id', String(p.id_pasantia)).appendTo($acciones);
            }
            const $datos = $('<tbody>').appendTo($tabla);
            [['Profesor/a Patrocinante', p.prof_patr], ['Institución', p.inst], ['Fecha Inicio', fecha(p.fech_in)], ['Fecha de Término', fecha(p.fech_ter)], ['Fondo', p.fondo], ['Ciudad', p.ciudad], ['País', p.pais]].forEach(fila => dato($datos, fila[0], fila[1]));
            $('<div>', { class: 'col-12' }).append($('<div>', { class: 'card mb-3 pasantia-registro' }).append($cuerpo)).appendTo($destino);
        });
    }

    window.cargarPasantia = function (usuario, destino) {
        destinoActual = destino || '#pasantia_card'; const datos = { op: 'list' }; const id = esGlobal ? String(usuario || $('#id_usuario').attr('name') || '') : null;
        if (esGlobal && !idValido(id)) { $(destinoActual).empty(); return $.Deferred().resolve().promise(); }
        usuarioActual = esGlobal ? id : null; if (esGlobal) datos.usuario = id;
        return solicitud(datos, false).done(respuesta => { if (!respuesta || respuesta.ok !== true) return mensaje(respuesta && respuesta.mensaje, 'danger'); renderizar(respuesta.datos, destinoActual); }).fail(xhr => { $(destinoActual).empty(); mensaje(error(xhr), 'danger'); });
    };
    function detalle(id) { const datos = { op: 'detail', id_pasantia: id }; if (agregarObjetivo(datos)) solicitud(datos, false).done(r => r && r.ok ? editor(r.datos) : mensaje(r && r.mensaje, 'danger')).fail(xhr => mensaje(error(xhr), 'danger')); }
    function datosFormulario() {
        const datos = { inst_pasant: String($('#pasantia_institucion').val() || ''), pais_pasant: String($('#pasantia_pais').val() || ''), prof_patr: String($('#pasantia_patrocinante').val() || '').trim(), fondo: String($('#pasantia_fondo').val() || '').trim(), ciudad: String($('#pasantia_ciudad').val() || '').trim(), fech_in: String($('#pasantia_fecha_inicio').val() || ''), fech_ter: String($('#pasantia_fecha_termino').val() || '') };
        if (datos.inst_pasant === 'otro') { const nombre = String($('#pasantia_nueva_institucion').val() || '').trim(); if (!nombre) { mensaje('Ingrese una Institución válida.', 'danger'); return null; } const alta = crearInstitucionContextual(nombre, objetivo() || 0, 'pasantia'); if (!alta.ok) { mensaje(alta.mensaje, 'danger'); return null; } idInstitucionContextual = String(alta.id); datos.inst_pasant = idInstitucionContextual; } else idInstitucionContextual = null;
        if (!idValido(datos.inst_pasant) || !idValido(datos.pais_pasant) || !datos.prof_patr || !datos.fondo || !datos.ciudad || !datos.fech_in || !datos.fech_ter) { mensaje('Complete todos los campos requeridos.', 'danger'); return null; }
        if ([datos.prof_patr, datos.fondo, datos.ciudad].some(t => Array.from(t).length > 45)) { mensaje('Patrocinante, fondo y ciudad admiten hasta 45 caracteres.', 'danger'); return null; }
        if (datos.fech_in > datos.fech_ter) { mensaje('La fecha de inicio no puede ser posterior al término.', 'danger'); return null; } return datos;
    }
    $('#btn_pasantia').on('click', () => editor(null));
    $(document).on('change', '#pasantia_institucion', function () { idInstitucionContextual = null; $('#pasantia_nueva_institucion_col').remove(); if ($(this).val() === 'otro') { const $col = $('<div>', { class: 'col-md-6 mb-3', id: 'pasantia_nueva_institucion_col' }); $('<label>', { class: 'form-label', for: 'pasantia_nueva_institucion', text: 'Nueva Institución' }).appendTo($col); $('<input>', { class: 'form-control', type: 'text', id: 'pasantia_nueva_institucion', maxlength: 80, required: true }).appendTo($col); $col.insertAfter($(this).closest('.col-md-6')); } });
    $(document).on('click', '#pasantia_cancelar', cerrarEditor);
    $(document).on('click', '#pasantia_cerrar', cerrarEditor);
    $(document).on('submit', '#pasantia_formulario_editor', function (evento) { evento.preventDefault(); const datos = datosFormulario(), id = String($('#pasantia_id').val() || ''); if (!datos || !agregarObjetivo(datos)) return; datos.op = id ? 'update' : 'create'; if (id) datos.id_pasantia = id; $('#pasantia_guardar').prop('disabled', true); solicitud(datos, true).done(r => { if (!r || r.ok !== true) return mensaje(r && r.mensaje, 'danger'); cerrarEditor(); mensaje(r.mensaje, 'success', true); window.cargarPasantia(usuarioActual, destinoActual); }).fail(xhr => mensaje(error(xhr), 'danger')).always(() => $('#pasantia_guardar').prop('disabled', false)); });
    $(document).on('click', '.pasantia-editar', function () { detalle(String($(this).data('id'))); });
    $(document).on('click', '.pasantia-eliminar', function () { const id = String($(this).data('id')); if (!window.confirm('¿Confirma la eliminación permanente de esta Pasantía?')) return; const datos = { op: 'delete', id_pasantia: id }; if (!agregarObjetivo(datos)) return; $('.pasantia-eliminar').prop('disabled', true); solicitud(datos, true).done(r => { if (!r || r.ok !== true) return mensaje(r && r.mensaje, 'danger'); mensaje(r.mensaje, 'success', true); window.cargarPasantia(usuarioActual, destinoActual); }).fail(xhr => mensaje(error(xhr), 'danger')).always(() => $('.pasantia-eliminar').prop('disabled', false)); });
}(jQuery));
