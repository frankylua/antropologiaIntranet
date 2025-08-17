<?php


//las variables de los datos de usuario


require('datos.pers.php')
?><div class="container">
  <div class="row mt-3 ">
    
    <div class="col" id="info_acad">
      <h3 class="cardtitle text-center">INFORMACIÓN ACADÉMICA</h3>
        
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
              <label  class="form-label mb-2">Trabaja Actualmente</label>
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
                <label for="sit_ocup" class="form-label" >Situación ocupacional previa al ingreso (cargo, lugar)</label>
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
                require('../form-doc/lineaInv.php');
            ?>
            <?php if (isset ($_SESSION['admin'])): ?>
              <div class="col-md-6 mb-3">
                  <label for="tipo_est" class="form-label">Tipo de Alumno</label>
                  <select id="tipo_est"  class="form-select">
                    
                  </select>
                  
                </div>
                <?php endif; ?>
              </div>

              <div class="row justify-content-center " id="mnsj_row_acad_est">
        <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_acad_est">
        </div>
      </div>
            <div class="row mt-5 justify-content-between ">
              <div class="col-md-4 mb-3">
                <button type="button" class="col-12 btn btn-dark col-6" id="volver_acad" >Volver</button>
              </div>
              <div class="col-md-4 mb-3 ">
                <button type="submit" class="col-12 btn btn-dark  col-6 ">Guardar</button>
              </div>
            </div>
          </div>
          </form>
          <div class="row mt-3 justify-content-center" id="antec_acad">
            <div class="col-12" id="id_usuario" name="0">
              
            <h3 class="cardtitle text-center">ANTECEDENTES ACADÉMICOS</h3>
            <?php
          require('agr.form.dat.acad.php');
          require('agr.form.beca.php');
          
          ?>
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
        require('footer.php');
        ?>

<script src="scripts/estudiante.js"></script>
</html>

  
    
          

