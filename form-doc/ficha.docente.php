<div class="row" id="ficha_acad">
  <h3 class=" text-center ">FICHA ACADÉMICA</h3>
  <div class="col">
  </div>
</div>
<div class="row" id="acad_doc">
  <div class="col-12" id="antAcadDoc">
    <h3 class=" text-center ">ANTECEDENTES ACADEMICOS<button type="button" class="btn  btn-dark volverInfoDoc m-3"
        id="volver">Volver</button>
    </h3>

  </div>
  <div class="col-12" id="id_usuario" name="0">
    <?php
    require '../form-doc/agr.form.dat.acad.php';
    require '../form-doc/agr.form.tesis.php';
    ?>

  </div>

</div>
<div class="row" id="edit_pers_doc">
  <div class="col" id="login" name="0">
    <h3 class="mb-5 text-center" id="text-tit">EDITAR INFORMACIÓN PERSONAL</h3>
    <form class="form" id="form_edit_pers">
      <?php require '../form-doc/agr.form.dat.pers.php'; ?>


      <div class="row justify-content-center " id="mnsj_row_per_doc">
        <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_per_doc">
        </div>
      </div>

      <div class="row mt-5 justify-content-between ">
        <div class="col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark col-6 volverInfoDoc">Volver</button>
        </div>
        <div class="col-md-4 mb-3" id="button_datos_pers">
          <button type="submit" name="btn_pers" class="col-12 btn btn-dark col-6">Editar</button>
        </div>
      </div>

  </div>
  </form>
</div>
<div class="row" id="edit_prog_doc">
  <div class="col" id="log" name="0">
    <h3 class="mb-5 text-center" id="text-tit">EDITAR INFORMACIÓN PROGRAMA</h3>
    <form class="form" id="form_edit_prog">
      <?php require '../form-doc/agr.form.dat.prog.doc.php'; ?>

      <div class="row mt-5 justify-content-between ">
        <div class="col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark col-6 volverInfoDoc">Volver</button>
        </div>
        <div class="col-md-4 mb-3" id="button_datos_pers">
          <button type="submit" name="btn_pers" class="col-12 btn btn-dark col-6">Editar</button>
        </div>
      </div>

  </div>
  </form>
</div>
<div class="row" id="edit_acad_doc">
  <div class="col" id="edit_academicos" name="0">
    <form action="" id="form_edit_grado">

      <div class="col" id="campos_grado">
      </div>
    </form>
    <form action="" id="form_edit_postdoc">

      <div class="col" id="campos_postdoc">
      </div>
    </form>
    <form action="" id="form_edit_publicacion">
      <div class="col" id="campos_publicacion">
      </div>
    </form>
    <form action="" id="form_edit_congreso">
      <div class="col" id="campos_congreso">
      </div>
    </form>
    <form action="" id="form_edit_proyecto">
      <div class="col" id="campos_proyecto">
      </div>
    </form>
    <form action="" id="form_edit_pasantia">
      <div class="col" id="campos_pasantia">
      </div>
    </form>
    <form action="" id="form_edit_tesis">
      <div class="col" id="campos_tesis">
      </div>
    </form>
    <!-- <h3 class="mb-5 text-center" id="text-tit">EDITAR INFORMACIÓN PROGRAMA</h3> -->
    <!-- <form class="form" id="form_edit_prog">

         <div class="row mt-5 justify-content-between ">
           <div class="col-md-4 mb-3">
             <button type="button" class="col-12 btn btn-dark col-6 volverInfoDoc" >Volver</button>
           </div>
           <div class="col-md-4 mb-3" id="button_datos_pers">
             <button type="submit" name="btn_pers" class="col-12 btn btn-dark col-6">Editar</button>
           </div>
         </div>

    </div>
    </form> -->
  </div>


</div>
  <div class="row" id="info_doc" name="<?php echo isset($_SESSION['docente']) ?>">
  <input type="hidden" name="prof" id="tipo_usuario">
  <h3 class=" text-center ">INFORMACIÓN DOCENTE<button type="button" id='vol-infodoc'
      class="btn  btn-dark volverTablaDoc m-3" id="volver">Volver</button>
  </h3>
  <div class="col-12 mt-3">
    <div class="card mb-3">
      <div class="card-body" id="card_ficha_doc">
        <table class="table table-striped">
          <thead>
          </thead>
          <tbody id="datos_fichadoc">
          </tbody>
        </table>

      </div>
    </div>
  </div>
  <div class="col">
    <div class="row ">
      <div class="col " id="ant_acad_doc"></div>
      <div class="row m-0 p-0" id="ficha_ant">
        <div class="row justify-content-center " id="mnsj_row_acad_doc">
          <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_acad_doc"></div>
        </div>



        <!-- incio -->
        <div class="accordion" id="accordionExample">
          <!-- grado academico -->
          <div class="accordion-item">
            <h5 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGrado"
                aria-expanded="false" aria-controls="collapseGrado">
                <H5>GRADO ACADÉMICO</H5>
              </button>
            </h5>
            <div id="collapseGrado" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_grado" name="0"></div>
              </div>
            </div>
          </div>
          <!--postdoctorado-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsePostdoc" aria-expanded="false" aria-controls="collapsePostdoc">
                <H5>POSTDOCTORADO</H5>
              </button>
            </h2>
            <div id="collapsePostdoc" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_postdoc"></div>
              </div>
            </div>
          </div>
          <!--publicacion-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsePub" aria-expanded="false" aria-controls="collapsePub">
                <H5>PUBLICACIÓN</H5>
              </button>
            </h2>
            <div id="collapsePub" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_publicacion"></div>
              </div>
            </div>
          </div>
          <!--congreso-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseCongreso" aria-expanded="false" aria-controls="collapseCongreso">
                <H5>CONGRESO</H5>
              </button>
            </h2>
            <div id="collapseCongreso" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_congreso"></div>
              </div>
            </div>
          </div>
          <!--proyecto investigacion-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseProy" aria-expanded="false" aria-controls="collapseProy">
                <H5>PROYECTO DE INVESTIGACIÓN</H5>
              </button>
            </h2>
            <div id="collapseProy" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_proyecto"></div>
              </div>
            </div>
          </div>
          <!--pasantia-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsePasant" aria-expanded="false" aria-controls="collapsePasant">
                <H5>PASANTÍA</H5>
              </button>
            </h2>
            <div id="collapsePasant" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_pasantia"></div>

              </div>
            </div>
          </div>
          <!--tesis-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseTesis" aria-expanded="false" aria-controls="collapseTesis">
                <H5>TESIS</H5>
              </button>
            </h2>
            <div id="collapseTesis" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="ficha_tesis"></div>
              </div>
            </div>
          </div>