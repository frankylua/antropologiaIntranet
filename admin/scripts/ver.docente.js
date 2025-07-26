
function cargarFichaDoc(id_usu) {
  $.ajax({
    async: false,
    url: "../ajax/docente.php",
    type: "POST",
    data: { op: "read_prof_id", id_usu },
    success: function (response) {
      let usu = JSON.parse(response);
      console.log(usu);
      console.log(id_usu)
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
        <td class="col-8">${mostrarFecha(usu[0]["fecha_nac"])}</td>
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
  cargarPostdoc($("#info_doc").attr("name"), "#ficha_postdoc");
  cargarPub($("#info_doc").attr("name"), "#ficha_publicacion");
  cargarCong($("#info_doc").attr("name"), "#ficha_congreso");
  cargarProy($("#info_doc").attr("name"), "#ficha_proyecto");
  cargarPasantia($("#info_doc").attr("name"), "#ficha_pasantia");
  cargarTesis($("#info_doc").attr("name"), "#ficha_tesis");
  $('.titulo_acad').html('')
}
function cargarDocentes(tipo, busqueda) {
  op =
    tipo == 0
      ? "read-doc"
      : tipo == 1 || tipo == 2 || tipo == 3
      ? "read-filtrada"
      : "read_prof";
  $.ajax({
    async: false,
    url: "../ajax/docente.php",
    type: "POST",
    data: { op, tipo, busqueda },
    success: function (response) {
      console.log(response)
      let docentes = JSON.parse(response);
      let template;
      console.log(docentes.length);
      if (docentes.length > 0) {
        docentes.forEach((doc) => {
          nombre =
            cadenaMay(doc["nombres"]) +
            " " +
            letraMay(doc["ap_pat"]) +
            " " +
            letraMay(doc["ap_mat"]);
          vinculo =
            doc["vinculo"] == 1
              ? "Claustro"
              : doc["vinculo"] == 2
              ? "Visitante"
              : "Colaborador(a)";
          template += `<tr>
                        <td >${nombre}</td>
                        <td>${vinculo}</td>
                        <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm  text-center" id="${doc["id_usuario"]}"><i class="fa-solid fa-eye"></i></button></td>
                        <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm verFicha text-center" id="${doc["id_usuario"]}"><i class="fa-solid fa-eye"></i></button></td>
                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm eliminarDoc text-center" id="${doc["id_login"]}" ><i class="fa-solid fa-trash-can"></i></button></td>
                        </tr>
                        `;
        });
      } else {
        template = "";
      }

      $("#table_doc").html(template);
    },
  });
}
function listaDoc() {
  $("#lista_doc").fadeIn();
  $("#info_doc").fadeOut();
  $("#ficha_acad").fadeOut();
  $("#acad_doc").fadeOut();
  $("#edit_pers_doc").fadeOut();
  $("#edit_prog_doc").fadeOut();
  $('#login').attr('name','')
  $('#info_doc').attr('name','')
  $("#edit_acad_doc").fadeOut();
}
// filtrar docentes x categoria
$(document).on("change", "#tipo_doc", function () {
  cargarDocentes($("#tipo_doc").val());
});

// buscar docentes con el nombre
$("#buscar_doc").keyup(function () {
  busqueda = $("#buscar_doc").val();
  if (busqueda !== "") {
    cargarDocentes(4, busqueda);
    $("#tipo_doc").val(0);
  } else {
    cargarDocentes($("#tipo_doc").val());
  }
});
//boton ver ficha academica
$("body").on("click", ".verFicha", function () {
  id_usu = $(this).attr("id");
  console.log('aca esta ek id usuario'+id_usu)
  fichaDoc();
  cargarFichaDoc(id_usu);
  //reinicio los campos para editar antecedentes academicos
  $('#campos_grado').html('')
  $('#campos_postdoc').html('')
  $('#campos_publicacion').html('')
  $('#campos_congreso').html('')
  $('#campos_pasantia').html('')
  $('#campos_tesis').html('')
});
$("body").on("click", ".eliminarDoc", function () {
  id_login = $(this).attr("id");
  $.ajax({
    url: "../ajax/docente.php",
    type: "POST",
    data: { id_login, op: "delete" },
    success: function (response) {
      let mensaje = JSON.parse(response);
      console.log(mensaje);
      $("#mnsj_elim").show();
      $("#eliminado").addClass("alert-success");
      $("#eliminado").html(mensaje);
      cargarDocentes();
      setTimeout(function () {
        $("#mnsj_elim").fadeOut(1500);
      }, 3000);
    },
  });
});
function init() {
  listaDoc();
  cargarDocentes($("#tipo_doc").val());
  $("#box-pass").hide();
  $("#mnsj_row_per").hide();
  $("#mnsj_row_per_doc").hide();
  $("#mnsj_row_prog_doc").hide();
  $("#mnsj_row_acad_doc").hide();
  $('.loadPage').fadeOut();
  
}
init();
