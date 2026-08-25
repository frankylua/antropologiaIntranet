<?php
declare(strict_types=1);

use App\Security\Authorization;

require_once __DIR__ . '/../src/bootstrap/session.php';
require_once __DIR__ . '/../src/bootstrap/app.php';

ob_start();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!Authorization::hasCapability('cursos.ver')) {
    header('Location:../index.php');
    exit;
}

if (!isset($_SESSION['csrf_curso']) || !is_string($_SESSION['csrf_curso']) || strlen($_SESSION['csrf_curso']) !== 64) {
    $_SESSION['csrf_curso'] = bin2hex(random_bytes(32));
}

$esAdminCurso = Authorization::hasAny(['admin']);
$esComiteCurso = !$esAdminCurso && Authorization::hasAny(['comite']);
$esProfesorCurso = !$esAdminCurso
    && !$esComiteCurso
    && Authorization::hasAny(['docente'])
    && Authorization::hasCapability('docente.habilitado');
$puedeCrearCurso = $esAdminCurso || $esComiteCurso || $esProfesorCurso;
$puedeSeleccionarProfesor = $esAdminCurso || $esComiteCurso;

require 'header.php';
?>
<meta name="csrf-curso" content="<?= htmlspecialchars($_SESSION['csrf_curso'], ENT_QUOTES, 'UTF-8') ?>">
<span class="loadPage">
  <img src="../img/loadPage.gif" alt="Cargando" width="10%" height="10%">
</span>

<div
  class="container mt-5"
  id="curso_app"
  data-puede-crear="<?= $puedeCrearCurso ? '1' : '0' ?>"
  data-selecciona-profesor="<?= $puedeSeleccionarProfesor ? '1' : '0' ?>"
>
  <div class="row justify-content-center" id="mnsj_global" hidden>
    <div class="col-lg-8 alert mt-3 text-center fs-6" role="alert" id="mnsj"></div>
  </div>

  <section id="list_curso">
    <h3 class="text-center titulo_curso">CURSOS
      <?php if ($puedeCrearCurso): ?>
        <button class="btn btn-dark text-light px-3 m-3" id="btn-agr-curso" type="button">Agregar Registro</button>
      <?php endif; ?>
    </h3>

    <div class="row mt-5 g-3 m-5">
      <div class="col mb-3">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Periodo</th>
              <th>Año</th>
              <th>Detalle</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="datos_curso"></tbody>
        </table>
      </div>
    </div>
  </section>

  <?php if ($puedeCrearCurso): ?>
  <section id="form_curso_container" hidden>
    <h3 class="card-title text-center mb-5" id="titulo_form_curso">INGRESAR CURSO</h3>
    <form class="g-3 m-5" method="post" id="form_curso" enctype="multipart/form-data" novalidate>
      <input type="hidden" id="id_curso" value="0">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="nom_curso" class="form-label">Nombre Curso</label>
          <select id="nom_curso" class="form-select" required></select>
        </div>
        <div class="col-md-3 mb-3">
          <label for="creditos" class="form-label">Créditos SCT</label>
          <input type="number" class="form-control" id="creditos" step="any" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="caracter" class="form-label">Carácter</label>
          <select id="caracter" class="form-select" required>
            <option selected value="0">Seleccione</option>
            <option value="1">Obligatorio</option>
            <option value="2">Seminario</option>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label for="periodo" class="form-label">Periodo</label>
          <select id="periodo" class="form-select" required>
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
          <select class="form-select" id="anio_curso" required></select>
        </div>
        <?php if ($puedeSeleccionarProfesor): ?>
        <div class="col-md-6 mb-3" id="campo_profesor_curso">
          <label for="docente_curso" class="form-label">Profesor</label>
          <input type="text" class="form-control" id="docente_curso" autocomplete="off" required>
          <ul class="list-group" id="list_prof_curso"></ul>
        </div>
        <?php endif; ?>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="prog_curso" class="form-label">Adjuntar Programa del Curso</label>
          <input type="file" class="form-control" id="prog_curso" accept=".pdf,application/pdf">
          <div class="form-text" id="estado_programa"></div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
          <label for="car_hor" class="form-label">Carga Horaria (horas cronológicas)</label>
          <input type="number" class="form-control" id="car_hor" disabled required>
        </div>
      </div>

      <div class="row mt-5 justify-content-between">
        <div class="col-6 col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark" id="btn-ver-curso">Cancelar</button>
        </div>
        <div class="col-6 col-md-4 mb-3">
          <button type="submit" class="col-12 btn btn-dark" id="btn-guardar-curso">Guardar</button>
        </div>
      </div>
    </form>
  </section>
  <?php endif; ?>

  <section class="row mt-5 justify-content-center" id="det_curso" hidden>
    <div class="col-10">
      <table class="table" id="ficha">
        <tbody>
          <tr><th>Nombre Curso</th><td id="nom"></td></tr>
          <tr><th>Créditos</th><td id="cred"></td></tr>
          <tr><th>Carácter</th><td id="car"></td></tr>
          <tr><th>Periodo</th><td id="per"></td></tr>
          <tr><th>Año</th><td id="year"></td></tr>
          <tr><th>Carga Horaria</th><td id="carg"></td></tr>
          <tr><th>Profesor</th><td id="prof"></td></tr>
          <tr><th>Programa del Curso</th><td id="prog"></td></tr>
        </tbody>
      </table>
      <div class="row mt-5 justify-content-between">
        <div class="col-6 col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark detalleCurso">Volver</button>
        </div>
        <?php if ($puedeCrearCurso): ?>
        <div class="col-6 col-md-4 mb-3">
          <button type="button" class="col-12 btn btn-dark" id="editar_curso" hidden>Editar</button>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php require 'footer.php'; ?>
<script src="scripts/curso.js"></script>
<?php
ob_end_flush();
