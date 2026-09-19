/* global $ */
(function () {
  'use strict';
  const endpoint = '../ajax/publicacion.php';
  const context = { loaded: false, pending: null, csrfToken: null, canManageGlobal: false };
  let usuarioContextual = null;
  let selectorListado = '#publicacion_card';
  let editorialesExistentes = [];
  let editorialesNuevas = [];

  function notify(message) { const $m = $('#mnsj_pub'); if ($m.length) $m.text(message).show(); }
  function failure(xhr) { return xhr && xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'No fue posible completar la operación.'; }
  function request(data, write) {
    const options = { url: endpoint, type: 'POST', dataType: 'json', data };
    if (write) options.headers = { 'X-CSRF-Token': context.csrfToken };
    return $.ajax(options).then(function (response) {
      return response && response.status === 'OK' ? response : $.Deferred().reject({ responseJSON: response }).promise();
    });
  }
  function ensurePublicacionContext() {
    if (context.loaded) return $.Deferred().resolve(context).promise();
    if (context.pending) return context.pending;
    context.pending = request({ op: 'context' }).then(function (response) {
      const data = response.data || {};
      if (typeof data.csrf_token !== 'string' || typeof data.can_manage_global !== 'boolean') return $.Deferred().reject().promise();
      context.loaded = true; context.csrfToken = data.csrf_token; context.canManageGlobal = data.can_manage_global;
      return context;
    }).always(function () { context.pending = null; });
    return context.pending;
  }
  function payloadContextual() { return context.canManageGlobal && usuarioContextual ? { usuario: usuarioContextual, subject_usuario_id: usuarioContextual } : {}; }
  function value(value) { return value == null ? '' : String(value); }
  function nombreNominal(entrada) { const texto = value(entrada).trim().replace(/\s+/g, ' '); return texto ? cadenaMay(texto) : ''; }
  function otrosCoautores(entrada) { const texto = value(entrada).trim(); return texto ? nombreNominal(texto) : 'Sin coautores adicionales.'; }
  function editorialesLibro(editoriales) { const nombres = (Array.isArray(editoriales) ? editoriales : []).map(function (editorial) { return nombreNominal(editorial && editorial.nombre); }).filter(Boolean); return nombres.length ? nombres.join(', ') : 'Sin editoriales registradas.'; }
  function action(label, className, data) { const $button = $('<button>', { type: 'button', class: className }).text(label); if (data !== undefined) $button.data(data); return $button; }
  function tableCard(title, fields, controls) {
    const $table = $('<table>', { class: 'table table-striped' });
    const $tr = $('<tr>').append($('<th>').append($('<h5>').text(title)));
    $tr.append($('<th>', { class: 'row justify-content-end ps-0 botones' }).append(controls));
    const $body = $('<tbody>');
    fields.forEach(function (field) { $body.append($('<tr>').append($('<td>').text(field[0]), $('<td>').text(value(field[1])))); });
    return $('<div>', { class: 'card mb-3 card_pub' }).append($('<div>', { class: 'card-body' }).append($table.append($('<thead>').append($tr), $body)));
  }
  function controls(id, type, otra) {
    if (!context.canManageGlobal) return [];
    return [action('Editar', otra ? 'col-auto btn btn-link link-success ps-1 editarOtraPub' : 'col-auto btn btn-link link-success ps-1 editarPub', { id, tipo: type }), action('Eliminar', otra ? 'col-auto btn btn-link link-danger ps-1 eliminarOtraPub' : 'col-auto btn btn-link link-danger ps-1 eliminarPub', { id })];
  }
  function render($target, list, otras) {
    $target.empty();
    (list.articulos || []).forEach(function (p) { $target.append(tableCard('PUBLICACIÓN', [['Tipo', nombreNominal(Number(p.tipo) === 1 ? 'Artículo' : 'Edición de Revista Temática')], ['Título', nombreNominal(p.titulo)], ['Año', p.anio], ['Nombre Revista', nombreNominal(p.nombre_revista)], ['Indización', nombreNominal(p.indizacion)], ['Estado', nombreNominal(p.estado_publicacion)], ['Issn', p.issn], ['Factor de Impacto', Number(p.factor_impacto) ? p.factor_impacto : 'No tiene factor de impacto'], ['Autor principal', nombreNominal(p.autor_nombre)], ['Coautor principal', nombreNominal(p.coautor_nombre)], ['Otros coautores', otrosCoautores(p.otros_autores)]], controls(Number(p.id_publicacion), Number(p.tipo), false))); });
    (list.libros || []).forEach(function (p) { $target.append(tableCard('PUBLICACIÓN', [['Tipo', nombreNominal(Number(p.tipo) === 1 ? 'Libro' : 'Capítulo de Libro')], ['Nombre', nombreNominal(p.nom_pub)], ['Rol', nombreNominal(Number(p.rol) === 1 ? 'Autor/a' : 'Editor/a')], ['Año', p.anio], ['Estado', nombreNominal(p.nom_est)], ['Lugar', nombreNominal(p.lugar)], ['Autor principal', nombreNominal(p.autor_nombre)], ['Coautor principal', nombreNominal(p.coautor_nombre)], ['Otros coautores', otrosCoautores(p.otros_autores)], ['Editoriales', editorialesLibro(p.editoriales)]], controls(Number(p.id_publicacion), 3, false))); });
    (otras || []).forEach(function (p) { $target.append(tableCard('OTRA PUBLICACIÓN', [['Tipo', nombreNominal(p.tipo)], ['Descripción', nombreNominal(p.descripcion)]], controls(Number(p.id_otra_pub), 4, true))); });
  }
  window.cargarPub = function (usuario, selector) {
    usuarioContextual = Number.parseInt(usuario, 10) || null;
    if (selector) selectorListado = selector;
    const $target = $(selectorListado).empty();
    return ensurePublicacionContext().then(function () { return $.when(request(Object.assign({ op: 'list' }, payloadContextual())), request(Object.assign({ op: 'otra_list' }, payloadContextual()))); }).then(function (list, otras) { render($target, list.data || {}, otras.data || []); }).fail(function (xhr) { notify(failure(xhr)); });
  };
  function restaurarListadoPublicacion() {
    $('#campos_publicacion').empty();
    if (selectorListado === '#ficha_publicacion') {
      $('#edit_acad_doc').stop(true, true).hide();
      $('#info_doc').stop(true, true).show();
    } else if (selectorListado === '#publi_est') {
      $('#edit_acad_est').stop(true, true).hide();
      $('#info_est').stop(true, true).show();
    } else if ($('#ficha_acad').attr('name') === 'doc') {
      $('#edit_acad_doc').stop(true, true).hide();
      $('#acad_doc').stop(true, true).show();
    } else {
      $('#edit_acad_est').stop(true, true).hide();
      $('#acad_est').stop(true, true).show();
    }
    return window.cargarPub(usuarioContextual, selectorListado);
  }
  function input(label, id, type, max) { return $('<div>', { class: 'col-md-6 mb-3' }).append($('<label>', { for: id, class: 'form-label' }).text(label), type === 'textarea' ? $('<textarea>', { id, class: 'form-control', maxlength: max }) : $('<input>', { id, type: type || 'text', class: 'form-control', maxlength: max })); }
  function select(label, id, choices) { const $s = $('<select>', { id, class: 'form-select' }).append($('<option>', { value: '' }).text('Seleccione')); choices.forEach(function (c) { $s.append($('<option>', { value: c[0] }).text(c[1])); }); return $('<div>', { class: 'col-md-6 mb-3' }).append($('<label>', { for: id, class: 'form-label' }).text(label), $s); }
  function editoriales($root) { $root.append($('<div>', { class: 'row' }).append(input('Editorial(es)', 'editorial', 'text', 60), $('<div>', { class: 'col-md-3 align-self-end' }).append(action('Agregar editorial', 'btn btn-dark btn_editorial'))), $('<ul>', { id: 'list_editorial', class: 'list-group' }).hide(), $('<div>', { id: 'editoriales_seleccionadas' })); renderEditoriales(); }
  function renderEditoriales() { const $root = $('#editoriales_seleccionadas').empty(); editorialesExistentes.forEach(function (p) { $root.append(action(p.nombre + ' ×', 'btn btn-sm btn-outline-secondary me-1 quitar-editorial', { kind: 'old', id: p.id })); }); editorialesNuevas.forEach(function (p) { $root.append(action(p + ' ×', 'btn btn-sm btn-outline-secondary me-1 quitar-editorial', { kind: 'new', name: p })); }); }
  function fields($root, type) {
    $root.append($('<div>', { class: 'row' }).append(input(type === 3 ? 'Nombre libro' : 'Nombre Revista', 'nombre', 'text', 60), input('Año', 'anio', 'number')),$('<div>', { class: 'row' }).append(input('Otros/as Autores/as', 'autores', 'textarea', 300), select('Estado', 'estado', [[1, 'Publicada'], [2, 'En Prensa'], [3, 'Aceptada'], [4, 'Enviada']])));
    if (type < 3) {
      const $factor = input('Factor de Impacto', 'fac_imp', 'number');
      const $sinFactor = $('<div>', { class: 'col-md-6 mb-3 align-self-end' }).append($('<div>', { class: 'form-check' }).append($('<input>', { id: 'no_fac', type: 'checkbox', class: 'form-check-input' }), $('<label>', { for: 'no_fac', class: 'form-check-label' }).text('No tiene Factor de Impacto')));
      $root.append($('<div>', { class: 'row' }).append(input('Título', 'titulo', 'textarea', 300), input('Issn', 'issn', 'text', 9)),$('<div>', { class: 'row' }).append(select('Indización', 'indizacion', [[1, 'No tiene'], [2, 'Wos'], [3, 'Scopus'], [4, 'Erih Plus'], [5, 'Scielo'], [6, 'Latindex']]), $factor), $('<div>', { class: 'row' }).append($sinFactor));
    }
    else $root.append($('<div>', { class: 'row' }).append(select('Tipo libro', 'tipo_lib', [[1, 'Libro'], [2, 'Capítulo']]), select('Rol bibliográfico', 'rol_libro', [[1, 'Autor/a'], [2, 'Editor/a']])),$('<div>', { class: 'row' }).append(select('Referato externo', 'ref_ext', [[1, 'Sí'], [0, 'No']]), select('Traducción', 'traduccion', [[1, 'Sí'], [0, 'No']])), $('<div>', { class: 'row' }).append(input('Lugar', 'lugar', 'text', 45)), editoriales($root));
  }
  function formData(type) { const data = { tipo: type, nombre: $.trim($('#nombre').val()), autores: $.trim($('#autores').val()), anio: Number($('#anio').val()), estado: Number($('#estado').val()) }; if (type < 3) Object.assign(data, { titulo: $.trim($('#titulo').val()), issn: $.trim($('#issn').val()), indizacion: Number($('#indizacion').val()), fac_imp: $('#no_fac').is(':checked') ? 0 : Number($('#fac_imp').val()) }); else Object.assign(data, { tipo_lib: Number($('#tipo_lib').val()), rol_libro: Number($('#rol_libro').val()), ref_ext: Number($('#ref_ext').val()), traduccion: Number($('#traduccion').val()), lugar: $.trim($('#lugar').val()), editoriales_existentes: editorialesExistentes.map(function (p) { return p.id; }), editoriales_nuevas: editorialesNuevas.slice() }); return data; }
  function valid(data) { return data.nombre && data.anio && data.estado && (data.tipo === 3 ? data.tipo_lib && data.rol_libro && data.lugar : data.titulo && data.issn && data.indizacion && ($('#no_fac').is(':checked') || $.trim($('#fac_imp').val()) !== '')); }
  function syncFactorImpacto() { const disabled = $('#no_fac').is(':checked'); $('#fac_imp').prop('disabled', disabled); if (disabled) $('#fac_imp').val(''); }
  function participantBlock() {
    const $box=$('<fieldset>',{class:'border rounded p-3 mb-3 participant-block'}).append($('<legend>',{class:'float-none w-auto px-2 fs-6'}).text('Autoría principal'));
    const $role=select('Rol en la publicación','rol_propio',[['AUTOR','Autor'],['COAUTOR','Coautor']]);
    const $input=$('<input>',{type:'text',id:'persona_contraria',class:'form-control academic-search',autocomplete:'off',maxlength:120});
    return $box.append($('<div>',{class:'row'}).append($role),$('<label>',{for:'persona_contraria',class:'form-label'}).text('Coautor'),$input,$('<div>',{class:'small mt-1 academic-selected'}).data('id',null),$('<ul>',{class:'list-group academic-results mt-1'}).hide());
  }
  function participantsData() {
    const rol=$('#rol_propio').val(), id=Number($('.academic-selected').data('id'))||null, texto=$.trim($('#persona_contraria').val());
    let data={};
    if(!rol)return {data:data,error:'Seleccione el rol en la publicación.'}; if(!texto)return {data:data,error:'Ingrese la persona del rol contrario.'};
    const propio=usuarioContextual, contrarioUsuario=id||'', contrarioNombre=id?'':texto;
    if(id&&id===propio)return {data:data,error:'Autor y Coautor principal no pueden ser el mismo usuario.'};
    if(rol==='AUTOR'){data={autor_usuario:propio,autor_nombre_externo:'',coautor_usuario:contrarioUsuario,coautor_nombre_externo:contrarioNombre};}else{data={autor_usuario:contrarioUsuario,autor_nombre_externo:contrarioNombre,coautor_usuario:propio,coautor_nombre_externo:''};}
    return {data:data,error:!propio?'No existe usuario contextual de ficha.':''};
  }
  function hydrateParticipant(autor, coautor) {
    const propioAutor=autor && Number(autor.usuario)===usuarioContextual, propioCoautor=coautor && Number(coautor.usuario)===usuarioContextual;
    const propio=propioAutor?'AUTOR':propioCoautor?'COAUTOR':''; const contrario=propioAutor?coautor:autor;
    $('#rol_propio').val(propio); actualizarEtiquetaContraria();
    if(!contrario)return; $('#persona_contraria').val(contrario.tipo==='INTERNO'?(contrario.nombre||''):(contrario.nombre_externo||'')); $('.academic-selected').text(contrario.tipo==='INTERNO'?(contrario.nombre||''):'').data('id',contrario.tipo==='INTERNO'?Number(contrario.usuario):null);
  }
  function actualizarEtiquetaContraria(){ $('#persona_contraria').prev('label').text($('#rol_propio').val()==='AUTOR'?'Coautor':'Autor'); }
  function openEdit(idPublicacion, type) {
    if (!context.canManageGlobal) return;
    if ($('#ficha_acad').attr('name') === 'doc') editAcadDoc(); else editAcadEst();
    request({ op: 'detail', id_publicacion: idPublicacion, tipo: type }).then(function (response) {
      const data = response.data || {};
      const $box = $('<div>', { class: 'publicacion-edit', 'data-id-publicacion': idPublicacion, 'data-tipo': type });
      const $campos = $('#campos_publicacion');
      $campos.closest('#edit_acad_doc, #edit_acad_est').stop(true, true).show();
      const $header = $('<div>',{class:'row justify-content-between'}).append($('<div>',{class:'col-auto mb-3'}).append($('<h3>',{class:'card-title'}).text('EDITAR PUBLICACIÓN')),$('<div>',{class:'col-auto'}).append($('<button>',{type:'button',class:'btn btn-close btn-sm cancelar-publicacion','aria-label':'Cerrar'})));
      editorialesExistentes = type === 3 ? (data.editoriales || []).map(function (p) { return { id: Number(p.id_editorial), nombre: value(p.nombre) }; }).filter(function (p) { return p.id > 0; }) : [];
      editorialesNuevas = [];
      fields($box, type); $box.append(participantBlock());
      $box.append($('<div>', { class: 'row mt-5 justify-content-end' }).append($('<div>', { class: 'col-12 col-md-4 mb-3 d-grid' }).append(action('Guardar cambios', 'col-12 btn btn-dark guardar-edicion'))));
      $campos.stop(true, true).show().empty().append($header, $box);
      $('#nombre').val(data.nombre); $('#autores').val(data.otros_autores || ''); $('#anio').val(data.anio); $('#estado').val(data.estado);
      hydrateParticipant(data.autor,data.coautor);
      if (type === 3) { $('#tipo_lib').val(data.tipo); $('#rol_libro').val(data.rol); $('#ref_ext').val(data.ref_ext); $('#traduccion').val(data.traduccion); $('#lugar').val(data.lugar); renderEditoriales(); }
      else { $('#titulo').val(data.titulo); $('#issn').val(data.issn); $('#indizacion').val(data.indizacion); $('#no_fac').prop('checked', Number(data.factor_impacto) === 0); $('#fac_imp').val(Number(data.factor_impacto) === 0 ? '' : data.factor_impacto); syncFactorImpacto(); }
    }).fail(function (xhr) { notify(failure(xhr)); });
  }
  function openOtraEdit(idOtraPub) {
    if (!context.canManageGlobal) return;
    if ($('#ficha_acad').attr('name') === 'doc') editAcadDoc(); else editAcadEst();
    request({ op: 'otra_detail', id_otra_pub: idOtraPub }).then(function (response) {
      const data = response.data || {};
      const $box = $('<div>', { class: 'otra-edit', 'data-id-otra-pub': idOtraPub });
      const $campos = $('#campos_publicacion');
      $campos.closest('#edit_acad_doc, #edit_acad_est').stop(true, true).show();
      const $header = $('<div>', { class: 'row justify-content-between' }).append($('<div>', { class: 'col-auto mb-3' }).append($('<h3>', { class: 'card-title' }).text('EDITAR OTRA PUBLICACIÓN')), $('<div>', { class: 'col-auto' }).append($('<button>', { type: 'button', class: 'btn btn-close btn-sm cancelar-publicacion', 'aria-label': 'Cerrar' })));
      $campos.empty().append($header, $box);
      $box.append($('<div>', { class: 'row' }).append(input('Tipo', 'tipo_otra_pub', 'text', 50), input('Descripción', 'descripcion', 'textarea', 300)), action('Guardar cambios', 'btn btn-dark guardar-otra-edicion'));
      $('#tipo_otra_pub').val(data.tipo); $('#descripcion').val(data.descripcion);
    }).fail(function (xhr) { notify(failure(xhr)); });
  }

  $(document)
    .off('click.publicacion', '#btn_publicacion').on('click.publicacion', '#btn_publicacion', function () { $('#boton_publicacion').hide(); const $card = $('<div>', { id: 'ingresar_publicacion', class: 'card mb-3' }).append($('<div>', { class: 'card-body' }).append($('<div>',{class:'row justify-content-between'}).append($('<div>',{class:'col-auto mb-3'}).append($('<h4>',{class:'card-title'}).text('Publicación')),$('<div>',{class:'col-auto'}).append($('<button>',{type:'button',id:'borrar_publicacion',class:'btn btn-close btn-sm borrar_publicacion','aria-label':'Cerrar'}))), select('Tipo de Publicación', 'pub', [[1, 'Artículo'], [2, 'Edición de Revista Temática'], [3, 'Libro'], [4, 'Otra Publicación']]), $('<div>', { id: 'publicacion_fields' }), $('<div>', { id: 'mnsj_pub', class: 'alert alert-danger' }).hide(), $('<button>', { type: 'submit', id: 'guardar_pub', class: 'btn btn-dark' }).text('Guardar').hide())); $('#publicacion').empty().append($card); })
    .off('change.publicacion', '#pub').on('change.publicacion', '#pub', function () { const type = Number($(this).val()); editorialesExistentes = []; editorialesNuevas = []; const $root = $('#publicacion_fields').empty(); if (type === 4) $root.append($('<div>', { class: 'row' }).append(input('Tipo', 'tipo_otra_pub', 'text', 50), input('Descripción', 'descripcion', 'textarea', 300))); else if (type) fields($root, type); $('#guardar_pub').toggle(!!type); })
    .off('click.publicacion', '.borrar_publicacion').on('click.publicacion', '.borrar_publicacion', function () { $('#ingresar_publicacion').remove(); $('#boton_publicacion').show(); })
    .off('click.publicacion', '.cancelar-publicacion').on('click.publicacion', '.cancelar-publicacion', function () { restaurarListadoPublicacion(); })
    .off('input.publicacion', '#editorial').on('input.publicacion', '#editorial', function () { const q = $.trim($(this).val()); const $list = $('#list_editorial').empty().hide(); if (!q) return; request({ op: 'editorial_search', busqueda: q }).then(function (r) { (r.data || []).forEach(function (p) { $list.append($('<li>', { class: 'list-group-item listEdit' }).text(p.nombre).data({ id: Number(p.id_editorial), nombre: value(p.nombre) })); }); $list.toggle($list.children().length > 0); }); })
    .off('click.publicacion', '.listEdit').on('click.publicacion', '.listEdit', function () { const p = $(this).data(); if (!editorialesExistentes.some(function (x) { return x.id === p.id; })) editorialesExistentes.push(p); $('#editorial').val(''); $('#list_editorial').hide(); renderEditoriales(); })
    .off('click.publicacion', '.btn_editorial').on('click.publicacion', '.btn_editorial', function () { const p = $.trim($('#editorial').val()); if (!p || p.length > 60) return notify('Ingrese una editorial válida.'); if (!editorialesNuevas.includes(p) && !editorialesExistentes.some(function (x) { return x.nombre === p; })) editorialesNuevas.push(p); $('#editorial').val(''); renderEditoriales(); })
    .off('click.publicacion', '.quitar-editorial').on('click.publicacion', '.quitar-editorial', function () { const p = $(this).data(); if (p.kind === 'old') editorialesExistentes = editorialesExistentes.filter(function (x) { return x.id !== p.id; }); else editorialesNuevas = editorialesNuevas.filter(function (x) { return x !== p.name; }); renderEditoriales(); })
    .off('change.publicacion', '#no_fac').on('change.publicacion', '#no_fac', syncFactorImpacto)
    .off('submit.publicacion', '#form_publicacion').on('submit.publicacion', '#form_publicacion', function (e) { e.preventDefault(); const type = Number($('#pub').val()); ensurePublicacionContext().then(function () { if (type === 4) { const data = Object.assign({ op: 'otra_create', tipo_otra_pub: $.trim($('#tipo_otra_pub').val()), descripcion: $.trim($('#descripcion').val()) }, payloadContextual()); if (!data.tipo_otra_pub || !data.descripcion) return notify('Complete los campos obligatorios.'); return request(data, true); } const data = Object.assign({ op: 'create', rol_participacion: $('#rol_participacion').val() }, formData(type), payloadContextual()); if (!valid(data) || !data.rol_participacion) return notify('Complete los campos obligatorios y seleccione el rol.'); return request(data, true); }).then(function (r) { if (!r) return; $('#ingresar_publicacion').remove(); $('#boton_publicacion').show(); window.cargarPub(usuarioContextual, '#publicacion_card'); }).fail(function (xhr) { notify(failure(xhr)); }); })
    .off('click.publicacion', '.eliminarPub').on('click.publicacion', '.eliminarPub', function () { if (!context.canManageGlobal || !confirm('¿Eliminar esta publicación?')) return; request({ op: 'delete', id_publicacion: $(this).data('id') }, true).then(function () { window.cargarPub(usuarioContextual, '#publicacion_card'); }).fail(function (x) { notify(failure(x)); }); })
    .off('click.publicacion', '.eliminarOtraPub').on('click.publicacion', '.eliminarOtraPub', function () { if (!context.canManageGlobal || !confirm('¿Eliminar esta publicación?')) return; request({ op: 'otra_delete', id_otra_pub: $(this).data('id') }, true).then(function () { window.cargarPub(usuarioContextual, '#publicacion_card'); }).fail(function (x) { notify(failure(x)); }); })
    .off('click.publicacion', '.editarPub').on('click.publicacion', '.editarPub', function () { const data = $(this).data(); openEdit(Number(data.id), Number(data.tipo)); })
    .off('click.publicacion', '.editarOtraPub').on('click.publicacion', '.editarOtraPub', function () { openOtraEdit(Number($(this).data('id'))); })
    .off('click.publicacion', '.guardar-otra-edicion').on('click.publicacion', '.guardar-otra-edicion', function () { const idOtraPub = Number($(this).closest('.otra-edit').data('id-otra-pub')); const data = { op: 'otra_update', id_otra_pub: idOtraPub, tipo_otra_pub: $.trim($('#tipo_otra_pub').val()), descripcion: $.trim($('#descripcion').val()) }; if (!data.tipo_otra_pub || !data.descripcion) return notify('Complete los campos obligatorios.'); request(data, true).then(restaurarListadoPublicacion).fail(function (xhr) { notify(failure(xhr)); }); });
  $(document)
    .off('change.autoria', '#pub').on('change.autoria', '#pub', function(){ if(Number($(this).val())&&Number($(this).val())!==4) $('#publicacion_fields').append(participantBlock()); })
    .off('change.autoria', '#rol_propio').on('change.autoria', '#rol_propio', actualizarEtiquetaContraria)
    .off('input.autoria', '.academic-search').on('input.autoria', '.academic-search', function(){ const $input=$(this),q=$.trim($input.val()),$box=$input.closest('.participant-block'),$list=$box.find('.academic-results').empty().hide(); $box.find('.academic-selected').text('').data('id',null); if(q.length<2)return; request({op:'academic_user_search',q:q}).then(function(r){(r.data||[]).forEach(function(u){$list.append($('<li>',{class:'list-group-item academic-option'}).text(u.nombre).data({id:Number(u.id_usuario),nombre:u.nombre}));});$list.toggle($list.children().length>0);}); })
    .off('click.autoria', '.academic-option').on('click.autoria', '.academic-option', function(){ const u=$(this).data(),$box=$(this).closest('.participant-block');$box.find('.academic-selected').text(u.nombre).data('id',u.id);$box.find('.academic-search').val(u.nombre);$box.find('.academic-results').empty().hide(); })
    .off('submit.publicacion', '#form_publicacion').on('submit.publicacion', '#form_publicacion', function(e){ e.preventDefault(); const type=Number($('#pub').val()); ensurePublicacionContext().then(function(){ if(type===4){const d=Object.assign({op:'otra_create',tipo_otra_pub:$.trim($('#tipo_otra_pub').val()),descripcion:$.trim($('#descripcion').val())},payloadContextual());if(!d.tipo_otra_pub||!d.descripcion){notify('Complete los campos obligatorios.');return;}return request(d,true);}const p=participantsData();if(p.error){notify(p.error);return;}const d=Object.assign({op:'create'},formData(type),p.data,payloadContextual());if(!valid(d)){notify('Complete los campos obligatorios.');return;}return request(d,true);}).then(function(r){if(!r)return;$('#ingresar_publicacion').remove();$('#boton_publicacion').show();window.cargarPub(usuarioContextual,'#publicacion_card');}).fail(function(x){notify(failure(x));}); });
  $(document).off('click.publicacion', '.guardar-edicion').on('click.publicacion', '.guardar-edicion', function(){ const $box=$(this).closest('.publicacion-edit'),type=Number($box.data('tipo')),p=participantsData(); if(p.error)return notify(p.error); const data=Object.assign({op:'update',id_publicacion:Number($box.data('id-publicacion')),subject_usuario_id:usuarioContextual},formData(type),p.data); if(!valid(data))return notify('Complete los campos obligatorios.'); request(data,true).then(restaurarListadoPublicacion).fail(function(x){notify(failure(x));}); });
}());
