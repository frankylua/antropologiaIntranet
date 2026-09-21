/* OA Tesis: autorización y validación autoritativa en ajax/tesis.php. */ (() => {
  'use strict';
  let ctx, token, editing = null;
  const $ = window.jQuery;
  if (!$) return;
  const root = () => $('#info_est').length ? $('#ficha_tesis_est') : $('#form_tesis');
  const listRoot = () => {
    const card = root().find('#tesis_card');
    return card.length ? card : root();
  };
  const subject = () => {
    const docente = $('#info_doc').attr('name'), estudiante = $('#info_est').attr('name');
    return docente ? {
      subject_docente_id: docente
    }
    : estudiante ? {
      subject_usuario_id: estudiante
    }
    : {
    };
  };
  const call = (op, data = {
  }, write = false) => $.ajax({
    url: '../ajax/tesis.php', type: 'POST', data: {
      op, ...subject(), ...data
    }, headers: write ? {
      'X-CSRF-Token': token
    }
    : {
    }
  }).then(r => typeof r === 'string' ? JSON.parse(r) : r);
  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }
  [c]));
  const input = (label, name, type = 'text') => `<div class="col-md-6 mb-2"><label class="form-label">${label}</label><input class="form-control" type="${type}" name="${name}"></div>`;
  const select = (label, name, options = '') => `<div class="col-md-6 mb-2"><label class="form-label">${label}</label><select class="form-select" name="${name}">${options}</select></div>`;
  function clearMessage() {
    root().find('.tesis-message').remove();
  }
  function show(error) {
    clearMessage();
    const message = `<div class="alert alert-danger tesis-message">${esc(error?.responseJSON?.mensaje || error?.mensaje || 'No fue posible completar la operación.')}</div>`;
    const editor = root().find('.oa-editor:visible .card-body').first();
    (editor.length ? editor : listRoot()).prepend(message);
  }
  function cards(rows) {
    clearMessage();
    const canManage = !!ctx.canCreate;
    const text = value => value === null || value === undefined || String(value).trim() === '' ? '-' : String(value);
    const date = value => {
      const raw = text(value);
      return raw !== '-' && /^\d{4}-\d{2}-\d{2}$/.test(raw) ? raw.split('-').reverse().join('-') : raw;
    };
    const place = value => String(value) === '1' ? 'Realizada en el programa' : String(value) === '2' ? 'Realizada fuera del programa' : text(value);
    const degree = value => String(value) === '1' ? 'Magíster' : String(value) === '2' ? 'Doctorado' : text(value);
    const row = (label, value) => `<tr><td>${label}</td><td>${esc(text(value))}</td></tr>`;
    const html = rows.length ? rows.map(t => {
      const canUpdate = !!t.canUpdate;
      const canDelete = !!t.canDelete;
      const actions = `${canUpdate ? `<button class="col-auto btn btn-link link-success ps-1 tesis-edit" data-id="${t.id_tesis}">Editar</button>` : ''}${canDelete ? `<button class="col-auto btn btn-link link-danger ps-1 tesis-delete" data-id="${t.id_tesis}">Eliminar</button>` : ''}`;
      const thesisRows = [ ['Título', t.tit_tesis], ['Lugar', place(t.lugar)], ['Grado', degree(t.grado)], ['Año', t.anio], ['Fecha aprobación', date(t.fecha_aprob)], ['Fecha defensa', date(t.fecha_def)], ['Panel evaluador', t.panel_eval], ['Estudiante tesista', t.estudiante_interno || t.nom_est_tes], ['Guía', t.guia_interno || t.nom_prof_guia], ['Coguía', t.coguia_interno || t.nom_prof_coguia], ['Institución', t.inst_tesis_nombre], ['País', t.pais_tesis_nombre] ].map(([label, value]) => row(label, value)).join('');
      const cotutelaRows = t.id_cotutela ? `<tr><td colspan="2"><h5>Cotutela</h5></td></tr>${[
        ['Guía de Cotutela', t.cot_guia_nombre || t.cot_guia_externo], ['Institución', t.inst_cot_nombre],
        ['Fondo', t.fondo], ['Ciudad', t.ciudad], ['País', t.pais_cot_nombre]
      ].map(([label, value]) => row(label, value)).join('')}` : '';
      return `<div class="card mb-3 card_pub"><div class="card-body"><table class="table table-striped"><thead><tr><th class="col-md-4 titulo_acad"><h5>Tesis</h5></th><th class="row justify-content-end ps-0 botones">${actions}</th></tr></thead><tbody>${thesisRows}${cotutelaRows}</tbody></table></div></div>`;
    }).join('') : '<p class="text-muted">No hay tesis registradas.</p>';
    listRoot().html(`<div class="oa-list">${html}</div>`);
    $('#boton_tesis').toggle(canManage);
  }
  function load() {
    const s = subject();
    if (!s.subject_docente_id && !s.subject_usuario_id) return;
    listRoot().show();
    call('list').then(r => cards(r.datos || [])).catch(show);
  }
  function hybrid(label, key, type, placeholder, classes = 'col-md-6 mb-2') {
    return `<div class="${classes} tesis-hybrid"><label class="form-label" for="${key}_texto">${label}</label><input class="form-control tesis-hybrid-text" id="${key}_texto" name="${key}_texto" autocomplete="off" data-key="${key}" data-type="${type}" placeholder="${placeholder}"><input type="hidden" name="${key}_usuario_id"><div class="small mt-1 tesis-person-kind"></div><ul class="list-group tesis-results"></ul></div>`;
  }
  function cotPerson(label, key) {
    return hybrid(label, key, 'profesor', 'Buscar docente o ingresar nombre externo');
  }
  function direction() {
    return `<fieldset class="col-12 mb-2 border rounded p-3 tesis-direction"><legend class="float-none w-auto px-2 fs-6">Rol Docente</legend><div class="row">${select('Rol en la Tesis', 'rol_docente', '<option value="">Seleccione</option><option value="guia">Guía</option><option value="coguia">Coguía</option>')}${hybrid('Participante contrario', 'contrario', 'profesor', 'Buscar docente o ingresar nombre externo', 'col-md-6 mb-2 tesis-opponent-wrap d-none')}</div></fieldset>`;
  }
  function catalogs() {
    ajaxSelect('[name=inst_tesis]', '../ajax/institucion.php', 'Seleccione', 'read', undefined, 'id_inst', 'inst');
    ajaxSelect('[name=inst_cot]', '../ajax/institucion.php', 'Seleccione', 'read', undefined, 'id_inst', 'inst');
    $('[name="inst_tesis"],[name="inst_cot"]').each(function () {
      $(this).append('<option value="nueva">Otra institución</option>');
    });
    ajaxSelect('[name=pais_tesis]', '../ajax/pais.php', 'Seleccione', 'pais', undefined, 'id_pais', 'pais');
    ajaxSelect('[name=pais_cot]', '../ajax/pais.php', 'Seleccione', 'pais', undefined, 'id_pais', 'pais');
  }
  function syncDirectionLabel() {
    const role = $('[name=rol_docente]').val(), wrap = $('.tesis-opponent-wrap');
    wrap.toggleClass('d-none', !role);
    wrap.find('label').text(role === 'guia' ? 'Coguía' : role === 'coguia' ? 'Guía' : 'Participante contrario');
  }
  function hydrateHybrid(key, id, text, internal) {
    const box = $(`.tesis-hybrid-text[data-key="${key}"]`).closest('.tesis-hybrid');
    box.find(`[name="${key}_usuario_id"]`).val(id || '');
    box.find(`[name="${key}_texto"]`).val(text || '');
    box.find('.tesis-person-kind').text(internal ? 'Interno' : text ? 'Externo' : '');
  }
  function hydrateDirection(row) {
    const subjectId = String(subject().subject_docente_id || '');
    const subjectGuide = subjectId && subjectId === String(row.id_prof_guia || '');
    const role = subjectGuide ? 'guia' : 'coguia';
    const internal = subjectGuide ? row.id_prof_coguia : row.id_prof_guia;
    const external = subjectGuide ? row.nom_prof_coguia : row.nom_prof_guia;
    const name = subjectGuide ? row.coguia_interno : row.guia_interno;
    $('[name=rol_docente]').val(role);
    syncDirectionLabel();
    hydrateHybrid('contrario', internal, name || external, !!internal);
  }
  function editor(row = {
  }) {
    editing = row.id_tesis || null;
    $('#boton_tesis').hide();
    root().find('#tesis_card').hide();
    root().find('#tesis').html(`<section class="oa-editor card"><div class="card-body"><button type="button" class="btn-close float-end tesis-close"></button><h5>${editing ? 'Editar' : 'Agregar'} Tesis</h5><form id="tesis-oa-form"><div class="row">${input('Título', 'titulo')}${select('Lugar', 'lugar', '<option value="">Seleccione</option><option value="1">Realizada en el programa</option><option value="2">Realizada fuera del programa</option>')}${select('Grado', 'grado', '<option value="">Seleccione</option><option value="1">Magíster</option><option value="2">Doctorado</option>')}${input('Año', 'anio', 'number')}${select('Institución donde se realiza la Tesis', 'inst_tesis')}<div class="col-md-6 mb-2 d-none tesis-nueva-institucion" data-for="inst_tesis"><label class="form-label">Nueva institución de Tesis</label><input class="form-control" name="nueva_inst_tesis"></div>${select('País Tesis', 'pais_tesis')}${input('Fecha aprobación', 'fecha_aprob', 'date')}${input('Fecha defensa', 'fecha_def', 'date')}${input('Panel evaluador', 'panel')}${hybrid('Estudiante tesista', 'estudiante', 'estudiante', 'Buscar estudiante o ingresar nombre externo')}${direction()}<div class="col-12 form-check mb-2"><input class="form-check-input" type="checkbox" id="cot-act"><label class="form-check-label" for="cot-act">Cotutela</label></div><div id="cot-fields" class="row d-none">${cotPerson('Guía Cotutela', 'cot_guia')}${select('Institución Cotutela', 'inst_cot')}<div class="col-md-6 mb-2 d-none tesis-nueva-institucion" data-for="inst_cot"><label class="form-label">Nueva institución de Cotutela</label><input class="form-control" name="nueva_inst_cot"></div>${input('Fondo', 'fondo')}${input('Ciudad', 'ciudad_cot')}${select('País Cotutela', 'pais_cot')}</div></div><button class="btn btn-dark" type="submit">Guardar</button></form></div></section>`);
    catalogs();
    const form = $('#tesis-oa-form');
    const values = {
      titulo: row.tit_tesis, lugar: row.lugar, grado: row.grado, anio: row.anio, inst_tesis: row.inst_tesis, pais_tesis: row.pais_tesis, fecha_aprob: row.fecha_aprob, fecha_def: row.fecha_def, panel: row.panel_eval, inst_cot: row.inst_cot, fondo: row.fondo, ciudad_cot: row.ciudad, pais_cot: row.pais_cot, cot_guia_usuario_id: row.cot_guia_interno, cot_guia_nombre_externo: row.cot_guia_externo
    };
    Object.entries(values).forEach(([key, value]) => form.find(`[name="${key}"]`).val(value ?? ''));
    hydrateHybrid('estudiante', row.id_est_tes, row.estudiante_interno || row.nom_est_tes, !!row.id_est_tes);
    hydrateHybrid('cot_guia', row.cot_guia_interno, row.cot_guia_nombre || row.cot_guia_externo, !!row.cot_guia_interno);
    if (editing) hydrateDirection(row);
    if (row.id_cotutela) {
      $('#cot-act').prop('checked', true);
      $('#cot-fields').removeClass('d-none');
    }
    root()[0]?.scrollIntoView({
      behavior: 'smooth', block: 'start'
    });
  }
  function participant(box, key) {
    return {
      id: box.find(`[name="${key}_usuario_id"]`).val(), text: $.trim(box.find(`[name="${key}_texto"]`).val())
    };
  }
  function directionPayload(form) {
    const role = form.find('[name=rol_docente]').val(), subjectId = String(subject().subject_docente_id || ''), opponent = participant(form.find('.tesis-opponent-wrap'), 'contrario');
    if (!subjectId) return {
      error: 'No se pudo determinar el Docente de la ficha.'
    };
    if (!role || (!opponent.id && !opponent.text)) return {
      error: 'Seleccione el rol del Docente y su participante contrario.'
    };
    if (opponent.id && String(opponent.id) === subjectId) return {
      error: 'Guía y Coguía internos deben ser personas distintas.'
    };
    const data = role === 'guia' ? {
      guia_usuario_id: subjectId, guia_nombre_externo: '', coguia_usuario_id: opponent.id, coguia_nombre_externo: opponent.id ? '' : opponent.text
    }
    : {
      guia_usuario_id: opponent.id, guia_nombre_externo: opponent.id ? '' : opponent.text, coguia_usuario_id: subjectId, coguia_nombre_externo: ''
    };
    return {
      data
    };
  }
  $(document).on('input', '.tesis-hybrid-text', function () {
    const input = $(this), box = input.closest('.tesis-hybrid'), key = input.data('key'), term = input.val().trim();
    box.find(`[name="${key}_usuario_id"]`).val('');
    box.find('.tesis-person-kind').text(term ? 'Externo' : '');
    if (term.length < 2) return box.find('.tesis-results').empty();
    call('lookup', {
      tipo: input.data('type'), termino: term
    }).then(r => box.find('.tesis-results').html((r.datos || []).map(x => `<li class="list-group-item tesis-hybrid-result" data-id="${x.id_usuario}" data-key="${key}">${esc(x.nombre)}</li>`).join(''))).catch(() => box.find('.tesis-results').empty());
  });
  $(document).on('click', '.tesis-hybrid-result', function () {
    const item = $(this), box = item.closest('.tesis-hybrid'), key = item.data('key');
    box.find(`[name="${key}_texto"]`).val(item.text());
    box.find(`[name="${key}_usuario_id"]`).val(item.data('id'));
    box.find('.tesis-person-kind').text('Interno');
    box.find('.tesis-results').empty();
  });
  $(document).on('input', '.tesis-person-query', function () {
    const input = $(this), box = input.closest('.tesis-person'), key = input.data('key'), term = input.val().trim();
    box.find(`[name="${key}_usuario_id"]`).val('');
    if (term.length < 2) return box.find('.tesis-results').empty();
    call('lookup', {
      tipo: input.data('type'), termino: term
    }).then(r => box.find('.tesis-results').html((r.datos || []).map(x => `<li class="list-group-item tesis-person-result" data-id="${x.id_usuario}" data-key="${key}">${esc(x.nombre)}</li>`).join(''))).catch(() => box.find('.tesis-results').empty());
  });
  $(document).on('click', '.tesis-person-result', function () {
    const item = $(this), box = item.closest('.tesis-person'), key = item.data('key');
    box.find('.tesis-person-query').val(item.text());
    box.find(`[name="${key}_usuario_id"]`).val(item.data('id'));
    box.find(`[name="${key}_nombre_externo"]`).val('');
    box.find('.tesis-results').empty();
  });
  $(document).on('input', '.tesis-external', function () {
    const box = $(this).closest('.tesis-person'), key = $(this).data('key');
    if ($(this).val().trim()) {
      box.find(`[name="${key}_usuario_id"]`).val('');
      box.find('.tesis-person-query').val('');
      box.find('.tesis-results').empty();
    }
  });
  $(document).on('change', '[name=rol_docente]', syncDirectionLabel);
  $(document).on('change', '[name="inst_tesis"],[name="inst_cot"]', function () {
    const box = $(`.tesis-nueva-institucion[data-for="${this.name}"]`), isNew = this.value === 'nueva';
    box.toggleClass('d-none', !isNew);
    if (!isNew) box.find('input').val('');
  });
  $(document).on('click', '#btn_tesis', () => editor());
  $(document).on('click', '.tesis-close', () => {
    $('#tesis').empty();
    $('#boton_tesis').show();
    root().find('#tesis_card').show();
  });
  $(document).on('change', '#cot-act', function () {
    $('#cot-fields').toggleClass('d-none', !this.checked);
  });
  $(document).on('click', '.tesis-edit', function () {
    call('detail', {
      id_tesis: this.dataset.id
    }).then(r => editor(r.datos)).catch(show);
  });
  $(document).on('click', '.tesis-delete', function () {
    if (confirm('¿Eliminar esta Tesis?')) call('delete', {
      id_tesis: this.dataset.id
    }, true).then(load).catch(show);
  });
  $(document).on('submit', '#tesis-oa-form', function (event) {
    event.preventDefault();
    const directionData = directionPayload($(this));
    if (directionData.error) return show({
      mensaje: directionData.error
    });
    const student = participant($(this), 'estudiante');
    if (!student.id && !student.text) return show({
      mensaje: 'Seleccione o ingrese un estudiante tesista.'
    });
    const cotGuide = participant($(this), 'cot_guia');
    if ($('#cot-act').prop('checked') && !cotGuide.id && !cotGuide.text) return show({
      mensaje: 'Ingrese una Guía de Cotutela.'
    });
    const data = Object.fromEntries(new FormData(this));
    data.estudiante_usuario_id = student.id;
    data.estudiante_nombre_externo = student.id ? '' : student.text;
    data.cot_guia_usuario_id = cotGuide.id;
    data.cot_guia_nombre_externo = cotGuide.id ? '' : cotGuide.text;
    Object.assign(data, directionData.data);
    data.cotutela_activa = $('#cot-act').prop('checked') ? 'true' : 'false';
    if (editing) data.id_tesis = editing;
    call(editing ? 'update' : 'create', data, true).then(() => {
      $('#tesis').empty();
      root().find('#tesis_card').show();
      load();
    }).catch(show);
  });
  function loadContext() {
    if (!Object.keys(subject()).length) return;
    ctx = null;
    clearMessage();
    call('context').then(r => {
      ctx = r.datos;
      token = ctx.csrf_token;
      load();
    }).catch(show);
  }
  $(function () {
    if (!$('#info_est').length) loadContext();
  });
  $(document).on('tesis:subject-ready', loadContext);
})();
