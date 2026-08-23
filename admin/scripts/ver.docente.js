window.contextoDocenteTercero = true;

function cargarFichaDoc(id_usu) {
  let fichaCargada = false;
  $.ajax({
    async: false,
    url: "../ajax/docente.php",
    type: "POST",
    dataType: "json",
    data: { op: "read_prof_id", id_usu },
    success: function (respuesta) {
      if (!respuesta.ok || !respuesta.datos || respuesta.datos.length === 0) {
        return;
      }
      fichaCargada = true;
      let usu = respuesta.datos;
      let template = "";
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
      $("#login").attr("name", usu[0]["id_login"]);
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
          usu[0]["vinculo"] == 1
            ? "Claustro"
            : usu[0]["vinculo"] == 2
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
        if (respuesta.puede_gestionar_rol) {
          template += `<tr>
          <td class="col-4">Rol administrativo</td>
          <td class="col-8">
            <div class="row g-2 align-items-center">
              <div class="col-md-7">
                <select class="form-select" id="rol_admin_docente">
                  <option value="none">Ninguno</option>
                  <option value="admin">Administrador</option>
                  <option value="comite">Comité Académico</option>
                </select>
              </div>
              <div class="col-md-5">
                <button type="button" class="btn btn-dark" id="guardar_rol_docente">Guardar rol</button>
              </div>
            </div>
            <div class="alert mt-2 mb-0 d-none" id="mensaje_rol_docente" role="alert"></div>
          </td>
          </tr>`;
        }
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
      if (respuesta.puede_gestionar_rol) {
        if (["none", "admin", "comite"].includes(respuesta.rol_administrativo)) {
          $("#rol_admin_docente").val(respuesta.rol_administrativo);
        } else {
          $("#rol_admin_docente, #guardar_rol_docente").prop("disabled", true);
          $("#mensaje_rol_docente")
            .removeClass("d-none alert-success")
            .addClass("alert-danger")
            .text("El estado actual de permisos requiere revisión técnica.");
        }
      }
    },
  });
  if (!fichaCargada) {
    return;
  }
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
function cargarDocentes(tipo, busqueda, estado) {
  const tipoFiltro = tipo === undefined ? Number($("#tipo_doc").val()) : Number(tipo);
  const estadoFiltro = estado === undefined ? Number($("#estado_profesor_filtro").val()) : Number(estado);
  const op =
    tipoFiltro == 0
      ? "read-doc"
      : tipoFiltro == 1 || tipoFiltro == 2 || tipoFiltro == 3
      ? "read-filtrada"
      : "read-doc-search";
  $.ajax({
    async: false,
    url: "../ajax/docente.php",
    type: "POST",
    data: { op, tipo: tipoFiltro, busqueda, estado: estadoFiltro },
    success: function (response, textStatus, xhr) {
      let docentes = JSON.parse(response);
      let template = "";
      const puedeEliminar = xhr.getResponseHeader("X-Puede-Eliminar-Docente") === "1";
      if (!puedeEliminar) {
        $("#table_doc").closest("table").find("thead th").filter(function () {
          return $(this).text().trim() === "Eliminar";
        }).remove();
      }
      if (docentes.length > 0) {
        docentes.forEach((doc) => {
          const nombre =
            cadenaMay(doc["nombres"]) +
            " " +
            letraMay(doc["ap_pat"]) +
            " " +
            letraMay(doc["ap_mat"]);
          const vinculo =
            doc["vinculo"] == 1
              ? "Claustro"
              : doc["vinculo"] == 2
              ? "Visitante"
              : "Colaborador(a)";
          const estadoProfesor = Number(doc["estado_profesor"]);
          const etiquetaEstado = estadoProfesor === 1
            ? "Pendiente"
            : estadoProfesor === 2
            ? "Aceptado"
            : estadoProfesor === 3
            ? "Rechazado"
            : "Invalido";
          let accionesEstado = "";
          if (estadoProfesor === 1) {
            accionesEstado = `<button type="button" class="btn btn-outline-success btn-sm cambiarEstadoProfesor" data-login="${doc["id_login"]}" data-estado="2">Aceptar</button>
              <button type="button" class="btn btn-outline-danger btn-sm cambiarEstadoProfesor" data-login="${doc["id_login"]}" data-estado="3">Rechazar</button>`;
          } else if (estadoProfesor === 3) {
            accionesEstado = `<button type="button" class="btn btn-outline-success btn-sm cambiarEstadoProfesor" data-login="${doc["id_login"]}" data-estado="2">Aceptar</button>`;
          }
          const accionEliminar = puedeEliminar
            ? `<td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm eliminarDoc text-center" id="${doc["id_login"]}" ><i class="fa-solid fa-trash-can"></i></button></td>`
            : "";
          template += `<tr>
                        <td >${nombre}</td>
                        <td>${vinculo}</td>
                        <td>${etiquetaEstado}</td>
                        <td class="text-center">${accionesEstado}</td>
                        <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm  text-center" id="${doc["id_usuario"]}"><i class="fa-solid fa-eye"></i></button></td>
                        <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm verFicha text-center" id="${doc["id_usuario"]}"><i class="fa-solid fa-eye"></i></button></td>
                        ${accionEliminar}
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
$(document).on("change", "#tipo_doc, #estado_profesor_filtro", function () {
  cargarDocentes($("#tipo_doc").val(), undefined, $("#estado_profesor_filtro").val());
});

// buscar docentes con el nombre
$("#buscar_doc").keyup(function () {
  busqueda = $("#buscar_doc").val();
  if (busqueda !== "") {
    cargarDocentes(4, busqueda, $("#estado_profesor_filtro").val());
    $("#tipo_doc").val(0);
  } else {
    cargarDocentes($("#tipo_doc").val(), undefined, $("#estado_profesor_filtro").val());
  }
});
$("body").on("click", ".cambiarEstadoProfesor", function () {
  const id_login = Number($(this).data("login"));
  const estado_objetivo = Number($(this).data("estado"));
  const mensaje = $("#mensaje_estado_docente");
  if (!Number.isInteger(id_login) || id_login < 1 || ![2, 3].includes(estado_objetivo)) {
    mensaje.removeClass("d-none alert-success").addClass("alert-danger").text("No fue posible validar la transicion solicitada.");
    return;
  }
  const accion = estado_objetivo === 2 ? "aceptar" : "rechazar";
  if (!window.confirm(`¿Desea ${accion} esta solicitud de Profesor?`)) {
    return;
  }
  $.ajax({
    url: "../ajax/docente.php",
    type: "POST",
    dataType: "json",
    data: { op: "update-estado-profesor", id_login, estado_objetivo },
    success: function (respuesta) {
      if (!respuesta || respuesta.ok !== true) {
        mensaje.removeClass("d-none alert-success").addClass("alert-danger").text(
          respuesta && respuesta.mensaje ? respuesta.mensaje : "No fue posible actualizar el estado del Profesor."
        );
        return;
      }
      mensaje.removeClass("d-none alert-danger").addClass("alert-success").text(respuesta.mensaje);
      cargarDocentes($("#tipo_doc").val(), undefined, $("#estado_profesor_filtro").val());
    },
    error: function (xhr) {
      const texto = xhr.responseJSON && xhr.responseJSON.mensaje
        ? xhr.responseJSON.mensaje
        : "No fue posible actualizar el estado del Profesor.";
      mensaje.removeClass("d-none alert-success").addClass("alert-danger").text(texto);
    },
  });
});
//boton ver ficha academica
$("body").on("click", ".verFicha", function () {
  id_usu = $(this).attr("id");
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
$("body").on("click", "#guardar_rol_docente", function () {
  const id_login = Number($("#login").attr("name"));
  const rol_admin = $("#rol_admin_docente").val();
  const mensaje = $("#mensaje_rol_docente");
  if (!Number.isInteger(id_login) || id_login < 1) {
    mensaje.removeClass("d-none alert-success").addClass("alert-danger").text("No fue posible validar el Profesor.");
    return;
  }
  $.ajax({
    url: "../ajax/docente.php",
    type: "POST",
    dataType: "json",
    data: { op: "update-rol-admin", id_login, rol_admin },
    success: function (respuesta) {
      if (respuesta.ok !== true) {
        mensaje.removeClass("d-none alert-success").addClass("alert-danger").text(respuesta.mensaje || "No fue posible actualizar el rol.");
        return;
      }
      mensaje.removeClass("d-none alert-danger").addClass("alert-success").text(respuesta.mensaje);
    },
    error: function (xhr) {
      const texto = xhr.responseJSON && xhr.responseJSON.mensaje
        ? xhr.responseJSON.mensaje
        : "No fue posible actualizar el rol.";
      mensaje.removeClass("d-none alert-success").addClass("alert-danger").text(texto);
    },
  });
});
$("body").on("click", ".eliminarDoc", function () {
  const id_login = $(this).attr("id");
  if (!window.confirm("Esta operación eliminará completamente la cuenta del Profesor. ¿Desea continuar?")) {
    return;
  }
  $.ajax({
    url: "../ajax/docente.php",
    type: "POST",
    dataType: "json",
    data: { id_login, op: "delete" },
    success: function (response) {
      if (!response.ok) {
        $("#mnsj_elim").show();
        $("#eliminado").removeClass("alert-success").addClass("alert-danger");
        $("#eliminado").text(response.mensaje || "No fue posible eliminar al Profesor.");
        return;
      }
      $("#mnsj_elim").show();
      $("#eliminado").removeClass("alert-danger").addClass("alert-success");
      $("#eliminado").text(response.mensaje);
      cargarDocentes($("#tipo_doc").val(), undefined, $("#estado_profesor_filtro").val());
      setTimeout(function () {
        $("#mnsj_elim").fadeOut(1500);
      }, 3000);
    },
    error: function (xhr) {
      let mensaje = "No fue posible eliminar al Profesor.";
      if (xhr.responseJSON && xhr.responseJSON.mensaje) {
        mensaje = xhr.responseJSON.mensaje;
      } else {
        try {
          const respuesta = JSON.parse(xhr.responseText);
          mensaje = respuesta.mensaje || mensaje;
        } catch (error) {
        }
      }
      $("#mnsj_elim").show();
      $("#eliminado").removeClass("alert-success").addClass("alert-danger");
      $("#eliminado").text(mensaje);
    },
  });
});
function init() {
  listaDoc();
  $('#lista_doc').attr('name','true')
  cargarDocentes($("#tipo_doc").val(), undefined, $("#estado_profesor_filtro").val());
  const idUsuario=new URLSearchParams(window.location.search).get('id_usuario');
  if(idUsuario && /^[1-9]\d*$/.test(idUsuario)){
    fichaDoc();
    cargarFichaDoc(Number(idUsuario));
  }
  $("#box-pass").hide();
  $("#mnsj_row_per").hide();
  $("#mnsj_row_per_doc").hide();
  $("#mnsj_row_prog_doc").hide();
  $("#mnsj_row_acad_doc").hide();
  $('.loadPage').fadeOut();
  
}
init();
