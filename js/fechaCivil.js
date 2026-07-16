function fechaCivil(dato) {
  if (dato === null || dato === undefined) {
    return {
      valor: null,
      estado: "no_informado",
      presentacion: "No informado"
    };
  }

  if (typeof dato !== "string") {
    return {
      valor: null,
      estado: "invalido",
      presentacion: "Dato inválido"
    };
  }

  var valor = dato.replace(/^\s+|\s+$/g, "");
  if (valor === "") {
    return {
      valor: null,
      estado: "no_informado",
      presentacion: "No informado"
    };
  }

  var partes = /^(\d{4})-(\d{2})-(\d{2})$/.exec(valor);
  if (partes === null) {
    return {
      valor: valor,
      estado: "invalido",
      presentacion: "Dato inválido"
    };
  }

  var anio = Number(partes[1]);
  var mes = Number(partes[2]);
  var dia = Number(partes[3]);
  var bisiesto = anio % 4 === 0 && (anio % 100 !== 0 || anio % 400 === 0);
  var diasPorMes = [31, bisiesto ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
  var fechaValida = anio >= 1 && mes >= 1 && mes <= 12 && dia >= 1 && dia <= diasPorMes[mes - 1];

  if (!fechaValida) {
    return {
      valor: valor,
      estado: "invalido",
      presentacion: "Dato inválido"
    };
  }

  return {
    valor: valor,
    estado: "valido",
    presentacion: partes[3] + "/" + partes[2] + "/" + partes[1]
  };
}
