function ajaxListas(id, url, op, tipo, propiedadId, propiedadEtiqueta, permitirEliminar) {
  $.ajax({
    url: url,
    type: "POST",
    data: { op: op, tipo: tipo },
    success: function (response) {
      let listas = JSON.parse(response);
      let template = "";
      listas.forEach((list) => {
        let idLista = propiedadId ? list[propiedadId] : list[0];
        let etiquetaLista = propiedadEtiqueta ? list[propiedadEtiqueta] : list[1];
        template += `
        <li class='list-group-item'> 
        <div class='row justify-content-between'>
        <div class='col-auto '>
        ${cadenaMay(etiquetaLista)}
        </div>
        <div class='col-auto p-0 m-0'>
        <button type="button" class='btn btn-link link-success btn-sm editar_lista' name='${
          etiquetaLista
        }'  id='${idLista}'>Editar</button>
        ${permitirEliminar ? `<button type="button" class='btn btn-link link-danger btn-sm eliminar_lista' id='${idLista}'>Eliminar</button>` : ""}
       
      </div>
      </div>
      </li>
      `;
      });
      $(id).html(template);
     },
  });
}


function ajaxSelect(
  id,
  url,
  titulo,
  op,
  tipo,
  propiedadId,
  propiedadEtiqueta
) {
  $.ajax({
    async:false,
    url: url,
    type: "POST",
    data: { op, tipo },
    success: function (response) {
      let listas = JSON.parse(response);
      let template = "";
      template = `<option value="0">${titulo}</option>`;
      listas.forEach((p) => {
        let idOpcion = propiedadId ? p[propiedadId] : p[0];
        let etiquetaOpcion = propiedadEtiqueta ? p[propiedadEtiqueta] : p[1];
        template += `<option value="${idOpcion}"> ${cadenaMay(etiquetaOpcion)}</option>`;
      });
      if (op !== "pais" && op !== "read_cursos") {
        template += '<option value="otro">Otro</option>';
      }
      $(id).html(template);
    },
  });

}
function cargarListas(n_input) {
  $("#oculto").val("");
  op = "read";
  if (n_input == "pueb") {
    ajaxListas("#listas", "../ajax/pueblo.php", op, undefined, "id_pueblo", "pueblo", true);
  }
  if (
    n_input == "lic" ||
    n_input == "un" ||
    n_input == "mag" ||
    n_input == "doc"
  ) {
    ajaxListas(
      "#listas",
      "../ajax/titulo.php",
      op,
      n_input,
      "id_titulo",
      "tit_grado",
      true
    );
  }
  if (n_input == "inst") {
    ajaxListas(
      "#listas",
      "../ajax/institucion.php",
      "read",
      undefined,
      "id_inst",
      "inst"
    );
  }
  if (n_input == "bec_ext" ||n_input == "bec_int" ) {
    ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input, "id_nom_beca", "beca");
  }
  if (n_input == "financ") {
    ajaxListas("#listas", "../ajax/financiamiento.php", "read", undefined, "id_financ", "financiamiento");
  }
}
function eliminarLista(id, n_input, descripcion) {
  if (n_input !== "pueb" && !["lic", "un", "mag", "doc"].includes(n_input)) {
    return;
  }
  const tipoRegistro = n_input === "pueb" ? "Pueblo" : "Título académico";
  const descripcionRegistro = typeof descripcion === "string" ? descripcion.trim() : "";
  confirmarEliminacion({
    tipo: tipoRegistro,
    nombre: descripcionRegistro,
    onConfirm: function () {
      const dato_lista = {
        id: id,
        op: "delete",
        tipo: n_input,
      };
      const endpoint = n_input === "pueb" ? "../ajax/pueblo.php" : "../ajax/titulo.php";
      $.ajax({
        url: endpoint,
        type: "POST",
        data: dato_lista,
        dataType: "json",
        success: function (response) {
          mostrarMensajeCRUD({
            contenedor: "#mnsj_row_listas",
            mensaje: response.mensaje,
            tipo: response.ok ? "success" : "danger",
          });
          if (response.ok) {
            cargarListas(n_input);
          }
        },
        error: function (xhr) {
          const response = xhr.responseJSON;
          mostrarMensajeCRUD({
            contenedor: "#mnsj_row_listas",
            mensaje: response && response.mensaje ? response.mensaje : "No fue posible eliminar el registro",
            tipo: "danger",
          });
        },
      });
    },
  });
}
function mostrarPais(id) {
  $.get("../ajax/pais.php", function (response) {
    let listas = JSON.parse(response);
    let template = "";
    template = `<option value="0">Seleccione País</option>`;
    listas.forEach((p) => {
      template += `<option value="${p[0]}"> ${p[1]}</option>`;
    });
    template += '<option value="otro">Otro</option>';
    $(id).html(template);
  });
}
function mostrarTabla(id, url, tipo) {
  $.ajax({
    url: url,
    type: "POST",
    data: { tipo },
    success: function (response) {
      let listas = JSON.parse(response)
      let template = "";
      listas.forEach((list) => {
        template += `
            <li class='list-group-item'> 
            <div class='row justify-content-between'>
            <div class='col-auto '>
            ${list[1]} 
            </div>
            <div class='col-auto p-0 m-0'>
            <button type="button" class='btn btn-link link-success btn-sm editar_lista' name='${list[1]}'  id='${list[0]}'>Editar</button>
            <button type="button" class='btn btn-link link-danger btn-sm eliminar_lista'  id='${list[0]}'>Eliminar</button>
          </div>
          </div>
          </li>
          `;
      });
      $(id).html(template);
    },
  });
}
