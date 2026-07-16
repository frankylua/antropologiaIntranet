function confirmarEliminacion({ tipo, nombre, onConfirm }) {
  if (typeof tipo !== "string" || typeof nombre !== "string" || typeof onConfirm !== "function") {
    return;
  }

  const modalElement = document.createElement("div");
  modalElement.className = "modal fade";
  modalElement.tabIndex = -1;
  modalElement.setAttribute("aria-labelledby", "confirmDeleteTitle");
  modalElement.setAttribute("aria-hidden", "true");
  modalElement.innerHTML = `
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteTitle">Eliminar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p class="confirm-delete-message"></p>
          <p class="mb-0">Esta acción no puede deshacerse.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger confirm-delete-button">Eliminar</button>
        </div>
      </div>
    </div>
  `;

  modalElement.querySelector(".confirm-delete-message").textContent =
    `¿Está seguro que desea eliminar ${tipo}: "${nombre}"?`;

  document.body.appendChild(modalElement);

  const modal = new bootstrap.Modal(modalElement);
  const confirmButton = modalElement.querySelector(".confirm-delete-button");

  confirmButton.addEventListener("click", function () {
    modal.hide();
    onConfirm();
  }, { once: true });

  modalElement.addEventListener("hidden.bs.modal", function () {
    modal.dispose();
    modalElement.remove();
  }, { once: true });

  modal.show();
}
