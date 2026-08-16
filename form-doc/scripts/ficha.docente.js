//cargar ficha doce //cargar formulario datos programa desde ajax
  function mensajeErrorDocente(xhr,mensajePredeterminado){
    if(xhr.responseJSON&&xhr.responseJSON.mensaje){
      return xhr.responseJSON.mensaje
    }
    try{
      let respuesta=JSON.parse(xhr.responseText)
      return respuesta.mensaje||mensajePredeterminado
    }catch(error){
      return mensajePredeterminado
    }
  }
  function mostrarErrorPrograma(mensaje){
    $('#mnsj_row_prog_doc').show();
    $('#mnsj_prog_doc').removeClass('alert-success');
    $('#mnsj_prog_doc').addClass('alert-danger');
    $('#mnsj_prog_doc').html(mensaje);
  }
  function llenarFormProgDoc(id_usu){
    $.ajax({
        async: false,
        url: "../ajax/docente.php",
        type: "POST",
        data: { op: "read_prof_prog", id_usu },
        dataType:'json',
        success: function (usu) {
          $('#login').attr('name',usu[0]['id_login'])
          $('#inst_unid_trabajo').val(usu[0]['inst_usuario']);
          $('#cat_acad').val(usu[0]['cat_academica']);
          $('#anio_ing').val(usu[0]['anio_ingreso']);
          $('#vinculo').val(usu[0]['vinculo']);
  
          usu.forEach(u => {
            $("input[type='checkbox'][name='linea'][id='"+u['linea_inv']+"']").attr('checked',true);
          });
        }
       });
  }

  //vista de ficha docente
  function fichaDoc() {
    $("#lista_doc").fadeOut();
    $("#info_doc").fadeIn();
    $("#ficha_acad").fadeOut();
    $("#acad_doc").fadeOut();
    $("#edit_pers_doc").fadeOut();
    $("#edit_prog_doc").fadeOut();
    $("#edit_acad_doc").fadeOut();
  }
  //vista editar datos personales
  function editPersDoc() {
    $("#lista_doc").fadeOut();
    $("#info_doc").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_doc").fadeOut();
    $("#edit_pers_doc").fadeIn();
    $("#edit_prog_doc").fadeOut();
    $("#edit_acad_doc").fadeOut();
  }
  //vista editar datos programa
  function editProgDoc() {
    $("#lista_doc").fadeOut();
    $("#info_doc").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_doc").fadeOut();
    $("#edit_pers_doc").fadeOut();
    $("#edit_prog_doc").fadeIn();
    $("#edit_acad_doc").fadeOut();
  }
  //vista de ficha academica
  function fichaAcad() {
      $("#lista_doc").fadeOut();
      $("#info_doc").fadeOut();
      $("#ficha_acad").fadeIn();
      $("#acad_doc").fadeOut();
      $("#edit_pers_doc").fadeOut();
      $("#edit_acad_doc").fadeOut();
  }
  //vista de ingreso datos académicos
  function acadDoc() {
    $('html, body').stop().animate({
      scrollTop: $('#acad_doc').offset().top
    }, 1000);
    //codigo para llevar al usuario al inicio haciendo scroll
    $("#lista_doc").fadeOut();
    $("#info_doc").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_doc").fadeIn();
    $("#edit_pers_doc").fadeOut();
    $("#edit_acad_doc").fadeOut();
    $('#grados_card').html('')
    $('#postdoc_card').html('')
    $('#publicacion_card').html('')
    $('#congreso_card').html('')
    $('#pasantia_card').html('')
    $('#proyecto_card').html('')
    $('#tesis_card').html('')
    cargarGrado($('#id_usuario').attr('name'), '#grados_card')
    cargarPostdoc($('#id_usuario').attr('name'), '#postdoc_card')
    cargarPub($('#id_usuario').attr('name'), '#publicacion_card')
    cargarCong($('#id_usuario').attr('name'), '#congreso_card')
    cargarPasantia($('#id_usuario').attr('name'), '#pasantia_card')
    cargarProy($('#id_usuario').attr('name'), '#proyecto_card')
    cargarTesis($('#id_usuario').attr('name'), '#tesis_card')
  }
  //vista de formulario editar antecedentes académicos
  function editAcadDoc() {
    $("#lista_doc").fadeOut();
    $("#info_doc").fadeOut();
    $("#ficha_acad").fadeOut();
    $("#acad_doc").fadeOut();
    $("#edit_pers_doc").fadeOut();
    $("#edit_acad_doc").fadeIn();
    cargarGrado($('#id_usuario').attr('name'), '#grados_card')
  }
  function mostrarBotonPass(){
    $("#box-pass").hide();
    $('#btn_cambiar_pass').show();
    $('#box-pass').attr('name',0);
    $('#mnsj_row_per_doc').hide();
    $('#pass').removeClass('is-invalid');
    $('#pass2').removeClass('is-invalid');
  }
  // llenar ficha profesor
function cargarFichaDoc(id_usu) {
  $.ajax({
    async: false,
    url: "../ajax/docente.php",
    type: "POST",
    data: { op: "read_prof_id", id_usu },
    success: function (response) {
      let usu = JSON.parse(response);
      let template;
      nombre =
        cadenaMay(usu[0]["nombres"]) +
        " " +
        letraMay(usu[0]["ap_pat"]) +
        " " +
        letraMay(usu[0]["ap_mat"]);
      cat_acad =
        usu[0]["cat_academica"] == 1
          ? "Profesor(a) instructor(a)"
          : usu[0]["cat_academica"] == 2
          ? "Profesor(a) asistente"
          : usu[0]["cat_academica"] == 3
          ? "Profesor(a) asociado(a)"
          : usu[0]["cat_academica"] == 4
          ? "Profesor(a) titular"
          : "Sin Jerarquía";
      $("#info_doc").attr("name", usu[0]["id_usuario"]);
      $("#id_usuario").attr("name", usu[0]["id_usuario"]);
      template += `
        <tr>
        <td class="col-4" ><h4 class=" mt-3">ANTECEDENTES PERSONALES</h4></td>
        <td class="col-8 ps-0"><h4 class=" mt-3"><button type="button" class="btn btn-dark btn-sm  ps-1 editarPersDoc" id="${
          usu[0]["id_usuario"]
        }">Editar Datos Personales</button></h4></td>
        </tr>
        <tr>
        <td class="col-4" >Nombre</td>
        <td class="col-8">${nombre}</td>
        </tr>
        <tr>
        <td class="col-4" >País Nacionalidad</td>
        <td class="col-8">${letraMay(usu[0]["p_nac"])}</td>
        </tr>
        <td class="col-4" >${usu[0]["tipo_doc"] == 1 ? "Rut" : "Pasaporte"}</td>
        <td class="col-8">${usu[0]["nro_doc"]}</td>
        </tr>
        <tr>
        <td class="col-4" >fecha de Nacimiento</td>
        <td class="col-8">${fechaCivil(usu[0]["fecha_nac"]).presentacion}</td>
        </tr>
        <tr>
        <td class="col-4" >Género</td>
        <td class="col-8">${
          usu[0]["genero"] == 1
            ? "Femenino"
            : usu[0]["genero"] == 2
            ? "Masculino"
            : usu[0]["genero"] == 3
            ? "No Binario"
            : "Otro"
        }</td>
        </tr>
        <tr>
        <td class="col-4" >Pueblo Indígena</td>
        <td class="col-8">${letraMay(usu[0]["pueblo"])}</td>
        </tr>
        <tr>
        <td class="col-4"><h5 class="mt-3">DATOS DE CONTACTO</h5></td>
        <td class="col-8"></td>
        </tr>
        <tr>
        <td class="col-4">Correo Electrónico</td>
        <td class="col-8">${usu[0]["correo"]}</td>
        </tr>
        <tr>
        <td class="col-4">Dirección</td>
        <td class="col-8">${cadenaMay(usu[0]["direccion"])}</td>
        </tr>
        <tr>
        <td class="col-4">Comuna</td>
        <td class="col-8">${cadenaMay(usu[0]["comuna"])}</td>
        </tr>
        <tr>
        <td class="col-4">Región</td>
        <td class="col-8">${cadenaMay(usu[0]["region"])}</td>
        </tr>
        <tr>
        <td class="col-4" >País Residencia</td>
        <td class="col-8">${letraMay(usu[0]["p_resi"])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono</td>
        <td class="col-8">${usu[0]["telefono"]}</td>
        </tr>
        <tr>
        <td class="col-4">Contacto de Emergencia</td>
        <td class="col-8">${cadenaMay(usu[0]["cont_em"])}</td>
        </tr>
        <tr>
        <td class="col-4">Teléfono Contacto Emergencia</td>
        <td class="col-8">${usu[0]["tel_em"]}</td>
        </tr>
        <tr>
        <td class="col-4"><h4 class="mt-3">ANTECEDENTES PROGRAMA</h4></td>
        <td class="col-8 ps-0"><h4 class="mt-3"><button type="button" class="btn btn-dark ps-1 btn-sm   editarProgDoc text-center" id="${
          usu[0]["id_usuario"]
        }">Editar Datos Programa</button></h4></td>
        </tr>
        <tr>
        <td class="col-4">Institución/Unidad Académica</td>
        <td class="col-8">${cadenaMay(usu[0]["inst"])}</td>
        </tr>
        <tr>
        <td class="col-4">Vínculo con el Programa</td>
        <td class="col-8">${
          usu["vinculo"] == 1
            ? "Claustro"
            : usu["vinculo"] == 2
            ? "Visitante"
            : "Colaborador/a"
        }</td>
        </tr>
        <tr>
        <td class="col-4">Categoría Académica</td>
        <td class="col-8">${cat_acad}</td>
        </tr>
        <tr>
        <td class="col-4">Año de Ingreso</td>
        <td class="col-8">${usu[0]["anio_ingreso"]}</td>
        </tr>
     
        `;
        template+=`<tr>
        <td class="col-4">Linea(s) de Investigación</td>
        <td class="col-8">
        <ul class="list-group list-group-flush">
        `
        usu.forEach(usuario => {
          template+=`
          <li class="list-group-item">${usuario['linea']}</li>
        `
        });
        template+=`</ul></td></tr>`
      $("#datos_fichadoc").html(template);
    },
  });
  $("#ant_acad_doc").html('')
  $("#ant_acad_doc").append(
    '<div class="row m-2"><div class="col-md-4 m-0 p-2 ps-0 ms-0 mt-2 "><h4 class="text-left m-0 p-0">ANTECEDENTES ACADÉMICOS</h4></div><div class="col-md-8 m-0 p-2"><h4><button type="button" class="btn btn-dark btn-sm text-center " id="agrAcadDoc">Agregar Datos Académicos</button></h4></div></div>'
  );
  $("#ficha_grado").html('')
  $('#ficha_postdoc').html('')
  $('#ficha_publicacion').html('')
  $('#ficha_congreso').html('')
  $('#ficha_proyecto').html('')
  $('#ficha_pasantia').html('')
  $('#ficha_tesis').html('')
  cargarGrado($("#info_doc").attr("name"), "#ficha_grado");
  cargarPostdoc($("#info_doc").attr("name"), "#ficha_postest");
  cargarPub($("#info_doc").attr("name"), "#ficha_publicacion");
  cargarCong($("#info_doc").attr("name"), "#ficha_congreso");
  cargarProy($("#info_doc").attr("name"), "#ficha_proyecto");
  cargarPasantia($("#info_doc").attr("name"), "#ficha_pasantia");
  cargarTesis($("#info_doc").attr("name"), "#ficha_tesis");
  $('.titulo_acad').html('')
}
  
  function reiniciarInfoDoc(){
    $('#box-pass').show()
    $("html, body").animate({ scrollTop: 0 }, 100);
    fichaDoc();
    $("#datos_fichadoc").html("");
    $("#ant_acad_doc").html("");
    cargarFichaDoc($('#id_usuario').attr('name'))
    mostrarBotonPass()
  }
  //cargar formulario datos personales desde ajax

  function llenarFormPersDoc(id_usu){
    $.ajax({
        async: false,
        url: "../ajax/docente.php",
        type: "POST",
        data: { op: "read_prof_id", id_usu },
        success: function (response) {
          let usu = JSON.parse(response);
          $('#login').attr('name',usu[0]['id_login'])
          $('#nombres').val(cadenaMay(usu[0]['nombres']));
          $('#ap_pat').val(cadenaMay(usu[0]['ap_pat']));
          $('#ap_mat').val(cadenaMay(usu[0]['ap_mat']));
          $('#fech_nac').val(usu[0]['fecha_nac']);
          $('#documento').val(usu[0]['tipo_doc']);
          $('#nro_doc').val(usu[0]['nro_doc']);
          $('#pais_nac').val(usu[0]['pais_nac']);
          $('#genero').val(usu[0]['genero']);
          if(usu[0]['id_pueblo']==1){
            $("input[type='radio'][name='pueblo'][value='no']").attr('checked',true);
          }else{
            $("input[type='radio'][name='pueblo'][value='si']").attr('checked',true);
            $('#p').remove()          
            $('#row_pueb').append('<div class="col-md-6 mb-3" id="p"><label for="pueb_ind" class="form-label" >Pueblo Indígena</label><select id="select_pueb" name="select_pueb" class="form-select"></select></div>')
            ajaxSelect('#select_pueb',ruta+'ajax/pueblo.php','Seleccione','read',undefined,'id_pueblo','pueblo');
          }
          $('#select_pueb').val(usu[0]['id_pueblo']);
          $('#pais_res').val(usu[0]['pais_res']);
          $('#region').val(cadenaMay(usu[0]['region']));
          $('#comuna').val(cadenaMay(usu[0]['comuna']));
          $('#telefono').val(usu[0]['telefono']);
          $('#direccion').val(cadenaMay(usu[0]['direccion']));
          $('#cont_em').val(cadenaMay(usu[0]['cont_em']));
          $('#tel_em').val(usu[0]['tel_em']);
          $('#genero').val(usu[0]['genero']);
          $('#correo').val(usu[0]['correo']);
          $('#pass').val('');
          $('#pass2').val('');
        },
      });
  }
 
  $('body').on('click','#agrAcadDoc',function(){
    acadDoc();  
})
$("body").on("click", ".editarPersDoc", function () {
  $("html, body").animate({ scrollTop: 0 }, 1000);
    editPersDoc()
    llenarFormPersDoc($('#info_doc').attr('name'))
    $('#btn_cambiar_pass').show();
});
$("body").on("click", ".editarProgDoc", function () {
  $("html, body").animate({ scrollTop: 0 }, 100);
    editProgDoc()
    llenarFormProgDoc($('#info_doc').attr('name'))
});
$(".volverTablaDoc").click(function () {
  listaDoc()
});
$('.volverInfoDoc').click(function(){
  reiniciarInfoDoc()    
})
$('#form_edit_pers').submit(function(e){
  e.preventDefault();
  usuario=leerDatPers()
  usuario.id_usu=$('#info_doc').attr('name')
  usuario.id_login=$('#login').attr('name')
  if($('#box-pass').attr('name')=="1"&&usuario['pass']!=usuario['pass2']){
    $('#pass').addClass('is-invalid')
    $('#pass2').addClass('is-invalid')
    $('#mnsj_row_per_doc').show();
    $('#mnsj_per_doc').addClass('alert-danger');
    $('#mnsj_per_doc').html('Las contraseñas no coniciden');
  }else{
    $('#pass').removeClass('is-invalid')
    $('#pass2').removeClass('is-invalid')
      let datosCargados=true;
      $.ajax({
          url:'../ajax/docente.php',
          type: 'POST',
          async:false,
          data: {op:'read_prof_id',id_usu:usuario['id_usu']},
          success: function(response){ 
           let usu = JSON.parse(response);
           usuario['nombres']=usuario['nombres']==''?usu[0]['nombres']:usuario['nombres']
           usuario['ap_pat']=usuario['ap_pat']==''?usu[0]['ap_pat']:usuario['ap_pat']
           usuario['ap_mat']=usuario['ap_mat']==''?usu[0]['ap_mat']:usuario['ap_mat']
           usuario['ap_mat']=usuario['ap_mat']==''?usu[0]['ap_mat']:usuario['ap_mat']
           usuario['comuna']=usuario['comuna']==''?usu[0]['comuna']:usuario['comuna']
           usuario['cont_em']=usuario['cont_em']==''?usu[0]['cont_em']:usuario['cont_em']
           usuario['correo']=usuario['correo']==''?usu[0]['correo']:usuario['correo']
           usuario['direccion']=usuario['direccion']==''?usu[0]['direccion']:usuario['direccion']
           usuario['nro_doc']=usuario['nro_doc']==''?usu[0]['nro_doc']:usuario['nro_doc']
           usuario['pais_nac']=usuario['pais_nac']==0?usu[0]['pais_nac']:usuario['pais_nac']
           usuario['genero']=usuario['genero']==''?usu[0]['genero']:usuario['genero']
           usuario['pais_res']=usuario['pais_res']==''?usu[0]['pais_res']:usuario['pais_res']
           usuario['pueblo']=usuario['pueblo']==null?1:usuario['pueblo']
           usuario['region']=usuario['region']==''?usu[0]['region']:usuario['region']
           usuario['tel_em']=usuario['tel_em']==''?usu[0]['tel_em']:usuario['tel_em']
           usuario['telefono']=usuario['telefono']==''?usu[0]['telefono']:usuario['telefono']
          },
          error:function(xhr){
            datosCargados=false;
            $('#mnsj_row_per_doc').show();
            $('#mnsj_per_doc').removeClass('alert-success').addClass('alert-danger');
            $('#mnsj_per_doc').html(mensajeErrorDocente(xhr,'No fue posible cargar los datos del Profesor.'));
           }
        })
        if(!datosCargados){
          return;
        }
        usuario.op='update-inf-pers'
        $.ajax({
          url:'../ajax/docente.php',
          type:'POST',
          dataType:'json',
          data:usuario,
          success:function(dato){
            if(!dato||dato.ok!==true){
              $('#mnsj_row_per_doc').show();
              $('#mnsj_per_doc').removeClass('alert-success').addClass('alert-danger');
              $('#mnsj_per_doc').text(dato&&dato.mensaje?dato.mensaje:'No fue posible editar los datos personales.');
              return;
            }
            $('#mnsj_row_per_doc').show();
            $('#mnsj_per_doc').removeClass('alert-danger');
            $('#mnsj_per_doc').addClass('alert-success');
            $('#mnsj_per_doc').text(dato.mensaje);
            setTimeout(function() {
                $("#mnsj_row_per_doc").fadeOut(1500);
                reiniciarInfoDoc()
            },3000);
          },
          error:function(xhr){
            $('#mnsj_row_per_doc').show();
            $('#mnsj_per_doc').removeClass('alert-success').addClass('alert-danger');
            $('#mnsj_per_doc').html(mensajeErrorDocente(xhr,'No fue posible editar los datos personales.'));
          }
       }) 

        
   }
})
$('#form_edit_prog').submit(function(e){
  e.preventDefault();
  datProg=leerDatProgDoc()
  datProg.id_usu=$('#info_doc').attr('name')
  datProg.id_login=$('#login').attr('name')
  datProg.op='update-inf-prog'
  let datosProgramaCargados=true;
  $.ajax({
    url:'../ajax/docente.php',
    type:'POST',
    async:false,
    dataType:'json',
    data:{op:'read_prof_prog',id_usu:datProg['id_usu']},
    success:function(usu){
      datProg['instTrab']=datProg['instTrab']==0?usu[0]['inst_usuario']:datProg['instTrab']
      datProg['catAcad']=datProg['catAcad']==0?usu[0]['cat_academica']:datProg['catAcad']
      datProg['anio_ing']=datProg['anio_ing']==0?usu[0]['anio_ingreso']:datProg['anio_ing']
      datProg['vinculo']=datProg['vinculo']==0?usu[0]['vinculo']:datProg['vinculo']
    },
    error:function(xhr){
      datosProgramaCargados=false;
      mostrarErrorPrograma(mensajeErrorDocente(xhr,'No fue posible cargar los datos del Profesor.'));
    }
  })
  if(!datosProgramaCargados){
    return;
  }

  $.ajax({
    url:'../ajax/docente.php',
    type:'POST',
    dataType:'json',
    data:datProg,
    success:function(dato){
      if(!dato||dato.ok!==true){
        mostrarErrorPrograma(dato&&dato.mensaje?dato.mensaje:'No fue posible editar los datos de programa.');
        return;
      }
      $('#mnsj_row_prog_doc').show();
      $('#mnsj_prog_doc').removeClass('alert-danger');
      $('#mnsj_prog_doc').addClass('alert-success');
      $('#mnsj_prog_doc').text(dato.mensaje);
      setTimeout(function() {
        $("#mnsj_row_prog_doc").fadeOut(1500);
        reiniciarInfoDoc()
        cargarDocentes()
      },3000);
    },
    error:function(xhr){
      mostrarErrorPrograma(mensajeErrorDocente(xhr,'No fue posible editar los datos de programa.'));
    }
  })
})
// mostrar formulario editar password
  $('#btn_cambiar_pass').click(function(){
    $("#box-pass").show();
    $('#btn_cambiar_pass').hide();
    $('#box-pass').attr('name',1);
})

