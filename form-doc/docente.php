<?php
require ('datos.pers.php');
?>
<div class="container mt-5">
  <div class="row" id="info_acad">
    <div class="col">
      <h3 class=" mb-5 text-center ">INFORMACIÓN ACADÉMICA</h3>
      <?php
      require ('../form-doc/agr.form.dat.prog.doc.php');
      ?>
      <div class="row mt-5 justify-content-between ">
        <div class="col-6 col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark col-6 prog_prof_volver">Volver</button>
        </div>
        <div class="col-6 col-md-4 mb-3 ">
          <button type="submit" class="col-12 btn btn-dark col-6">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  </form>
  <div class="row mt-3 justify-content-center" id="antec_acad">
    <div class="col-12" id="id_usuario" name="0">
      <h3 class="cardtitle text-center">ANTECEDENTES ACADÉMICOS</h3>
      <?php
      require ('agr.form.dat.acad.php');
      ?>
      <div class='row '>
        <div class="col-12" id="tesis_card">

        </div>
        <form action="" id="form_tesis">
          <div class="col" id='tesis'>
          </div>
        </form>
      </div>
      <div class="row" id="boton_tesis">
        <div class=" col d-grid gap-2 mb-3">
          <button class="btn btn-outline-dark" id="btn_tesis" name="btn_tesis" type="button">Agregar Tesis</button>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-3 mt-3 ">
      <button type="button" class=" col-12 btn btn-dark " id="salir_form">Salir</button>
    </div>
  </div>
</div>
</div>
</div>
</div>
<?php
require ('footer.php');
?>
<script src="scripts/docente.js"></script>
</html>
