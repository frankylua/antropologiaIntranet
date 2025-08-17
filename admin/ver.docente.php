<?php

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
      <div class="row mt-5">
        <div class="col-md-6 mb-3">
          <select id="tipo_doc" class="form-select">
            <option value="0" selected>Todos(as)</option>
            <option value="1">Claustro</option>
            <option value="2">Visitante</option>
            <option value="3">Colaborador(a)</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
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
                <th class="col-5">Nombre</th>
                <th class="col-2">Vínculo</th>
                <th class="col-2 text-center">Ficha Académica</th>
                <th class="col-2 text-center">Información</th>
                <th class="col-1 text-center">Eliminar</th>                   
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

?>
</html>





      



                        
                    


    