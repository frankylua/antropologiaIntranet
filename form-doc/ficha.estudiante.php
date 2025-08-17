
<div class="row" id="acad_est">
  <div class="col-12" id="antAcadEst">
    <h3 class=" text-center ">ANTECEDENTES ACADEMICOS<button type="button" class="btn  btn-dark volverInfoEst m-3"
        id="volver">Volver</button>
    </h3>

  </div>
  <div class="col-12" id="id_usuario" name="0">
    <?php
    require ('../form-doc/agr.form.dat.acad.php');
    require ('../form-doc/agr.form.beca.php');
    ?>

  </div>

</div>

<div class="row" id="edit_pers_est">
  <div class="col" id="login" name="0">
    <h3 class="mb-5 text-center" id="text-tit">EDITAR INFORMACIÓN PERSONAL</h3>
    <form class="form" id="form_edit_pers">
      <?php require ('../form-doc/agr.form.dat.pers.php'); ?>
      <div class="row justify-content-center " id="mnsj_row_per_est">
        <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_per_est">
        </div>
      </div>
      <div class="row mt-5 justify-content-between ">
        <div class="col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark col-6 volverInfoEst">Volver</button>
        </div>
        <div class="col-md-4 mb-3" id="button_datos_pers">
          <button type="submit" name="btn_pers" class="col-12 btn btn-dark col-6">Editar</button>
        </div>
      </div>
  </div>
  </form>
</div>

<div class="row" id="edit_prog_est">
  <div class="col" id="login_est" name="0">
    <h3 class="mb-5 text-center" id="text-tit">EDITAR INFORMACIÓN PROGRAMA</h3>
    <form class="form" id="form_edit_prog">
      <div class="row justify-content-between mt-5">
        <div class="col-md-3 mb-3">
          <label for="promedio" class="form-label">Promedio de notas Pregrado</label>
          <input type="number" step='any' class="form-control" id="promedio" name='promedio' min="1" max="7">
        </div>
        <div class="col-md-6 mb-3" id="id_inst_est" name="0">
          <label for="inst_unid" class="form-label">Institución/Unidad Académica</label>
          <select id="inst_unid" name='inst_unid' class="form-select inst_unid">
          </select>
        </div>


        <div class="col-md-6" id="otra_inst">

        </div>
      </div>

      <div class="row" id="row_otro_inst">
        <div class="col-md-6  order-12 mb-3">
          <label class="form-label mb-2">Trabaja Actualmente</label>
          <div class="row" id="trabaja">
            <div class="col-auto">
              <div class="form-check   ms-2">
                <input type="radio" class="form-check-input trabaja" name="trab" id="si_trab" value="1">
                <label for="si_trab" class="form-check-label">Sí</label>
              </div>
            </div>
            <div class="col-auto">
              <div class="form-check  ms-2">
                <input type="radio" class="form-check-input trabaja" name="trab" id="no_trab" value="0">
                <label for="no_trab" class="form-check-label">No</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="orient" class="form-label">Orientación Disciplinar</label>
          <select id="orient" name='orient' class="form-select">
            <option selected value="0">Seleccione</option>
            <option value='1'>Antropología Social</option>
            <option value='2'>Arqueología</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label for="prof_guia" class="form-label">Profesor/a Guía</label>
          <input type="text" class="form-control" id="prof_guia" name="0">
          <ul class="list-group" id="list_prof">
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="sit_ocup" class="form-label">Situación ocupacional previa al ingreso (cargo, lugar)</label>
          <textarea class="form-control" id="sit_ocup" name='sit_ocup' rows="3" maxlength="300"></textarea>
          <div class="row" id="val_sit_ocup"></div>
        </div>
      </div>

      <div class="row MB-5">
        <div class="col-md-6 mb-3">
          <label for="fech_ing" class="form-label">Fecha de Ingreso</label>
          <input type="date" class="form-control" id="fech_ing">
        </div>
        <div class="col-md-6 mb-3">
          <label for="fech_grad" class="form-label">Fecha de Graduación</label>
          <input type="date" class="form-control" id="fech_grad">
        </div>
      </div>

      <div class="row">
        <?php
        require ('../form-doc/lineaInv.php');
        ?>
        
      </div>

      <div class="row justify-content-center " id="mnsj_row_acad_est">
        <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_acad_est">
        </div>
      </div>



      <div class="row mt-5 justify-content-between ">
        <div class="col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark col-6 volverInfoEst">Volver</button>
        </div>
        <div class="col-md-4 mb-3" id="button_datos_pers">
          <button type="submit" name="btn_pers" class="col-12 btn btn-dark col-6">Editar</button>
        </div>
      </div>
  </form>
</div>
</div>
<div class="row" id="edit_acad_est">
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

<div class="row" id="info_est" name="<?php echo isset($_SESSION['estudiante'])  ?>">
  <input type="hidden"  id="tipo_usuario" name="est">

  <h3 class=" text-center ">INFORMACIÓN ESTUDIANTE<button type="button" id='vol-infoest'
      class="btn  btn-dark volverTablaEst m-3" id="volver">Volver</button>
  </h3>

  <div class="col-12 mt-3">
    <div class="card mb-3">
      <div class="card-body" id="card_ficha_est">
        <table class="table table-striped">
          <thead>
          </thead>
          <tbody id="datos_fichaest">
          </tbody>
        </table>

      </div>
    </div>
  </div>
  <div class="col">
    <div class="row ">
      <div class="col " id="ant_acad_est"></div>
      <div class="row m-0 p-0" id="ficha_ant">
       



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
                <div class="col-12" id="grad_est" name="0"></div>
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
                <div class="col-12" id="postdoc_est"></div>
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
                <div class="col-12" id="publi_est"></div>
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
                <div class="col-12" id="cong_est"></div>
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
                <div class="col-12" id="proy_est"></div>
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
                <div class="col-12" id="pasant_est"></div>

              </div>
            </div>
          </div>
          <!--tesis-->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapsePasant" aria-expanded="false" aria-controls="collapsePasant">
                <H5>BECA</H5>
              </button>
            </h2>
            <div id="collapsePasant" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="col-12" id="beca_est"></div>

              </div>
            </div>
          </div>
         