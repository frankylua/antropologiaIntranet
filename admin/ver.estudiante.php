<?php

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite'])) {
  header('Location:../index.php');
} else {
  require ('../form-doc/header.php');
  ?>

  <div class="container mt-5">
    <span class="loadPage">
      <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
    </span>
    <div class="row" id="lista_est">
      <div class="col">
        <h3 class=" text-center"> ESTUDIANTES</h3>
        
        <div class="row justify-content-center " id="mnsj_row_encabezado">
          <div class="col-lg-8 alert  mt-3 text-center alert-danger" role="alert" id="mnsj_encabezado"></div>
        </div>
       
        <div class="row mt-3 g-3 m-5">
          <div class="col-md-6 mb-3">
            <select id="tipo_est" class="form-select">
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <div class="row justify-content-end">
              <div class="col-8">
                <input type="text" class="form-control" placeholder="Buscar" id="buscar_est">
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-5">
          <div class="col mb-3">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="col-5">Nombres</th>
                  <th class="col-3 text-center">Estado</th>
                  <th class="col-2 text-center">Cambiar Estado</th>
                  <th class="col-1 text-center">Información</th>
                  <th class="col-1 text-center">Eliminar</th>
                </tr>
              </thead>
              <tbody id="table_est">
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
            </div>
           -->
      </div>
    </div>

    <?php
    require '../form-doc/ficha.estudiante.php';
    require ('../form-doc/footer.php');
    ?>
    <script src="../form-doc/scripts/ficha.estudiante.js"></script>
    <script src="scripts/ver.estudiante.js"></script>
    <?php
}


?>
</html>