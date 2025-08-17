<div class="row justify-content-between" id="inst_doc">
  <div class="col-md-6 mb-3" id="id_inst_doc" name="0">
    <label for="inst_trab" class="form-label">Institución/Unidad donde trabaja</label>
    <select name="inst_unid_trabajo" id="inst_unid_trabajo" class="form-select">
    </select>
  </div>
  <div class="col-md-6 mb-3">
    <label for="cat_acad" class="form-label">Categoría Académica</label>
    <select id="cat_acad" class="form-select">
      <option selected value="0">Selecccione categoría</option>
      <option value="1">Profesor(a) instructor(a)</option>
      <option value="2">Profesor(a) asistente</option>
      <option value="3">Profesor(a) asociado(a)</option>
      <option value="4">Profesor(a) titular</option>
      <option value="5">Sin jerarquía</option>
    </select>
  </div>
</div>
<div class="row justify-content-between">
  <div class="col-md-6 mb-3">
    <label for="anio_ing" class="form-label">Año de Ingreso al programa</label>
    <select name="year" class="form-select" id="anio_ing">
    </select>
  </div>
  <div class="col-md-6 mb-3">
    <label for="vinculo" class="form-label">Vínculo con el programa</label>
    <select id="vinculo" class="form-select">
      <option selected value="0">Seleccione vínculo</option>
      <option value="1">Claustro</option>
      <option value="2">Visitante</option>
      <option value="3">Colaborador(a)</option>
    </select>
  </div>
</div>
<div class="row justify-content-between">
  <?php
  require '../form-doc/lineaInv.php';
  ?>
  <!-- validar permiso otorgar permisos de sesion -->
  <?php if (isset ($_SESSION['admin'])): ?>
    <div class="col-md-6 mb-3">
      <label for="permiso" class="form-label">Permiso de Sesión del comite</label>
      <select id="permiso" class="form-select">
        <option selected value="0">Seleccione</option>
        <option value="1">Administrador(a)</option>
        <option value="2">Comite Académico</option>
        <option value="3">Docente</option>
      </select>
    </div>
    <?php endif; ?>
  </div>
    

<!-- <div class="row">
            <div class="col-auto">
              <input type="checkbox" name="" id="acepto" class="form-group">
              <button class="btn btn-link link-dark">Acepto términos y condiciones</button>
            </div>
          </div> -->
<div class="row justify-content-center " id="mnsj_row_prog_doc">
  <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_prog_doc">
  </div>
</div>