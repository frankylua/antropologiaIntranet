(function ($) {
  "use strict";

  const endpointCurso = "../ajax/curso.php";
  const endpointProfesor = "../ajax/docente.php";
  const maxPdfBytes = 5 * 1024 * 1024;
  const $app = $("#curso_app");
  const puedeCrear = $app.data("puede-crear") === 1;
  const seleccionaProfesor = $app.data("selecciona-profesor") === 1;
  const csrfToken = $("meta[name='csrf-curso']").attr("content") || "";

  let cursoActual = null;
  let mutacionActiva = false;
  let deleteActivo = false;
  let solicitudProfesores = null;
  let temporizadorMensaje = null;

  function mensajeDesdeError(xhr, fallback) {
    if (xhr && xhr.responseJSON && typeof xhr.responseJSON.mensaje === "string") {
      return xhr.responseJSON.mensaje;
    }
    return fallback;
  }

  function mostrarMensaje(mensaje, tipo) {
    const $contenedor = $("#mnsj_global");
    const $mensaje = $("#mnsj");
    if (temporizadorMensaje !== null) {
      window.clearTimeout(temporizadorMensaje);
      temporizadorMensaje = null;
    }
    $mensaje
      .removeClass("alert-success alert-danger alert-secondary")
      .addClass(tipo === "success" ? "alert-success" : "alert-danger")
      .text(mensaje);
    $contenedor.prop("hidden", false);
    if (tipo === "success") {
      temporizadorMensaje = window.setTimeout(ocultarMensaje, 3000);
    }
  }

  function ocultarMensaje() {
    if (temporizadorMensaje !== null) {
      window.clearTimeout(temporizadorMensaje);
      temporizadorMensaje = null;
    }
    $("#mnsj_global").prop("hidden", true);
    $("#mnsj").text("");
  }

  function etiquetaPeriodo(periodo) {
    if (periodo === 1) return "I Semestre";
    if (periodo === 2) return "II Semestre";
    if (periodo === 3) return "III Semestre";
    return "Periodo no disponible";
  }

  function etiquetaCaracter(caracter) {
    if (caracter === 1) return "Obligatorio";
    if (caracter === 2) return "Seminario";
    return "Carácter no disponible";
  }

  function mostrarListado() {
    $("#list_curso").prop("hidden", false);
    $("#form_curso_container").prop("hidden", true);
    $("#det_curso").prop("hidden", true);
  }

  function mostrarFormulario() {
    if (!puedeCrear || !$("#form_curso").length) return;
    $("#list_curso").prop("hidden", true);
    $("#det_curso").prop("hidden", true);
    $("#form_curso_container").prop("hidden", false);
  }

  function mostrarDetalle() {
    $("#list_curso").prop("hidden", true);
    $("#form_curso_container").prop("hidden", true);
    $("#det_curso").prop("hidden", false);
  }

  function limpiarFormulario() {
    cursoActual = null;
    $("#id_curso").val("0");
    $("#nom_curso").val("0");
    $("#creditos").val("");
    $("#caracter").val("0");
    $("#periodo").val("0");
    $("#anio_curso").val("0");
    $("#car_hor").val("");
    $("#prog_curso").val("");
    $("#estado_programa").text("El programa PDF es obligatorio al crear.");
    $("#titulo_form_curso").text("INGRESAR CURSO");
    if (seleccionaProfesor) {
      $("#docente_curso").val("").removeData("id-usuario");
      $("#list_prof_curso").empty().hide();
    }
  }

  function cargarAnios() {
    const $select = $("#anio_curso");
    if (!$select.length) return;
    $select.empty().append($("<option>", { value: "0" }).text("Seleccione"));
    const anioActual = new Date().getFullYear();
    for (let anio = anioActual; anio >= 2006; anio -= 1) {
      $select.append($("<option>", { value: String(anio) }).text(String(anio)));
    }
  }

  function cargarCatalogo() {
    if (!$("#nom_curso").length) return;
    $.ajax({
      url: endpointCurso,
      type: "POST",
      dataType: "json",
      data: { op: "read_cursos" },
      success: function (response) {
        if (!response || response.ok !== true || !Array.isArray(response.datos)) {
          mostrarMensaje("No fue posible cargar el catálogo de Cursos.", "error");
          return;
        }
        const $select = $("#nom_curso");
        $select.empty().append($("<option>", { value: "0" }).text("Seleccione"));
        response.datos.forEach(function (item) {
          $select.append(
            $("<option>", { value: String(item.id_nom_curso) }).text(item.nom_curso)
          );
        });
      },
      error: function (xhr) {
        mostrarMensaje(mensajeDesdeError(xhr, "No fue posible cargar el catálogo de Cursos."), "error");
      },
    });
  }

  function botonAccion(texto, clase, idCurso) {
    return $("<button>", {
      type: "button",
      class: clase,
      "data-id-curso": String(idCurso),
    }).text(texto);
  }

  function renderListado(cursos) {
    const $tbody = $("#datos_curso").empty();
    cursos.forEach(function (curso) {
      const $fila = $("<tr>");
      $fila.append($("<td>").text(curso.nom_curso));
      $fila.append($("<td>").text(etiquetaPeriodo(curso.periodo)));
      $fila.append($("<td>").text(String(curso.anio_curso)));
      $fila.append(
        $("<td>").append(
          botonAccion("Ver", "btn btn-link link-success btn-sm verCurso", curso.id_curso)
        )
      );
      const $acciones = $("<td>");
      if (curso.puede_eliminar === true) {
        const $eliminar = botonAccion(
          "Eliminar",
          "btn btn-link link-danger btn-sm eliminarCurso",
          curso.id_curso
        );
        $eliminar.data("nombre-curso", curso.nom_curso);
        $acciones.append($eliminar);
      }
      $fila.append($acciones);
      $tbody.append($fila);
    });
  }

  function mostrarCursos() {
    $.ajax({
      url: endpointCurso,
      type: "POST",
      dataType: "json",
      data: { op: "read" },
      success: function (response) {
        if (!response || response.ok !== true || !Array.isArray(response.datos)) {
          mostrarMensaje("No fue posible obtener los Cursos.", "error");
          return;
        }
        renderListado(response.datos);
      },
      error: function (xhr) {
        mostrarMensaje(mensajeDesdeError(xhr, "No fue posible obtener los Cursos."), "error");
      },
    });
  }

  function cargarDetalle(idCurso, alCompletar) {
    $.ajax({
      url: endpointCurso,
      type: "POST",
      dataType: "json",
      data: { op: "query_id", id_curso: idCurso },
      success: function (response) {
        if (!response || response.ok !== true || !response.datos) {
          mostrarMensaje("No fue posible obtener el Curso.", "error");
          return;
        }
        alCompletar(response.datos);
      },
      error: function (xhr) {
        mostrarMensaje(mensajeDesdeError(xhr, "No fue posible obtener el Curso."), "error");
      },
    });
  }

  function renderDetalle(curso) {
    cursoActual = curso;
    $("#nom").text(curso.nom_curso);
    $("#cred").text(curso.creditos);
    $("#car").text(etiquetaCaracter(curso.caracter));
    $("#per").text(etiquetaPeriodo(curso.periodo));
    $("#year").text(String(curso.anio_curso));
    $("#carg").text(String(curso.carga_hor) + " horas cronológicas");
    $("#prof").text(curso.nombre_profesor || "Profesor no disponible");

    const $programa = $("#prog").empty();
    if (curso.programa_disponible === true) {
      $programa.append(
        $("<a>", {
          href: endpointCurso + "?op=download&id_curso=" + encodeURIComponent(curso.id_curso),
          class: "link-secondary",
        }).text("Descargar Programa")
      );
    } else {
      $programa.text("Programa no disponible");
    }

    $("#editar_curso").prop("hidden", curso.puede_editar !== true);
    mostrarDetalle();
  }

  function prepararEdicion(curso) {
    cursoActual = curso;
    $("#id_curso").val(String(curso.id_curso));
    $("#nom_curso").val(String(curso.id_nom_curso));
    $("#creditos").val(curso.creditos);
    $("#caracter").val(String(curso.caracter));
    $("#periodo").val(String(curso.periodo));
    $("#anio_curso").val(String(curso.anio_curso));
    $("#car_hor").val(String(curso.carga_hor));
    $("#prog_curso").val("");
    $("#estado_programa").text(
      curso.programa_disponible === true
        ? "Conserve el campo vacío para mantener el programa vigente."
        : "Debe adjuntar un PDF para reemplazar el programa ausente."
    );
    $("#titulo_form_curso").text("EDITAR CURSO");
    if (seleccionaProfesor) {
      $("#docente_curso")
        .val(curso.nombre_profesor || "")
        .data("id-usuario", Number(curso.profesor));
    }
    mostrarFormulario();
  }

  function validarFormulario() {
    const idCurso = Number($("#id_curso").val() || 0);
    const nomCurso = Number($("#nom_curso").val() || 0);
    const creditos = String($("#creditos").val() || "").trim();
    const caracter = Number($("#caracter").val() || 0);
    const periodo = Number($("#periodo").val() || 0);
    const anioCurso = Number($("#anio_curso").val() || 0);
    const cargaHor = String($("#car_hor").val() || "").trim();
    const archivo = $("#prog_curso")[0].files[0] || null;

    if (
      nomCurso < 1
      || !/^\d+(?:\.\d+)?$/.test(creditos)
      || ![1, 2].includes(caracter)
      || ![1, 2, 3].includes(periodo)
      || anioCurso < 2006
      || anioCurso > new Date().getFullYear()
      || !/^-?\d+$/.test(cargaHor)
    ) {
      mostrarMensaje("Debe completar todos los campos del Curso con valores válidos.", "error");
      return null;
    }
    if (idCurso === 0 && archivo === null) {
      mostrarMensaje("Debe adjuntar el programa PDF del Curso.", "error");
      return null;
    }
    if (archivo && (archivo.size < 1 || archivo.size > maxPdfBytes)) {
      mostrarMensaje("El programa debe ser un archivo no vacío de hasta 5 MiB.", "error");
      return null;
    }
    if (archivo && archivo.type && archivo.type !== "application/pdf") {
      mostrarMensaje("El archivo seleccionado debe ser PDF.", "error");
      return null;
    }

    let profesor = null;
    if (seleccionaProfesor) {
      profesor = Number($("#docente_curso").data("id-usuario") || 0);
      if (profesor < 1) {
        mostrarMensaje("Debe seleccionar un Profesor Aceptado de la lista.", "error");
        return null;
      }
    }

    return {
      idCurso,
      nomCurso,
      creditos,
      caracter,
      periodo,
      anioCurso,
      cargaHor,
      archivo,
      profesor,
    };
  }

  function enviarCurso(datos) {
    if (mutacionActiva) return;
    mutacionActiva = true;
    const $boton = $("#btn-guardar-curso").prop("disabled", true);
    const formData = new FormData();
    formData.append("op", datos.idCurso > 0 ? "update" : "create");
    formData.append("id_curso", String(datos.idCurso));
    formData.append("nom_curso", String(datos.nomCurso));
    formData.append("creditos", datos.creditos);
    formData.append("caracter", String(datos.caracter));
    formData.append("periodo", String(datos.periodo));
    formData.append("anio_curso", String(datos.anioCurso));
    formData.append("carga_hor", datos.cargaHor);
    if (seleccionaProfesor) formData.append("profesor", String(datos.profesor));
    if (datos.archivo) formData.append("arch_prog", datos.archivo);

    $.ajax({
      url: endpointCurso,
      type: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      data: formData,
      headers: { "X-CSRF-Token": csrfToken },
      success: function (response) {
        if (!response || response.ok !== true) {
          mostrarMensaje(
            response && response.mensaje ? response.mensaje : "No fue posible guardar el Curso.",
            "error"
          );
          return;
        }
        limpiarFormulario();
        mostrarListado();
        mostrarCursos();
        mostrarMensaje(response.mensaje, "success");
      },
      error: function (xhr) {
        mostrarMensaje(mensajeDesdeError(xhr, "No fue posible guardar el Curso."), "error");
      },
      complete: function () {
        mutacionActiva = false;
        $boton.prop("disabled", false);
      },
    });
  }

  function eliminarCurso(idCurso) {
    if (deleteActivo) return;
    deleteActivo = true;
    $(".eliminarCurso").prop("disabled", true);
    $.ajax({
      url: endpointCurso,
      type: "POST",
      dataType: "json",
      data: { op: "delete", id_curso: idCurso },
      headers: { "X-CSRF-Token": csrfToken },
      success: function (response) {
        if (!response || response.ok !== true) {
          mostrarMensaje(
            response && response.mensaje ? response.mensaje : "No fue posible eliminar el Curso.",
            "error"
          );
          return;
        }
        mostrarCursos();
        mostrarMensaje(response.mensaje, "success");
      },
      error: function (xhr) {
        mostrarMensaje(mensajeDesdeError(xhr, "No fue posible eliminar el Curso."), "error");
      },
      complete: function () {
        deleteActivo = false;
        $(".eliminarCurso").prop("disabled", false);
      },
    });
  }

  $("#btn-agr-curso").on("click", function () {
    ocultarMensaje();
    limpiarFormulario();
    mostrarFormulario();
  });

  $("#btn-ver-curso, .detalleCurso").on("click", function () {
    limpiarFormulario();
    mostrarListado();
  });

  $("#form_curso").on("submit", function (event) {
    event.preventDefault();
    ocultarMensaje();
    const datos = validarFormulario();
    if (datos) enviarCurso(datos);
  });

  $("body").on("click", ".verCurso", function () {
    const idCurso = Number($(this).attr("data-id-curso"));
    cargarDetalle(idCurso, renderDetalle);
  });

  $("#editar_curso").on("click", function () {
    if (!cursoActual || cursoActual.puede_editar !== true) return;
    cargarDetalle(cursoActual.id_curso, prepararEdicion);
  });

  $("body").on("click", ".eliminarCurso", function () {
    const $boton = $(this);
    const idCurso = Number($boton.attr("data-id-curso"));
    const nombreCurso = String($boton.data("nombre-curso") || "Curso");
    confirmarEliminacion({
      tipo: "Curso",
      nombre: nombreCurso,
      onConfirm: function () {
        eliminarCurso(idCurso);
      },
    });
  });

  $("#docente_curso").on("input", function () {
    const $input = $(this);
    $input.removeData("id-usuario");
    const busqueda = String($input.val() || "").trim();
    const $lista = $("#list_prof_curso").empty();
    if (busqueda === "") {
      $lista.hide();
      return;
    }
    if (solicitudProfesores) solicitudProfesores.abort();
    const solicitudActual = $.ajax({
      url: endpointProfesor,
      type: "POST",
      dataType: "json",
      data: { op: "read_prof", busqueda },
      success: function (profesores) {
        $lista.empty();
        if (!Array.isArray(profesores) || profesores.length === 0) {
          $lista.append($("<li>", { class: "list-group-item text-rosado" }).text("Sin coincidencias"));
        } else {
          profesores.forEach(function (profesor) {
            const nombre = [profesor.nombres, profesor.ap_pat, profesor.ap_mat]
              .filter(Boolean)
              .join(" ");
            const $opcion = $("<button>", {
              type: "button",
              class: "list-group-item list-group-item-action listProfCurso",
              "data-id-usuario": String(profesor.id_usuario),
            }).text(nombre);
            $opcion.data("nombre-profesor", nombre);
            $lista.append($opcion);
          });
        }
        $lista.show();
      },
      error: function (xhr, estado) {
        if (estado !== "abort") {
          mostrarMensaje(mensajeDesdeError(xhr, "No fue posible consultar Profesores."), "error");
        }
      },
    });
    solicitudProfesores = solicitudActual;
    solicitudActual.always(function () {
      if (solicitudProfesores === solicitudActual) {
        solicitudProfesores = null;
      }
    });
  });

  $("body").on("click", ".listProfCurso", function () {
    const $opcion = $(this);
    $("#docente_curso")
      .val(String($opcion.data("nombre-profesor") || ""))
      .data("id-usuario", Number($opcion.attr("data-id-usuario")));
    $("#list_prof_curso").empty().hide();
  });

  $("#creditos").on("input", function () {
    const creditos = Number.parseFloat(String($(this).val() || ""));
    $("#car_hor").val(Number.isFinite(creditos) ? String(Math.trunc(creditos * 28)) : "");
  });

  cargarAnios();
  cargarCatalogo();
  limpiarFormulario();
  mostrarListado();
  mostrarCursos();
  $(".loadPage").fadeOut();
})(jQuery);
