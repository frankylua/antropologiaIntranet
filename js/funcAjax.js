function templateListas(listas,id) {
      let template = "";
      listas.forEach((list) => {
        template += `
        <li class='list-group-item'> 
        <div class='row justify-content-between'>
        <div class='col-auto '>
        ${cadenaMay(list[1])} 
        </div>
        <div class='col-auto p-0 m-0'>
        <button type="button" class='btn btn-link link-success btn-sm editar_lista' name='${
          list[1]
        }'  id='${list[0]}'>Editar</button>
       
      </div>
      </div>
      </li>
      `;
    });
    $(id).html(template);
}


function templateSelect(listas,id) {

      let template = "";
      template = `<option value="0">${titulo}</option>`;
      listas.forEach((p) => {
        template += `<option value="${p[0]}"> ${cadenaMay(p[1])}</option>`;
      });
      if (op !== "pais" && op !== "read_cursos") {
        template += '<option value="otro">Otro</option>';
      }
      $(id).html(template);


}
function cargarListas(n_input) {
  $("#oculto").val("");
  op = "read";
  if (n_input == "pueb") {
      // // $.ajax({
      // // url: '../ajax/pueblo.php',
      // // type: "POST",
      // // data: { op: op },
      // // success: function (response) {
      // //   console.log('la respuesta es ')
      // //   console.log(response)
      // // let listas = JSON.parse(response);
        $.post( '../ajax/pueblo.php', {op}, function (response) {
        let template = "";
        console.log(response)
        response.forEach((list) => {
          template += `
          <li class='list-group-item'> 
          <div class='row justify-content-between'>
          <div class='col-auto '>
          ${cadenaMay(list[1])} 
          </div>
          <div class='col-auto p-0 m-0'>
          <button type="button" class='btn btn-link link-success btn-sm editar_lista' name='${
            list[1]
          }'  id='${list[0]}'>Editar</button>
         
        </div>
        </div>
        </li>
        `;
        });
        $('#listas').html(template);

      })

  }




 // -------------------------------------------------------------
  if (
    n_input == "lic" ||
    n_input == "un" ||
    n_input == "mag" ||
    n_input == "doc"
  ) {
    ajaxListas("#listas", "../ajax/titulo.php", op, n_input);
  }
  if (n_input == "inst") {
    ajaxListas("#listas", "../ajax/institucion.php", "read");
  }
  if (n_input == "bec_ext" ||n_input == "bec_int" ) {
    ajaxListas("#listas", "../ajax/beca.php", 'read_lista', n_input);
  }
  if (n_input == "financ") {
    ajaxListas("#listas", "../ajax/financiamiento.php", "read");
  }
}
function eliminarLista(id, n_input) {
  op = "delete";
  const dato_lista = {
    id: id,
    op: op,
  };
  if (n_input == "pueb") {
    $.post("../ajax/pueblo.php", dato_lista, function (response) {
      cargarListas(n_input);
    });
  }
  if (
    n_input == "lic" ||
    n_input == "un" ||
    n_input == "mag" ||
    n_input == "doc"
  ) {
    $.post("../ajax/titulo.php", dato_lista, function (response) {
      cargarListas(n_input);
    });
  }
  if (n_input == "inst") {
    $.post("../ajax/institucion.php", dato_lista, function (response) {
      cargarListas(n_input);
    });
  }
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
