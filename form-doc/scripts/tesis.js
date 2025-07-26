//Agregar Tesis
anios('#anio_ing', 2006)
let cotutela
function agregarCotutela() {
  $("#btn_cotutela").prop('disabled', true)
  $('#cotutela').append('<div class="card mb-3" id="ingresar_cotutela"><div class="card-body" id="card_cotutela"></div></div>')
  $('#card_cotutela').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Cotutela</h4></div><div class="col-auto"><button type="button" name="add" id="borrar_cotutela"  class="btn btn-close btn-sm borrar_cotutela"></button></div></div>');
  $('#card_cotutela').append('<div class="row" id="row_inst_cot"><div class="col-md-6 mb-3"><label for="prof_gu" class="form-label">Profesor Guía</label><input type="text" class="form-control" id="prof_gu" maxlength="45"></div><div class="col-md-6 mb-3" id="id_inst_cot" name="0"><label for="inst_cot" class="form-label">Institución</label><select id="inst_cot" class="form-select"></select></div></div>')
  $('#card_cotutela').append('<div class="row"><div class="col-md-6 mb-3"><label for="fondo" class="form-label">Fondo</label><input type="text" class="form-control" id="fondo" maxlength="45"></div><div class="col-md-6 mb-3"><label for="ciudad_cot" class="form-label">Ciudad</label><input type="text" class="form-control" id="ciudad_cot" maxlength="45"></div></div>')
  $('#card_cotutela').append('<div class="row" id="row_pais"><label for="pais_cot" class="form-label">País</label><div class="col-md-6 mb-3"><select id="pais_cot" class="form-select"></select></div></div>')
  $('#card_cotutela').append('<div class="row></div>')
  ajaxSelect('#pais_cot', '../ajax/pais.php', 'Seleccione', 'pais');
  ajaxSelect('#inst_cot', '../ajax/institucion.php', 'Seleccione', 'read');

}
function cargarTesis(usu, id) {
  op = 'read'
  $.ajax({
    async: false,
    type: 'POST',
    url: '../ajax/tesis.php',
    data: { op, usu },
    success: function (response) {
      // let tesis = JSON.parse(response);
      // console.log(tesis)
      // let tabla = '';
      // let con = 1
      // let conCot = 1
      // tesis.forEach(t => {
      //   rol = t['id_prof_guia'] == usu ? 'Profesor/a Guía' : t['id_prof_coguia'] == usu ? 'Profesor/a Coguía' : ''
      //   tabla += `
      //   <div class="card mb-3 grado_card">
      //   <div class="card-body">
      //   <table class="table table-striped">
      //   <thead>
      //   <tr>
      //   <th class="col-md-5 titulo_acad"><h5>TESIS</h5></th>
      //   <th class="ps-0"><button type="button" class="btn btn-link ps-1 link-secondary" id="${t['id_tesis']}">Editar Tesis</button></th>
      //   </tr>
      //   </thead>
      //   <tbody>
      //   <tr>
      //   <td>Lugar</td>
      //   <td>${t['lugar']==1?'Realizada en el programa':'Realizada fuera del programa'}</td>
      //   </tr>
      //   <tr>
      //   <td>Título Tesis</td>
      //   <td>${cadenaMay(t['tit_tesis'])}</td>
      //   </tr>
      //   <tr>
      //   <td>Grado</td>
      //   <td>${t['grado'] == 1 ? 'Magíster' : 'Doctorado'}</td>
      //   </tr>
      //   <tr>
      //   <td>Año</td>
      //   <td>${t['anio']}</td>
      //   </tr>
      //   <td>Institución</td>
      //   <td>${t['it']}</td>
      //   </tr> 
      //   <td>País</td>
      //   <td>${t['pt']}</td>
      //   </tr>  
      //   <tr>
      //   <td>Fecha Aprobación</td>
      //   <td>${mostrarFecha(t['fecha_aprob'])}</td>
      //   </tr>
      //   <tr>
      //   <td>Fecha Defensa</td>
      //   <td>${mostrarFecha(t['fecha_def'])}</td>
      //   </tr>
      //   <tr>
      //   <td>Panel de Evaluación</td>
      //   <td>${cadenaMay(t['panel_eval'])}</td>
      //   </tr>

      // `

      //   if (t['id_prof_guia'] == usu) {
      //     op = 'read-usu'
      //     usuario = t['id_prof_coguia']
      //     rol = 'Profesor Guía'
      //     $.ajax({
      //       async: false,
      //       type: 'POST',
      //       url: '../ajax/tesis.php',
      //       data: { op, usu:usuario },
      //       success: function (response) {
      //         let prof = JSON.parse(response);
      //         if (t['id_prof_coguia'] == null) {
      //           prof = cadenaMay(t['nom_prof_coguia'])
      //         } else {
      //           nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
      //           prof = nombre
      //         }

      //         tabla += `
      //         <tr>
      //           <td>Rol</td>
      //           <td>Profesor/a Guía</td>
      //           </tr>
      //           <tr>
      //           <td>Profesor/a Coguía</td>
      //           <td>${prof}</td>
      //           </tr>`
      //       }
      //     })
      //   }
      //   if (t['id_prof_coguia'] == usu) {
      //     op = 'read-usu'
      //     usuario = t['id_prof_guia']
      //     rol = 'Profesor Guía'
      //     $.ajax({
      //       async: false,
      //       type: 'POST',
      //       url: '../ajax/tesis.php',
      //       data: { op, usu:usuario },
      //       success: function (response) {
      //         let autor = JSON.parse(response);
      //         if (t['id_prof_guia'] == null) {
      //           prof = cadenaMay(t['nom_prof_guia'])
      //         } else {
      //           nombre = cadenaMay(autor[0]['nombres']) + ' ' + cadenaMay(autor[0]['ap_pat']) + ' ' + cadenaMay(autor[0]['ap_mat'])
      //           prof = nombre
      //         }

      //         tabla += `
      //         <tr>
      //           <td>Rol</td>
      //           <td>Profesor/a Coguía</td>
      //           </tr>
      //           <tr>
      //         <td>Profesor/a Guía</td>
      //         <td>${prof}</td>
      //         </tr>`
      //       }
      //     })
      //   }
      //   // agregar cotutela
      //   $.ajax({
      //     async: false,
      //     type: 'POST',
      //     url: '../ajax/tesis.php',
      //     data: { op: 'read-cotutela', id_tesis: t['id_tesis'] },
      //     success: function (response) {
      //       let cot = JSON.parse(response);
      //       console.log(cot)
      //       if (cot.length > 0) {
      //         tabla += `<tr>
      //     <td><h5>Cotutela</h5></td>
      //     <td></td>
      //     </tr>
      //     <tr>
      //     <td>Profesor/a Guía</td>
      //     <td>${cadenaMay(cot[0]['prof_guia'])}</td>
      //     </tr>
      //     <tr>
      //     <td>Institución Cotutela</td>
      //     <td>${cadenaMay(cot[0]['ic'])}</td>
      //     </tr>
      //     <tr>
      //     <td>Ciudad Cotutela</td>
      //     <td>${cadenaMay(cot[0]['ciudad'])}</td>
      //     </tr>
      //     <tr>
      //     <td>País Cotutela</td>
      //     <td>${cadenaMay(cot[0]['pc'])}</td>
      //     </tr>
      //     <tr>
      //     <td>Fondo</td>
      //     <td>${cadenaMay(cot[0]['fondo'])}</td>
      //     </tr>        
      //     `

      //       }

      //     }
      //   })
      //   con++;
      //   tabla += `</tbody>
      //   </table>    
      //   </div>
      //   </div>`
      // });
      // $(id).append(tabla);
    }
  })
}
$('#btn_tesis').click(function () {
  $('#boton_tesis').fadeOut();
  cotutela = false
  $('#tesis').append('<div class="card mb-3" id="ingresar_tesis"><div class="card-body" id="card_tesis"> </div></div>');
  $('#card_tesis').append('<div class="row justify-content-between"><div class="col-auto mb-3"><h4 class="card-title">Tesis</h4></div><div class="col-auto"><button type="button" name="add" id=""  class="btn btn-close btn-sm borrar_tesis"></button></div></div>');
  $('#card_tesis').append('<div class="row "><div class="col-md-6 mb-3"><select id="lugar_tesis" class="form-select"><option selected value="0">Seleccione</option><option value="1">Realizada en el programa</option><option value="2">Realizada fuera del programa</option></select></div><div class="col-md-6 mb-3"><select id="grad_tesis" class="form-select"><option selected value="0">Grado Tesis</option><option value="1">Magíster</option><option value="2">Doctorado</option></select></div></div>');
  $('#card_tesis').append('<div class="row"><div class="col mb-3"><label for="tit_tesis" class="form-label">Título</label><textarea row="1" type="text" class="form-control" id="tit_tesis" name="0" autocomplete="off" maxlength="400"></textarea><ul class="list-group" id="list_titulos"></ul></div></div>');
  $('#card_tesis').append('<div class="row" id="rol_te"><div class="col-md-6 mb-3"><label for="guia_tesis" class="form-label">Rol</label><select id="rol_tesis" class="form-select"><option selected value="0">Seleccione</option><option value="1">Guía</option><option value="2">Coguía</option></select></div></div>');
  $('#card_tesis').append('<div class="row " id="pa_t"><div class="col-md-6 mb-3"><label for="pais_tesis" class="form-label">País</label><select id="pais_tesis" class="form-select"></select></div><div class="col-md-6 mb-3"><label for="nom_est" class="form-label">Nombre de estudiante tesista</label><input type="text" class="form-control" id="nom_est" name="0" maxlength="60"><ul class="list-group" id="list_est"></ul></div></div>');
  $('#card_tesis').append('<div class="row" id="row_inst_tesis"><div class="col-md-6 mb-3" id="id_inst_tesis" name="0"><label for="inst_tesis" class="form-label">Institución</label><select id="inst_tesis" class="form-select"></select></div><div class="col-md-6 mb-3"><div class="row"><div class="col-md-6"><label for="fecha_aprob" class="form-label">Fecha de aprobación</label><input type="date" class="form-control" id="fecha_aprob"></div><div class="col-md-6"><label for="fecha_def" class="form-label">Fecha defensa</label><input type="date" class="form-control" id="fecha_def"></div></div></div><div class="col-md-6 mb-3"><label for="anio_tesis" class="form-label">Año</label><select id="anio_tesis" class="form-select"></select></div></div>');
  $('#card_tesis').append('<div class="row"><div class="col mb-3"><label for="pan_ev" class="form-label">Panel evaluador(Ingresar nombres separados por una coma)</label><input type="text" class="form-control" id="pan_ev" maxlength="200"></div></div>');
  $('#card_tesis').append('<div class="row"><div class="col mb-3" id="btn_cot"><button class="btn btn-link link-dark btn_cotutela" id="btn_cotutela" type="button">Agregar Cotutela</button></div></div><div class="row" id="row_cot"><div class="col mb-3" id="cotutela"></div></div>');
  $('#card_tesis').append(' <div class="row justify-content-center " id="mnsj_row_tesis"><div class="col-lg-8 alert  text-center alert-danger" role="alert" id="mnsj_tesis"></div></div>')
  $('#card_tesis').append('<div class="row justify-content-center"><div class="col-md-6 mt-3 d-grid gap-2"><button type="submit" class="btn btn-dark">Guardar Tesis</button></div></div>');
  $('#mnsj_row_tesis').hide();
  ajaxSelect('#pais_tesis', ruta + 'ajax/pais.php', 'Seleccione', 'pais');
  ajaxSelect('#inst_tesis', ruta + 'ajax/institucion.php', 'Seleccione', 'read');
  anios('#anio_tesis', 2006)
  $('#nom_est').keyup(function () {
    //$('#list_est').hide();

    busqueda = $('#nom_est').val();
    if (busqueda !== '') {
      $.ajax({
        url: '../ajax/tesis.php',
        type: 'POST',
        data: { op: 'read_est', busqueda },
        success: function (response) {
          let alumnos = JSON.parse(response);
          let template = '';
          //$('#prof_guia').attr('name','');
          $('#nom_est').attr('name', '');
          alumnos.forEach(list => {
            est = list[1] + ' ' + list[2] + ' ' + list[3];
            est = cadenaMay(est);
            template += `
      
      <li class='list-group-item listEst' id='${list[0]}' name='${est}'> ${est}</li>`
          });

          $('#list_est').html(template);
          $('#list_est').show();
        }
      })

    }
  })
  $('#tit_tesis').keyup(function () {
    $('#list_titulos').hide();
    busqueda = $('#tit_tesis').val();
    if (busqueda !== '') {
      $.ajax({
        url: '../ajax/tesis.php',
        type: 'POST',
        data: { op: 'read_tesis', busqueda },
        success: function (response) {
          let proyecto = JSON.parse(response);
          let template = "";
          //$('#prof_guia').attr('name','');
          if (proyecto.length > 0) {
            proyecto.forEach(list => {
              template += `
                <li class='list-group-item listTes' id='${list[0]}' name="${list[1]}"> ${cadenaMay(list[1])}</li>`
            });
            $('#list_titulos').html(template);
            $('#list_titulos').show();

          }
        }
      })

    }
  })
})
$('body').on('click', '.listTes', function () {
  id = $(this).attr('id');
  titulo = $(this).attr('name');
  $('#tit_tesis').attr('name', id);
  $('#tit_tesis').val(cadenaMay(titulo));
  $('#list_titulos').hide();
  if ($('#tit_tesis').attr('name') !== 0) {
    $.ajax({
      async: false,
      url: '../ajax/tesis.php',
      type: 'POST',
      data: { op: 'carg_tesis', id },
      success: function (response) {
        let tesis = JSON.parse(response);
        cotutela = tesis[0]['id_cotutela'] != undefined;
        console.log(cotutela)
        if (tesis[0]['id_prof_guia'] == null || tesis[0]['id_prof_coguia'] == null) {
          $('#tit_tesis').prop('disabled', true);
          $('#lugar_tesis').val(cadenaMay(tesis[0]['lugar']));
          $('#lugar_tesis').prop('disabled', true);
          $('#grad_tesis').val(tesis[0]['grado']);
          $('#grad_tesis').prop('disabled', true);
          $('#anio_tesis').val(tesis[0]['anio']);
          $('#anio_tesis').prop('disabled', true);
          $('#inst_tesis').val(tesis[0]['inst_tesis']);
          $('#inst_tesis').prop('disabled', true);
          $('#pais_tesis').val(tesis[0]['pais_tesis']);
          $('#pais_tesis').prop('disabled', true);
          $('#fecha_aprob').val(tesis[0]['fecha_aprob']);
          $('#fecha_aprob').prop('disabled', true);
          $('#fecha_def').val(tesis[0]['fecha_def']);
          $('#fecha_def').prop('disabled', true);
          $('#pan_ev').val(cadenaMay(tesis[0]['panel_eval']));
          $('#pan_ev').prop('disabled', true);
          if (tesis[0]['id_est_tes'] == null) {
            est = cadenaMay(tesis[0]['nom_est_tes'])
          } else {
            $.ajax({
              async: false,
              type: 'POST',
              url: '../ajax/tesis.php',
              data: { op: 'read-usu', usu: tesis[0]['id_est_tes'] },
              success: function (response) {
                let prof = JSON.parse(response);
                nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
                est = nombre
              }
            })
          }
          $('#nom_est').val(est);
          $('#nom_est').prop('disabled', true);
          if (tesis[0]['id_prof_guia'] == null) {
            $('#rol_tesis').val(1);
            $('#rol_tesis').prop('disabled', true);
            $('#rol_tesis').attr('name', tesis[0]['id_tesis']);//guardo el id de la tesis en el name del rol
            $('#rol_te').append('<div class="col-md-6 mb-3" id="guia_coguia"><label for="coguia" class="form-label" id="prof_rol">Profesor Coguía</label><input type="text" class="form-control" id="coguia"></div>')
            $.ajax({
              async: false,
              type: 'POST',
              url: '../ajax/tesis.php',
              data: { op: 'read-usu', usu: tesis[0]['id_prof_coguia'] },
              success: function (response) {
                let prof = JSON.parse(response);
                nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
                autor = nombre
                $('#coguia').val(autor);
              }
            })
            $('#coguia').prop('disabled', true);
          } else if (tesis[0]['id_prof_coguia'] == null) {
            $('#rol_tesis').val(2);
            $('#rol_tesis').prop('disabled', true);
            $('#rol_tesis').attr('name', tesis[0]['id_tesis']);//guardo el id de la tesis en el name del rol
            $('#rol_te').append('<div class="col-md-6 mb-3" id="guia_coguia"><label for="guia" class="form-label" id="prof_rol">Profesor Guía</label><input type="text" class="form-control" id="guia"></div>')
            $.ajax({
              async: false,
              type: 'POST',
              url: '../ajax/tesis.php',
              data: { op: 'read-usu', usu: tesis[0]['id_prof_guia'] },
              success: function (response) {
                let prof = JSON.parse(response);
                console.log(prof)
                nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
                autor = nombre
                $('#guia').val(autor);
              }
            })
            $('#guia').prop('disabled', true);

          }
          if (cotutela) {
            agregarCotutela()
            $('#borrar_cotutela').remove()
            $('#btn_cotutela').remove()
            $('#prof_gu').val(cadenaMay(tesis[0]['prof_guia']));
            $('#prof_gu').prop('disabled', true);
            $('#inst_cot').val(tesis[0]['inst_cot']);
            $('#inst_cot').prop('disabled', true);
            $('#fondo').val(cadenaMay(tesis[0]['fondo']));
            $('#fondo').prop('disabled', true);
            $('#ciudad_cot').val(cadenaMay(tesis[0]['ciudad']));
            $('#ciudad_cot').prop('disabled', true);
            $('#pais_cot').val(tesis[0]['pais_cot']);
            $('#pais_cot').prop('disabled', true);


          }

        } else {
          $('#list_titulos').show();
          $('#list_titulos').html("<li class='list-group-item text-danger'>Esta tesis ya se encuentra registrada<li>")

        }


      }
    })
  }

})
// Agregar campo guia/coguia
$(document).on('change', '#rol_tesis', function () {
  $('#guia_coguia').remove()
  titulo = $('#rol_tesis').val() == '1' ? 'Coguía' : 'Guía'
  if ($('#rol_tesis').val() != 0) {
    $('#rol_te').append('<div class="col-md-6 mb-3" id="guia_coguia"><label for="coguia" class="form-label" id="prof_rol">Profesor ' + titulo + '</label><input type="text" class="form-control" id="prof_tesis" name="0"><ul class="list-group" id="list_prof"></ul></div>')
  }
  $('#prof_tesis').keyup(function () {
    $('#list_prof').hide();
    busqueda = $('#prof_tesis').val();
    if (busqueda !== '') {
      $.ajax({
        url: '../ajax/tesis.php',
        type: 'POST',
        data: { op: 'read_prof', busqueda },
        success: function (response) {
          let profes = JSON.parse(response);
          let template = '';
          //$('#prof_guia').attr('name','');
          $('#docente_curso').attr('name', '');
          profes.forEach(list => {
            prof = list[1] + ' ' + list[2] + ' ' + list[3];
            prof = cadenaMay(prof);
            template += `
      
      <li class='list-group-item listProfTesis' id='${list[0]}' name='${prof}'> ${prof}</li>`
          });

          $('#list_prof').html(template);
          $('#list_prof').show();
        }
      })

    }
  })
})
//cargar profesor guis y profesor coguia 
$('body').on('click', '.listProfTesis', function () {
  id = $(this).attr('id');
  prof = $(this).attr('name');
  $('#prof_tesis').val(prof);
  $('#prof_tesis').attr('name', id);
  $('#list_prof').hide();

})
//cargar estudiantes
$('body').on('click', '.listEst', function () {
  id = $(this).attr('id');
  prof = $(this).attr('name');
  $('#nom_est').val(prof);
  $('#nom_est').attr('name', id);
  $('#list_est').hide();

})
//agregar cotutela
$(document).on('click', '.btn_cotutela', function () {
  cotutela = true
  agregarCotutela()

});
//Borrar Cotutela
$(document).on('click', '.borrar_cotutela', function () {
  cotutela = false
  $("#btn_cotutela").prop('disabled', false)
  $('#ingresar_cotutela').remove()
});
//Eliminar Tesis
$(document).on('click', '.borrar_tesis', function () {
  $('#ingresar_tesis').remove()
  $('#boton_tesis').show();
});
//agregar input opcion otro instituto tesis
$(document).on('change', '#inst_tesis', function () {
  $('#i_t').remove();
  if ($('#inst_tesis').val() == 'otro') {
    $('#row_inst_tesis').append('<div class="col-md-6" id="i_t"><input type="text" class="form-control mb-3" placeholder="Institución" id="inst_tes" maxlength="80"></div>');
  }
})
//agregar input opcion otro instituto cotutela
$(document).on('change', '#inst_cot', function () {
  $('#i_c').remove();
  if ($('#inst_cot').val() == 'otro') {
    $('#row_inst_cot').append('<div class="col-md-6" id="i_c"><input type="text" class="form-control mb-3" placeholder="Institución" id="inst_cotutela" maxlength="80"></div>');
  }
})
//agregar tesis
$('#form_tesis').submit(function (e) {
  e.preventDefault();
  $('#lugar_tesis').click(function () { limpiarSelect('#lugar_tesis'); })
  $('#grad_tesis').click(function () { limpiarSelect('#grad_tesis'); })
  $('#tit_tesis').click(function () { limpiarInput('#tit_tesis'); })
  $('#pais_tesis').click(function () { limpiarSelect('#pais_tesis'); })
  $('#anio_tesis').click(function () { limpiarInput('#anio_tesis'); })
  $('#rol_tesis').click(function () { limpiarSelect('#rol_tesis'); })
  $('#inst_tesis').click(function () { limpiarSelect('#inst_tesis'); })
  $('#inst_tes').click(function () { limpiarInput('#inst_tes'); })
  $('#prof_tesis').click(function () { limpiarInput('#prof_tesis'); })
  $('#fecha_aprob').click(function () { limpiarInput('#fecha_aprob'); })
  $('#fecha_def').click(function () { limpiarInput('#fecha_def'); })
  $('#nom_est').click(function () { limpiarSelect('#nom_est'); })
  $('#pan_ev').click(function () { limpiarSelect('#pan_ev'); })
  $('#prof_gu').click(function () { limpiarInput('#prof_gu'); })
  $('#inst_cot').click(function () { limpiarSelect('#inst_cot'); })
  $('#fondo').click(function () { limpiarInput('#fondo'); })
  $('#ciudad_cot').click(function () { limpiarInput('#ciudad_cot'); })
  $('#pais_cot').click(function () { limpiarSelect('#pais_cot'); })
  let lugar = $('#lugar_tesis').val();
  let grado = $('#grad_tesis').val();
  let anio = $('#anio_tesis').val();
  let titulo = $('#tit_tesis').val();
  let rol = $('#rol_tesis').val();
  let guia
  let nom_coguia
  let coguia
  let nom_guia
  let nom_est = $('#nom_est').attr('name') == 0 ? $('#nom_est').val() : '';
  let est = $('#nom_est').attr('name') == 0 ? '' : $('#nom_est').attr('name');
  let inst_tesis = $('#inst_tesis').val() == 'otro' ? $('#inst_tes').val() : $('#inst_tesis').val();
  let pais_tesis = $('#pais_tesis').val();
  let fecha_aprob = $('#fecha_aprob').val();
  let fecha_def = $('#fecha_def').val();
  let panel = $('#pan_ev').val();
  let prof_cot = $('#prof_gu').val();
  let inst_cot = $('#inst_cot').val() == 'otro' ? $('#inst_cotutela').val() : $('#inst_cot').val();
  let fondo = $('#fondo').val();
  let ciudad_cot = $('#ciudad_cot').val();
  let pais_cot = $('#pais_cot').val();
  if (rol == 1) {
    guia = $('#id_usuario').attr('name');
    nom_coguia = $('#prof_tesis').attr('name') == 0 ? $('#prof_tesis').val() : '';
    coguia = $('#prof_tesis').attr('name') == 0 ? '' : $('#prof_tesis').attr('name');
    nom_guia = '';
  } else if (rol == 2) {
    guia = $('#prof_tesis').attr('name') == 0 ? '' : $('#prof_tesis').attr('name');
    coguia = $('#id_usuario').attr('name');
    nom_coguia = '';
    nom_guia = $('#prof_tesis').attr('name') == 0 ? $('#prof_tesis').val() : '';
  }

  if (validCampoVacio('#inst_tes') || validCampoVacio('#inst_cotutela') || lugar == 0 || grado == 0 || anio == 0 || titulo == '' || rol == 0 || $('#prof_tesis').val() == '' || $('#nom_est').val() == '' || $('#inst_tesis').val() == 0 || pais_tesis == 0 || fecha_aprob == '' || fecha_def == '' || panel == '' || prof_cot == '' || $('#inst_cot').val() == 0 || fondo == '' || ciudad_cot == '' || pais_cot == 0) {
    validSelect('#lugar_tesis');
    validSelect('#rol_tesis');
    validSelect('#grad_tesis');
    validSelect('#anio_tesis');
    validCampoVacio('#tit_tesis');
    validSelect('#inst_tesis');
    validSelect('#pais_tesis');
    validCampoVacio('#fecha_aprob');
    validCampoVacio('#fecha_def');
    validCampoVacio('#pan_ev');
    validCampoVacio('#prof_tesis');
    validCampoVacio('#nom_est');
    validCampoVacio('#prof_gu');
    validCampoVacio('#fondo');
    validCampoVacio('#ciudad_cot');
    validSelect('#inst_cot');
    validSelect('#pais_cot');
    $('#mnsj_row_tesis').show();
    $('#mnsj_tesis').html('Rellene todos los campos requeridos')
    // if($('#nom_cong').attr('name')!=0){
    // //limpiarCamposCong();
    // }
  } else {
    console.log('valida los datos')
    nuevo_inst_tesis = $('#inst_tesis').val() == 'otro' ? true : false;//agregar nuevo instituto
    nuevo_inst_cot = $('#inst_cot').val() == 'otro' ? true : false;//agregar nuevo instituto
    nuevo_financ = $('#financ').val() == 'otro' ? true : false;//agregar nuevo financiamiento 
    if (nuevo_financ) {
      op = 'insert';
      $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/financiamiento.php',
        data: { op, financ },
        success: function (response) {
          let dato = JSON.parse(response);
          $('#id_financ').attr('name', dato);
        }
      })
    }
    if (nuevo_inst_tesis) {
      op = 'insert';
      $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/institucion.php',
        data: { op, nombre: inst_tesis },
        success: function (response) {
          let dato = JSON.parse(response);
          $('#id_inst_tesis').attr('name', dato);
        }
      })
    }
    if (nuevo_inst_cot) {
      op = 'insert';
      $.ajax({
        async: false,
        type: 'POST',
        url: '../ajax/institucion.php',
        data: { op, nombre: inst_cot },
        success: function (response) {
          let dato = JSON.parse(response);
          $('#id_inst_cot').attr('name', dato);
        }
      })
    }
    //obtener id de nueva fuente financiamiento e institucion
    financ = $('#id_financ').attr('name') != 0 ? $('#id_financ').attr('name') : financ
    inst_tesis = $('#id_inst_tesis').attr('name') != 0 ? $('#id_inst_tesis').attr('name') : inst_tesis
    inst_cot = $('#id_inst_cot').attr('name') != 0 ? $('#id_inst_cot').attr('name') : inst_cot
    if ($('#tit_tesis').attr('name') == 0) {
      op = 'insert-update';
      tesis = { lugar, grado, anio, titulo, nom_guia, nom_est, nom_coguia, guia, est, coguia, inst_tesis, pais_tesis, fecha_aprob, fecha_def, panel, cotutela, op };
      if (cotutela) {
        Object.assign(tesis, { prof_cot, inst_cot, fondo, ciudad_cot, pais_cot })
      }
      console.log(tesis)
      $.post('../ajax/tesis.php', tesis, function (response) {
        let dato = JSON.parse(response);
        $('#mnsj_row_tesis').show();
        $('#mnsj_tesis').removeClass('alert-danger');
        $('#mnsj_tesis').addClass('alert-success');
        $('#mnsj_tesis').html(dato);
        setTimeout(function () {
          $('#ingresar_tesis').remove();
          $('#boton_tesis').show();
          cargarTesis($('#id_usuario').attr('name'), '#tesis_card')
          $("#mnsj_row_tesis").fadeOut(1500);
        }, 3000);
      })
    } else {

      id_tesis = $('#tit_tesis').attr('name');
      if (rol == 1) {
        op = 'update-guia';
        $.post('../ajax/tesis.php', { guia, op, id_tesis }, function (response) {
          let dato = JSON.parse(response);
          $('#mnsj_row_tesis').show();
          $('#mnsj_tesis').removeClass('alert-danger');
          $('#mnsj_tesis').addClass('alert-success');
          $('#mnsj_tesis').html(dato);
          setTimeout(function () {
            $('#ingresar_tesis').remove();
            $('#boton_tesis').show();
            //cargarProy($('#id_usuario').attr('name'))
            $("#mnsj_row_tesis").fadeOut(1500);
          }, 3000);
        })
      } else {
        op = 'update-coguia';
        $.post('../ajax/tesis.php', { coguia, op, id_tesis }, function (response) {
          let dato = JSON.parse(response);
          $('#mnsj_row_tesis').show();
          $('#mnsj_tesis').removeClass('alert-danger');
          $('#mnsj_tesis').addClass('alert-success');
          $('#mnsj_tesis').html(dato);
          setTimeout(function () {
            $('#ingresar_tesis').remove();
            $('#boton_tesis').show();
            //cargarCong($('#id_usuario').attr('name'))
            $("#mnsj_row_tesis").fadeOut(1500);
          }, 3000);
        })
      }
    }
  }
})

/// cargo la tesis en la card
// cargarTesis($('#id_usuario').attr('name'),'#tesis_card')




