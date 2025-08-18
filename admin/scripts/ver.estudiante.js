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
  $.ajax({
      async: false,
      url: '../ajax/estudiante.php',
      type: 'POST',
      data: { op: 'read_est_id', id_usu },
      success: function (response) {
          let usu = JSON.parse(response);
          console.log(usu)
          let template;
          nombre = cadenaMay(usu[0]['nombres']) + ' ' + letraMay(usu[0]['ap_pat']) + ' ' + letraMay(usu[0]['ap_mat'])
          cat_acad = usu[0]['cat_academica'] == 1 ? 'Profesor(a) instructor(a)' : usu[0]['cat_academica'] == 2 ? 'Profesor(a) asistente' : usu[0]['cat_academica'] == 3 ? 'Profesor(a) asociado(a)' : usu[0]['cat_academica'] == 4 ? 'Profesor(a) titular' : 'Sin Jerarquía'
          $('#info_est').attr('name', usu[0]['id_usuario'])
          $('#id_usuario').attr('name', usu[0]['id_usuario'])
          template = `
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
      <td class="col-8">${letraMay(usu[0]['p_nac'])}</td>
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
      <td class="col-8">${letraMay(usu[0]['pueblo'])}</td>
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
      <td class="col-8">${cadenaMay(usu[0]['direccion'])}</td>
      </tr>
      <tr>
      <td class="col-4">Comuna</td>
      <td class="col-8">${cadenaMay(usu[0]['comuna'])}</td>
      </tr>
      <tr>
      <td class="col-4">Región</td>
      <td class="col-8">${cadenaMay(usu[0]['region'])}</td>
      </tr>
      <tr>
      <td class="col-4" >País Residencia</td>
      <td class="col-8">${letraMay(usu[0]['p_resi'])}</td>
      </tr>
      <tr>
      <td class="col-4">Teléfono</td>
      <td class="col-8">${usu[0]['telefono']}</td>
      </tr>
      <tr>
      <td class="col-4">Contacto de Emergencia</td>
      <td class="col-8">${cadenaMay(usu[0]['cont_em'])}</td>
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
      <td class="col-8">${cadenaMay(usu[0]["inst"])}</td>
      </tr>
      <tr>
      <td class="col-4">Estado</td>
      <td class="col-8">${cadenaMay(usu[0]['tipo'])}</td>
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
              
              type: 'POST',
              url: '../ajax/docente.php',
              data: { op: 'read_prof_id', id_usu: usu[0]['prof_guia'] },
              success: function (response) {
                  let prof = JSON.parse(response);
                  console.log(prof)
                  nombre = cadenaMay(prof[0]['nombres']) + ' ' + cadenaMay(prof[0]['ap_pat']) + ' ' + cadenaMay(prof[0]['ap_mat'])
                  docente = nombre

                  template += `<tr>
                  <td>Profesor Guía</td>
                  <td>${docente}</td>
                  </tr>`
              }
          })

          template += `<tr>
          <td class="col-4">Linea(s) de Investigación</td>
          <td class="col-8">
          <ul class="list-group list-group-flush">
          `
          usu.forEach(usuario => {
              template += `
        <li class="list-group-item">${usuario['linea']}</li>
      `
    });
    template += `</ul></td></tr>`
    $('#datos_fichaest').html(template);
      }
  })
  $("#ant_acad_est").html('')
  $("#ant_acad_est").append(
    '<div class="row m-2"><div class="col-md-4 m-0 p-2 ps-0 ms-0 mt-2 "><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div><div class="col-md-8 m-0 p-2"><h4><button type="button" class="btn btn-dark btn-sm text-center " id="agrAcadEst">Agregar Datos Académicos</button></h4></div></div>'
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
}
function cargarEst(tipo,busqueda){
    op =
    tipo == 0
      ? "read-est"
      : tipo == 1 || tipo == 2 || tipo == 3
      ? "read-filtrada"
      : "read-est-filtro";
      console.log(op)
    $.ajax({
        async: false,
        url: '../ajax/estudiante.php',
        type: 'POST',
        data: {op,tipo,busqueda},
        success: function (response) {
            console.log(response)
        let estudiantes = JSON.parse(response);
        let template
        estudiantes.forEach(est => {
            nombre=cadenaMay(est['nombres'])+' '+letraMay(est['ap_pat'])+' '+letraMay(est['ap_mat'])
            estado=est['id_tipo']
            template += `<tr>
                <td >${nombre}</td>
                <td class="text-center">${letraMay(est['tipo'])}</td>
                <td class="d-flex justify-content-center">
              
               <!-- desde aca es el modal cambiat tipo estudiante -->
              <input type="hidden" class="login_modal" name=${est['id_login']} value=${est['id_usuario']}></input> 
               <button typr="button"  class="btn btn-outline-primary btn-sm text-center cambiarTipoEst" data-bs-toggle="modal" data-bs-target="#btn_tipo_est${est['id_usuario']}"><i class="fa-solid fa-rotate-right"></i></button>
               <div class="modal fade" id="btn_tipo_est${est['id_usuario']}" tabindex="-1" aria-labelledby="modal_tipo${est['id_usuario']}" aria-hidden="true">

                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title" id="modal_tipo${est['id_usuario']}">Seleccione tipo de
                           Estudiante/Acceso</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body row mx-3">
                        
                      .
                       <select id="tipo-e${est['id_usuario']}" class="form-select tipo-estudiante" name="tipo-estudiante">
                          <option value="0"selected>Seleccione</option>
                          <option value="1">Postulante</option>
                          <option value="2">Aceptado(a)</option>
                          <option value="3">Matriculado(a)</option>
                          <option value="4">Graduado(a)</option>
                          <option value="5">Retirado(a)</option>
                          <option value="6">Eliminado(a)</option>
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
                <td class="text-center"><button type="button" class="btn btn-outline-danger  btn-sm " data-bs-toggle="modal" data-bs-target="#btn_tipo_est${est['id_login']}" id="${est['id_login']}" ><i class="fa-solid fa-trash-can"></i></button>
                
                <!-- desde aca es el modal eliminar estudiante-->
                 <div class="modal fade" id="btn_tipo_est${est['id_login']}" tabindex="-1" aria-labelledby="modal_tipo${est['id_login']}" aria-hidden="true">
  
                     <div class="modal-dialog">
                       <div class="modal-content">
                         <div class="modal-header">
                           <h5 class="modal-title" id="modal_tipo${est['id_login']}">Seleccione tipo de
                             Estudiante/Acceso</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                         </div>
                         <div class="modal-body row mx-3">
                          
                      <p> Esta seguro que quiere eliminar del programa a ${nombre}</p>
                      
                         </div>
         
                         <div class="modal-footer">
                           <!-- evento ajax en js/buttons -->
                           <button type="button" class="btn btn-dark eliminarEst " id="${est['id_login']}" data-bs-dismiss="modal" aria-label="Close" ">Eliminar</button>
                         </div>
         
                       </div>
                     </div>
         
                  
                 </div>
                </td>
                </tr>
                `
                $('#table_est').html(template)
                $('#permisos').val(estado)
                ajaxSelect(`tipo-estudiante${est['id_usuario']}`,'../ajax/estudiante.php', 'Seleccione', 'read_tipo')
            
        });
        }
    })
    
}
$('body').on('click','.verEst',function(){
    id_usu = $(this).attr('id');
    fichaEst();
    cargarFichaEst(id_usu)  
    $("#mnsj_row_acad_est").hide(); 
})
$('body').on('click','.cambiarTipoEst',function(){
  id_login = $('#login_modal').attr('name');
 
         
        
        
    })


// cambiar lista de estudiantes con la opcion select

  $('body').on('click','.permiso',function(){
    op="update-permiso-tipo-est"
    id_usu=$(this).attr('id');
    tipo_est=$('#tipo-e'+id_usu).val()
    id_login = $(this).attr('name');
    console.log(tipo_est)
    $('#btn_tipo_est'+id_usu).modal('hide');
    $.ajax({
      async: false,
      url: '../ajax/estudiante.php',
      type: 'POST',
      data: {op,tipo_est,id_login, id_usu},
      success: function (response) {
        let dato = JSON.parse(response);
        console.log(dato)
        $('#mnsj_row_encabezado').show();
        $('#mnsj_encabezado').removeClass('alert-danger');
        $('#mnsj_encabezado').addClass('alert-success');
        $('#mnsj_encabezado').html(dato);
        setTimeout(function () {
            $("#mnsj_row_encabezado").fadeOut(1500);
            cargarEst()
        }, 3000);
    
      }
  })
})
  // buscar docentes con el nombre
$("#buscar_est").keyup(function () {
    busqueda = $("#buscar_est").val();
    if (busqueda !== "") {
      cargarEst(4, busqueda);
      $("#tipo_est").val(0);
    } else {
      cargarEst($("#tipo_est").val());
    }
  });


   


function init(){
  listaEst()
    $.post('../ajax/estudiante.php', {op:'read_tipo'}, function (response) {
        data = JSON.parse(response);
        templateSelect(data,'#tipo_est_edit')
})

     $('#tipo_est').append('<option value="0" selected>Todos(as)</option>')
     cargarEst()
     cargarEst($('#tipo_est').val())
    $("#box-pass").hide();
    $("#mnsj_row_est").hide();
    $("#mnsj_row_per_est").hide();
    $("#mnsj_row_prog_est").hide();
    $("#mnsj_row_elim_acad_est").hide();
    $("#mnsj_row_permisos_acad_est").hide();
    $("#mnsj_row_encabezado").hide();
    $('.loadPage').fadeOut();
}
init()