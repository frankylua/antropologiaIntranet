//listar años

function mostrarFecha(dato) {
  fecha = new Date(dato);
  dia = fecha.getDate() < 10 ? "0" + fecha.getDate() : fecha.getDate();
  mes = fecha.getMonth() < 10 ? "0" + fecha.getMonth() : fecha.getMonth();
  return dia + "/" + mes + "/" + fecha.getFullYear();
}
function otroCampo(idInput, idCol, idSelect, titulo, max) {
  if ($(idSelect).val() == "otro") {
    $(idCol).html(
      '<input type="text" class="form-control mb-3" id="' +
        idInput +
        '" placeholder="' +
        titulo +
        '" maxlength="' +
        max +
        '">'
    );
  }
}
function anios(id_select, inicio) {
  var fecha = new Date();
  var fin = fecha.getFullYear();
  $(id_select).append("<option value='0'>Seleccione</option>");
  for (i = fin; i >= inicio; i--) {
    $(id_select).append("<option value='" + i + "'>" + i + "</option>");
  }
}
function anioInform(id_select, inicio, tipo) {
  var fecha = new Date();
  var fin = fecha.getFullYear();
  $(id_select).append("<option value='0'>" + tipo + "</option>");
  for (i = inicio; i <= fin; i++) {
    $(id_select).append("<option value='" + i + "'>" + i + "</option>");
  }
}
function agregarInput(id_select, id_input, row, col, nombre) {
  $("#" + col).remove();
  if ($(id_select).val() == "1") {
    $(row).append(
      '<div class="col mb-3" id="' +
        col +
        '"><input type="text" class="form-control" id="' +
        id_input +
        '" placeholder="' +
        nombre +
        '" ></div>'
    );
  }
}
function agregarInputDinamico(id_select, id_input, row, col, nombre) {
  $("#" + col).remove();
  if ($(id_select).val() == "1") {
    $(row).append(
      '<div class="col mb-3" id="' +
        col +
        '"><input type="text" class="form-control" id="' +
        id_input +
        '" placeholder="' +
        nombre +
        '" ></div>'
    );
  }
}
//Agregar Articulo/Edicion Revista
function agregarArtRev() {
  $("#id_pub").append(
    '<div class="row justify-content-between" id="row_pub"><div class="col" id="pub_col"></div></div></div>'
  );
  $("#pub_col").append(
    '<div class="row "> <div class="col-md-8 mb-3"><label for="titulo" class="form-label">Título</label><input type="text" class="form-control " id="titulo"></div><div class="col-md-4 mb-3"><label for="anio" class="form-label">Año</label><select  class="form-select anio" id="anio"><select/></div></div>'
  );
  $("#pub_col").append(
    '<div class="row"><div class="col mb-3"><div class="card"><div class="card-body"><div class="row justify-content-between"><div class="col-auto mb-3"> <h6 class="card-title fs-5">Otros/as Autores/as</h6></div></div> <div class="row"><div class="col-md-12 mb-3"><label for="autores" class="form-label">(Ingresar nombres separados por una coma)</label><textarea class="form-control " id="autores" rows="3"></textarea></div> </div> </div> </div></div></div>'
  );
  $("#pub_col").append(
    '<div class="row"><div class="col-md-6 mb-3"> <label for="nombre" class="form-label">Nombre Revista</label><input type="text" class="form-control " id="nombre"></div><div class="col-md-6 mb-3 "><label for="indizacion" class="form-label">Indización</label><select id="indizacion" class="form-select indiz"><option selected value="0">Tipo</option><option>Wos</option><option>Scopus</option><option>Erih Plus</option><option>Scielo</option><option>Latindex</option><option>Otra</option><option>No tiene</option></select></div></div>'
  );
  $("#pub_col").append(
    '<div class="row"><div class="col-md-6 mb-3 "><label for="estado" class="form-label">Estado</label><select id="estado" class="form-select est"><option selected value="0">Seleccione Estado</option><option value="1">Publicada</option><option value="2">En Prensa</option><option value="3">Aceptada</option><option value="4">Enviada</option></select></div><div class="col-md-6 mb-3"><label for="issn" class="form-label">Issn</label><input type="number" class="form-control " id="issn"></div>'
  );
  $("#pub_col").append(
    '<div class="row"><label for="fact_imp" class="form-label">Factor de Impacto</label><div class="col-md-6 mb-3"><input type="number" step="any" class="form-control fact_imp" id="fact_imp" name="factor">></div><div class="col-md-6 mb-3 align-self-center"><div class="form-check"><input type="radio" class="form-check-input no_fac"  id="no_fac" name="no_fac"><label for="no_fac" class="form-check-label">No tiene Factor de Impacto</label></div></div>'
  );
  anios("#anio", 2006);
}
function agregarListas(id, objeto) {
  for (o in objeto) {
    $(id).append("<option >" + ojeto[o] + "</option>");
  }
}
//funcion para quitar espacios a los nombres y dividir por las comas
function guardar(cadena) {
  cadena = cadena.trimStart();
  cadena = cadena.trimEnd();
  cadena = cadena.toLowerCase();
  return cadena;
}
function espacios(cadena) {
  cadena = cadena.trimStart();
  cadena = cadena.trimEnd();
  return cadena;
}
function guardarAutores(autores) {
  palabras = autores.split(",");
  for (let i = 0; i < palabras.length; i++) {
    palabras[i] = guardar(palabras[i]);
  }
  return palabras.join(",");
}
function letraMay(palabra) {
  palabra = palabra[0].toUpperCase() + palabra.substr(1);
  return palabra;
}
//funcion para mostrar autores
function cadenaMay(cadena) {
  palabras = cadena.split(" ");
  for (let i = 0; i < palabras.length; i++) {
    ascii = palabras[i][0].toUpperCase().charCodeAt(0);
    if (ascii > 64 && ascii < 91) {
      romanos = [
        "C",
        "CC",
        "CCC",
        "CD",
        "D",
        "DC",
        "DCC",
        "DCCC",
        "CM",
        "X",
        "XX",
        "XXX",
        "XL",
        "L",
        "LX",
        "LXX",
        "LXXX",
        "XC",
        "I",
        "II",
        "III",
        "IV",
        "V",
        "VI",
        "VII",
        "VIII",
        "IX",
      ];
      if (
        palabras[i] != "de" &&
        palabras[i] != "del" &&
        palabras[i] != "of" &&
        palabras[i] != "in" &&
        palabras[i] != "en" &&
        palabras[i] != "la" &&
        palabras[i] != "las" &&
        palabras[i] != "y"
      ) {
        romanos.forEach((nros) => {
          nros = nros.toLowerCase();
          if (palabras[i] == nros) {
            palabras[i] = palabras[i].toUpperCase();
          }
        });
        palabras[i] = palabras[i][0].toUpperCase() + palabras[i].substr(1);
      }
    }
  }
  palabras[0] = letraMay(palabras[0]);
  return palabras.join(" ");
}
function mostrarAutores(autores) {
  autor = autores.split(",");
  for (let i = 0; i < autor.length; i++) {
    autor[i] = cadenaMay(autor[i]);
  }
  return autor.join(", ");
}
function validNomProf(cadena) {
  palabra = cadena.split(" ");
  return palabra;
}
