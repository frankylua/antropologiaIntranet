function mostrarMensajeCRUD({ contenedor, mensaje, tipo }) {
  if (typeof contenedor !== "string" || typeof mensaje !== "string") {
    return;
  }

  if (tipo !== "success" && tipo !== "danger") {
    return;
  }

  const $contenedor = $(contenedor);
  const $mensaje = $contenedor.find(".alert").first();

  if (!$contenedor.length || !$mensaje.length) {
    return;
  }

  const temporizadorAnterior = $contenedor.data("mensajeCrudTimeout");
  if (temporizadorAnterior) {
    clearTimeout(temporizadorAnterior);
  }

  $mensaje
    .removeClass("alert-success alert-danger")
    .addClass(`alert-${tipo}`)
    .text(mensaje);

  $contenedor.stop(true, true).removeClass("d-none").show();

  const temporizador = setTimeout(function () {
    $contenedor.fadeOut(1500);
  }, 3000);

  $contenedor.data("mensajeCrudTimeout", temporizador);
}
