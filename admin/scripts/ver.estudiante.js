// //vista de tabla de estudiantes
function listaEst(){

    $('#lista_est').fadeIn();
    $('#info_est').fadeOut();
    $('#acad_est').fadeOut();
    $("#edit_pers_est").fadeOut();
    $("#edit_prog_est").fadeOut();
    $("#edit_acad_est").fadeOut();
    $("#acad_doc").fadeOut();
}
//vista de ficha estudiantes
function cargarFichaEst(id_usu) {
  let fichaCargada = false;
  $.ajax({
      async: false,
      url: '../ajax/estudiante.php',
      type: 'POST',
      dataType: 'json',
      data: { op: 'read_est_id', id_usu },
      success: function (usu) {
          if (!Array.isArray(usu) || !usu.length) {
              mostrarErrorFichaEstudiante();
              return;
          }
          fichaCargada = true;
          let template = "";
          nombre = CrudHtml.title(usu[0]['nombres']) + ' ' + CrudHtml.title(usu[0]['ap_pat']) + ' ' + CrudHtml.title(usu[0]['ap_mat'])
          $('#info_est').attr('name', usu[0]['id_usuario'])
          $('#id_usuario').attr('name', usu[0]['id_usuario'])
          template = CrudHtml.html`
      <tr>
      <td class="col-4" ><h4 class="pt-1 mt-3">ANTECEDENTES PERSONALES</h4></td>
      <td class="col-8 ps-0"><h4 class=" mt-3"><button type="button" class="btn btn-dark btn-sm  ps-1    editarPers" id="${usu[0]['id_usuario']}">Editar Datos Personales</button></h4></td>
      </tr>
      <tr>
      <td class="col-4" >Nombre</td>
      <td class="col-8">${nombre}</td>
      </tr>
      <tr>
      <td class="col-4" >País Nacionalidad</td>
      <td class="col-8">${CrudHtml.firstUpper(usu[0]['p_nac'])}</td>
      </tr>
      <td class="col-4" >${usu[0]['tipo_doc'] == 1 ? 'Rut' : 'Pasaporte'}</td>
      <td class="col-8">${usu[0]['nro_doc']}</td>
      </tr>
      <tr>
      <td class="col-4" >fecha de Nacimiento</td>
      <td class="col-8">${mostrarFecha(usu[0]['fecha_nac'])}</td>
      </tr>
      <tr>
      <td class="col-4" >Género</td>
      <td class="col-8">${usu[0]['genero'] == 1 ? 'Masculino' : usu[0]['genero'] == 2 ? 'Femenino' : usu[0]['genero'] == 3 ? 'No Binario' : 'Otro'}</td>
      </tr>
      <tr>
      <td class="col-4" >Pueblo Indígena</td>
      <td class="col-8">${CrudHtml.firstUpper(usu[0]['pueblo'])}</td>
      </tr>
      <tr>
      <td class="col-4"><h5 class=" mt-3">DATOS DE CONTACTO</h5></td>
      <td class="col-8"></td>
      </tr>
      <tr>
      <td class="col-4">Correo Electrónico</td>
      <td class="col-8">${usu[0]['correo']}</td>
      </tr>
      <tr>
      <td class="col-4">Dirección</td>
      <td class="col-8">${CrudHtml.title(usu[0]['direccion'])}</td>
      </tr>
      <tr>
      <td class="col-4">Comuna</td>
      <td class="col-8">${CrudHtml.title(usu[0]['comuna'])}</td>
      </tr>
      <tr>
      <td class="col-4">Región</td>
      <td class="col-8">${CrudHtml.title(usu[0]['region'])}</td>
      </tr>
      <tr>
      <td class="col-4" >País Residencia</td>
      <td class="col-8">${CrudHtml.firstUpper(usu[0]['p_resi'])}</td>
      </tr>
      <tr>
      <td class="col-4">Teléfono</td>
      <td class="col-8">${usu[0]['telefono']}</td>
      </tr>
      <tr>
      <td class="col-4">Contacto de Emergencia</td>
      <td class="col-8">${CrudHtml.title(usu[0]['cont_em'])}</td>
      </tr>
      <tr>
      <td class="col-4">Teléfono Contacto Emergencia</td>
      <td class="col-8">${usu[0]['tel_em']}</td>
      </tr>
      <tr>
      <td class="col-4"><h4 class="mt-3">ANTECEDENTES PROGRAMA</h4></td>
      <td class="col-8 ps-0"><h4 class="mt-3"><button type="button" class="btn btn-dark ps-1 btn-sm   editarProgEst text-center" id="${usu[0]['id_usuario']}">Editar Datos Programa</button></h4></td>
      </tr>
      <tr>
      <td class="col-4">Institución/Unidad Académica</td>
      <td class="col-8">${CrudHtml.title(usu[0]["inst"])}</td>
      </tr>
      <tr>
      <td class="col-4">Estado</td>
      <td class="col-8">${CrudHtml.title(usu[0]['tipo'])}</td>
      </tr>
      <tr>
      <td class="col-4">Fecha de Ingreso</td>
      <td class="col-8">${mostrarFecha(usu[0]['fech_ingr'])}</td>
      </tr>
      <tr>
      <td class="col-4">Fecha de Egreso</td>
      <td class="col-8">${usu[0]['fecha_egr'] == null ? '------' : mostrarFecha(usu[0]['fecha_egr'])}</td>
      </tr>
      <tr>
      <td class="col-4">Promedio de Notas Pregrado</td>
      <td class="col-8">${usu[0]['prom_notas']}</td>
      </tr>
      `
          //profesor guia
          $.ajax({
              async: false,
              type: 'POST',
              url: '../ajax/estudiante.php',
              dataType: 'json',
              data: { op: 'read_nom_prof', id_estudiante: usu[0]['id_usuario'] },
              success: function (response) {
                  if (!response.ok || !response.datos || response.datos.length !== 1) {
                      return;
                  }
                  let prof = response.datos;
                  nombre = CrudHtml.title(prof[0]['nombres']) + ' ' + CrudHtml.title(prof[0]['ap_pat']) + ' ' + CrudHtml.title(prof[0]['ap_mat'])
                  docente = nombre

                  template += CrudHtml.html`<tr>
                  <td>Profesor Guía</td>
                  <td>${docente}</td>
                  </tr>`
              },
              error: function () {
                  console.error('No fue posible cargar el profesor guía del Estudiante.');
              }
          })

          template += CrudHtml.html`<tr>
          <td class="col-4">Linea(s) de Investigación</td>
          <td class="col-8">
          <ul class="list-group list-group-flush">
          `
          usu.forEach(usuario => {
              template += CrudHtml.html`
        <li class="list-group-item">${usuario['linea']}</li>
      `
    });
    template += CrudHtml.html`</ul></td></tr>`
    $('#datos_fichaest').html(template);
    // La ficha ya está disponible y el subject corresponde a usuario.id_usuario.
    // Tesis se carga de forma contextual, sin intervenir en la ficha histórica.
    $(document).trigger('tesis:subject-ready');
      },
      error: function (xhr) {
          mostrarErrorFichaEstudiante(xhr);
      }
  })
  if (!fichaCargada) return;
  $("#ant_acad_est").html('')
  $("#ant_acad_est").append(
    '<div class="row m-2"><div class="col m-0 p-2 ps-0 ms-0 mt-2"><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div></div>'
);
$("#grado_est").html('')
$('#postdoc_est').html('')
$('#publi_est').html('')
$('#cong_est').html('')
$('#proy_est').html('')
$('#pasant_est').html('')

cargarGrado($("#info_est").attr("name"), "#grado_est");
cargarPostdoc($("#info_est").attr("name"), "#postdoc_est");
cargarPub($("#info_est").attr("name"), "#publi_est");
cargarCong($("#info_est").attr("name"), "#cong_est");
cargarProy($("#info_est").attr("name"), "#proy_est");
cargarPasantia($("#info_est").attr("name"), "#pasant_est");
cargarBeca($("#info_est").attr("name"), "#beca_est");
}
function escaparListaEst(valor) {
    return CrudHtml.text(valor);
}
function cargarEst(tipo,busqueda){
    tipo = tipo == null ? ($('#tipo_est').val() || 0) : tipo;
    busqueda = busqueda == null ? ($('#buscar_est').val() || '') : busqueda;
    const op = 'read-est';
    const elimina = $('#reglas_estudiante').attr('data-elimina') === '1';
    $.ajax({
        async: false,
        url: '../ajax/estudiante.php',
        type: 'POST',
        dataType: 'json',
        data: {op,tipo,busqueda},
        success: function (response) {
            let estudiantes = Array.isArray(response) ? response : [];
        let template = ''
        estudiantes.forEach(est => {
            const nombre=escaparListaEst(CrudHtml.title(est.nombres)+' '+CrudHtml.title(est.ap_pat)+' '+CrudHtml.title(est.ap_mat));
            template += CrudHtml.html`<tr>
                <td >${nombre}</td>
                <td class="text-center">${escaparListaEst(est['tipo'])}</td>
                <td class="d-flex justify-content-center">
              
               <!-- desde aca es el modal cambiat tipo estudiante -->
              <input type="hidden" class="login_modal" name="${est['id_login']}" value="${est['id_usuario']}"></input>
               <button type="button"  class="btn btn-outline-primary btn-sm text-center cambiarTipoEst" data-bs-toggle="modal" data-bs-target="#estado-est-${est['id_usuario']}"><i class="fa-solid fa-rotate-right"></i></button>
               <div class="modal fade" id="estado-est-${est['id_usuario']}" tabindex="-1" aria-labelledby="modal-estado-est-${est['id_usuario']}" aria-hidden="true">

                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title" id="modal-estado-est-${est['id_usuario']}">Cambiar estado del Estudiante</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body row mx-3">

                       <select id="tipo-e${est['id_usuario']}" class="form-select tipo-estudiante" name="tipo-estudiante">
                          <option value="0"selected>Seleccione</option>
                          <option value="1">Postulante</option>
                          <option value="2">Aceptado(a)</option>
                          <option value="3">Matriculado(a)</option>
                          <option value="4">Graduado(a)</option>
                          <option value="5">Retirado(a)</option>
                          <option value="6">Inactivo(a)</option>
                          <option value="7">Reprobado(a)</option>
                        </select>
                    
                       </div>
       
                       <div class="modal-footer">
                         <!-- evento ajax en js/buttons -->
                         <button type="button" class="btn btn-dark permiso " id="${est['id_usuario']}" data-bs-dismiss="modal" aria-label="Close" name="${est['id_login']}">Actualizar</button>
                       </div>
       
                     </div>
                   </div>
       
                
               </div>
      
                </td>
                <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm verEst text-center" id="${est['id_usuario']}"><i class="fa-solid fa-eye"></i></button></td>
                ${elimina && Number(est.id_tipo) !== 6 ? CrudHtml.fragment`<td class="text-center"><button type="button" class="btn btn-outline-danger  btn-sm " data-bs-toggle="modal" data-bs-target="#eliminar-est-${est['id_login']}" id="${est['id_login']}" ><i class="fa-solid fa-trash-can"></i></button>
                
                <!-- desde aca es el modal eliminar estudiante-->
                 <div class="modal fade" id="eliminar-est-${est['id_login']}" tabindex="-1" aria-labelledby="modal-eliminar-est-${est['id_login']}" aria-hidden="true">
  
                     <div class="modal-dialog">
                       <div class="modal-content">
                         <div class="modal-header">
                           <h5 class="modal-title" id="modal-eliminar-est-${est['id_login']}">Inactivar Estudiante</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                         </div>
                         <div class="modal-body row mx-3">
                          
                      <p>¿Desea inactivar a ${nombre}? Se ocultará de la grilla habitual y se conservarán su cuenta, antecedentes y relaciones. Podrá reactivarlo desde Inactivos y Datos del programa.</p>
                      
                         </div>
         
                         <div class="modal-footer">
                           <!-- evento ajax en js/buttons -->
                           <button type="button" class="btn btn-dark eliminarEst " id="${est['id_login']}" data-bs-dismiss="modal" aria-label="Close">Inactivar</button>
                         </div>
         
                       </div>
                     </div>
         
                  
                 </div>
                </td>` : CrudHtml.fragment`<td></td>`}
                </tr>
                `

        });
        $('#table_est').html(template || '<tr><td colspan="5" class="text-center">No se encontraron estudiantes.</td></tr>');
        },
        error: function(xhr) {
            $('#table_est').empty().append($('<tr>').append($('<td>').attr('colspan',5).text(mensajeErrorFichaEstudiante(xhr))));
        }
    })
    
}
$('body').on('click','.verEst',function(){
    id_usu = $(this).attr('id');
    fichaEst();
    cargarFichaEst(id_usu)  
    $("#mnsj_row_acad_est").hide(); 
})
  $('body').on('click','.permiso',function(){
    const id_usu=$(this).attr('id');
    const tipo_est=$('#tipo-e'+id_usu).val();
    const id_login=$(this).attr('name');
    $.ajax({
      url: '../ajax/estudiante.php', type: 'POST', dataType: 'json',
      data: {op:'update-permiso-tipo-est',tipo_est,id_login,id_usu},
      success: function(mensaje) {
        $('#mnsj_row_encabezado').show();
        $('#mnsj_encabezado').removeClass('alert-danger').addClass('alert-success').text(mensaje);
        cargarEst();
      },
      error: function(xhr) {
        $('#mnsj_row_encabezado').show();
        $('#mnsj_encabezado').removeClass('alert-success').addClass('alert-danger').text(mensajeErrorFichaEstudiante(xhr));
      }
    });
});
$('#buscar_est').keyup(function(){ cargarEst(); });
$('#tipo_est').change(function(){ cargarEst(); });

function init(){
  listaEst()

     $.ajax({url:'../ajax/estudiante.php',type:'POST',dataType:'json',data:{op:'read_tipo'},
       success:function(tipos){
         const select=$('#tipo_est').empty().append($('<option>').val(0).text('Todos(as) activos(as)'));
         tipos.forEach(t => select.append($('<option>').val(t.id_tipo_est).text(CrudHtml.title(t.tipo))));
         select.val(0);
         cargarEst(0);
       },
       error:function(xhr){ mostrarErrorFichaEstudiante(xhr); }
     });
    $("#box-pass").hide();
    $("#mnsj_row_est").hide();
    $("#mnsj_row_per_est").hide();
    $("#mnsj_row_prog_est").hide();
    $("#mnsj_row_elim_acad_est").hide();
    $("#mnsj_row_permisos_acad_est").hide();
    $("#mnsj_row_encabezado").hide();
    $('.loadPage').fadeOut();
    $('#lista_est').attr('name','true')
}
init()
