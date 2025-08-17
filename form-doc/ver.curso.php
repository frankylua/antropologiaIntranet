<?php

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite']) && !isset($_SESSION['aceptado']) && !isset($_SESSION['docente'])) {
  header('Location:../index.php');
} else {
  require ('header.php');
  ?>
  <span class="loadPage">
    <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
  </span>

  <div class="container mt-5">
    <div class="row">
      <div class="col" id="list_curso">

        <h3 class=" text-center titulo_curso">CURSOS
          <button class="btn btn-dark text-light px-3 m-3" id="btn-agr-curso">Agregar Registro</button>

        </h3>
        <div class="row justify-content-center p-0" id="mnsj_elim">
          <div class="col-lg-8 alert mt-3 text-center  pb-0 fs-6" role="alert" id="eliminado">

          </div>
        </div>
        <div class="row mt-5 g-3 m-5">
          <div class="col mb-3">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="col-8">Nombre</th>
                  <th class="col-8">Periodo</th>
                  <th class="col-8">Año</th>
                  <th class="col-1">Descripción</th>
                  <th class="col-1">Eliminar</th>
                </tr>
              </thead>
              <tbody id="datos_curso">
              </tbody>
            </table>
          </div>
        </div>

        <div class="row ">
          <div class="col">
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center ">
                <li class="page-item">
                  <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li class="page-item text-dark"><a class="page-link" href="#">1</a></li>
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
      </div>
    </div>




    <div class="row">
      <div class="col" id="form_curso">
        <h3 class="card-title text-center mb-5">INGRESAR CURSO</h3>
        <form class=" g-3 m-5" method='post' id='form_curso' enctype="multipart/form-data">
          <div class="row ">
            <div class="col-md-6 mb-3">
              <input type='hidden' id='id_curso' value="0"></input>
              <label for="nom_curso" class="form-label">Nombre Curso</label>
              <select id="nom_curso" class="form-select">

              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label for="creditos" class="form-label">Créditos SCT</label>
              <input type="number" class="form-control" id="creditos">
            </div>
          </div>
          <div class="row ">
            <div class="col-md-6 mb-3">
              <label for="caracter" class="form-label">Carácter</label>
              <select id="caracter" class="form-select">
                <option selected value="0">Seleccione</option>
                <option value="1">Obligatorio</option>
                <option value="2">Seminario</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="periodo" class="form-label">Periodo</label>
              <select id="periodo" class="form-select">
                <option selected value="0">Seleccione</option>
                <option value="1">I Semestre</option>
                <option value="2">II Semestre</option>
                <option value="3">III Semestre</option>
              </select>
            </div>
          </div>
          <div class="row">

            <div class="col-md-6 mb-3">
              <label for="anio_curso" class="form-label">Año</label>
              <select class="form-select" id="anio_curso">
              </select>
            </div>


            <div class="col-md-6 mb-3">
              <label for="docente_curso" class="form-label">Docente</label>
              <input type="text" class="form-control" id="docente_curso">
              <ul class="list-group" id="list_prof_curso"></ul>
            </div>
          </div>
          <div class='row'>
            <div class="col-md-6 mb-3">
              <label for="prog_curso" class="form-label">Adjuntar Programa del Curso</label>
              <input type="file" class="form-control" id="prog_curso" accept="pdf">
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
              <label for="car_hor" class="form-label">Carga Horaria (horas cronológicas)</label>
              <input type="number" class="form-control" id="car_hor">
            </div>
          </div>
          <div class="row justify-content-center p-0" id="mnsj_row">
            <div class="col-lg-8 alert  mt-3 text-center pb-0 fs-6" role="alert" id="mnsj">
            </div>
          </div>

          <div class="row mt-5 justify-content-between ">
            <div class="col-6 col-md-4 mb-3">
              <button type="button" class="col-12 btn btn-dark col-6" id="btn-ver-curso">Cancelar</button>
            </div>
            <div class="col-6 col-md-4 mb-3 ">
              <button type="submit" class="col-12 btn btn-dark col-6">Guardar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="row mt-5 justify-content-center" id="det_curso">
      <div class="col-10">

        <table class="table" id="ficha">
          <thead>
            <tr>
              <td class="col-auto">Nombre Curso</td>
              <td class="col-auto" id="nom"></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="col-auto">Créditos</th>
              <td class="col-auto" id="cred">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Carácter</th>
              <td class="col-auto" id="car">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Periodo</th>
              <td class="col-auto" id="per">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Año</th>
              <td class="col-auto" id="year">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Carga Horaria</th>
              <td class="col-auto" id="carg">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Docente</th>
              <td class="col-auto" id="prof">
                </th>
            </tr>
            <tr>
              <td class="col-auto">Programa del Curso</th>
              <td class="col-auto" id="prog">
                </th>
            </tr>
          </tbody>
        </table>
        <div class="row mt-5 justify-content-between ">
          <div class="col-6 col-md-4 mb-3">
            <button type="button" class="col-12 btn btn-dark col-6 detalleCurso">Volver</button>
          </div>
          <div class="col-6 col-md-4 mb-3 ">
            <button type="submit" class="col-12 btn btn-dark col-6" id="editar_curso">Editar</button>
          </div>
        </div>

      </div>
    </div>
    <?php
    require ('footer.php');
    ?>
    <script src="scripts/curso.js"></script>
    <?php
}

?>
</html>