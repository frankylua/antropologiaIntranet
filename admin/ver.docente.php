<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
    ob_start();
    if (strlen(session_id()) < 1){
        session_start();//Validamos si existe o no la sesión
    }
    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite'])){
        header('Location:../index.php');
    }else
    {
    require('../form-doc/header.php')
?>
<span class="loadPage">
      <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
  </span>
<div class="container mt-5">
  <div class="row" id="lista_doc">
    <div class="col g-3 m-5">
      <h3 class=" text-center "> DOCENTES</h3>      
      <div class="alert d-none" id="mensaje_estado_docente" role="alert"></div>
      <div class="row mt-5">
        <div class="col-md-4 mb-3">
          <select id="tipo_doc" class="form-select">
            <option value="0" selected>Todos(as)</option>
            <option value="1">Claustro</option>
            <option value="2">Visitante</option>
            <option value="3">Colaborador(a)</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <select id="estado_profesor_filtro" class="form-select">
            <option value="0" selected>Todos los estados</option>
            <option value="1">Pendientes</option>
            <option value="2">Aceptados</option>
            <option value="3">Rechazados</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <div class="row justify-content-end">
            <div class="col-8">
              <input type="text" class="form-control" placeholder="Buscar" id="buscar_doc" >
            </div> 
          </div>
        </div> 
      </div>
      <div class="row mt-5">
        <div class="col mb-3">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Vínculo</th>
                <th>Estado</th>
                <th class="text-center">Acciones estado</th>
                <th class="text-center">Ficha Académica</th>
                <th class="text-center">Información</th>
                <th class="text-center">Eliminar</th>
              </tr>
            </thead>
            <tbody id="table_doc">
            </tbody>
          </table>
        </div>
      </div>
                
          <!-- <div class="row ">
            <div class="col">
              <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                  <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  </li>
                </ul>
              </nav>

            </div>
          </div> -->
          
      </div>
  </div>
  
<?php require '../form-doc/ficha.docente.php';?>

  
</div>
  <!-- fin -->
            </div>
            </div>
        </div>
    </div>
  </div>


<?php
    require('../form-doc/footer.php')
?>
<script src="../form-doc/scripts/ficha.docente.js"></script>
<script src="scripts/ver.docente.js"></script>
<?php
}
ob_end_flush();
?>





      



                        
                    


    