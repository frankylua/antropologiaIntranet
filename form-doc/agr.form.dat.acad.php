<?php
$gradoLogin = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
  ? (int) $_SESSION['login']
  : 0;
$gradoGlobal = $gradoLogin > 0 && (
  (isset($_SESSION['admin']) && is_scalar($_SESSION['admin']) && (int) $_SESSION['admin'] === $gradoLogin)
  || (isset($_SESSION['comite']) && is_scalar($_SESSION['comite']) && (int) $_SESSION['comite'] === $gradoLogin)
);
$gradoUsuariosSesion = $_SESSION['id_usuario'] ?? null;
$gradoUsuarioSesionValido = is_array($gradoUsuariosSesion)
  && count($gradoUsuariosSesion) === 1
  && isset($gradoUsuariosSesion[0]['id_usuario'])
  && is_scalar($gradoUsuariosSesion[0]['id_usuario'])
  && (int) $gradoUsuariosSesion[0]['id_usuario'] > 0;
$gradoCapacidades = isset($_SESSION['capacidades']) && is_array($_SESSION['capacidades'])
  ? $_SESSION['capacidades']
  : [];
$gradoProfesorPropio = $gradoLogin > 0
  && isset($_SESSION['docente'])
  && is_scalar($_SESSION['docente'])
  && (int) $_SESSION['docente'] === $gradoLogin;
$gradoEstudiantePropio = $gradoLogin > 0
  && isset($_SESSION['estudiante'])
  && is_scalar($_SESSION['estudiante'])
  && (int) $_SESSION['estudiante'] === $gradoLogin
  && in_array('perfil.ver', $gradoCapacidades, true);
$gradoPropio = $gradoUsuarioSesionValido && ($gradoProfesorPropio || $gradoEstudiantePropio);
$gradoContexto = $gradoGlobal ? 'global' : ($gradoPropio ? 'propio' : 'sin-acceso');
$gradoPuedeOperar = $gradoGlobal || $gradoPropio;
$gradoTokenCsrf = '';

if (session_status() === PHP_SESSION_ACTIVE) {
  if (
    !isset($_SESSION['csrf_grado'])
    || !is_string($_SESSION['csrf_grado'])
    || preg_match('/^[a-f0-9]{64}$/D', $_SESSION['csrf_grado']) !== 1
  ) {
    $_SESSION['csrf_grado'] = bin2hex(random_bytes(32));
  }
  $gradoTokenCsrf = $_SESSION['csrf_grado'];
}
?>
            <div id="grado-app"
              data-contexto="<?= htmlspecialchars($gradoContexto, ENT_QUOTES, 'UTF-8') ?>"
              data-csrf="<?= htmlspecialchars($gradoTokenCsrf, ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class='row mt-5'>
              <div class="col-12" id="grados_card">

              </div>
              <form action="" id="form_grado">
                <div class="col" id='grado_academico'>
                </div>
                </form>
            </div> 
                  
          
            
            <div class="row" id="boton_grado">
              <div class=" col d-grid gap-2 mb-3">
                <?php if ($gradoPuedeOperar): ?>
                <button class="btn btn-outline-dark" id="btn_grado" name="btn_pregrado" type="button" >Agregar Grado Académico (Pregrado / Postgrado)</button>
                <?php endif; ?>
              </div>
            </div>

            <div class='row'>
              <div class="col-12" id="postdoc_card">

              </div>
              <form action="" id="form_postdoc">
                <div class="col" id='postdoctorado'>
                </div>
                </form>
            </div> 
            <div class="row" id="boton_postdoc">
              <div class=" col d-grid gap-2 mb-3">
                <button class="btn btn-outline-dark" id="btn_postdoc" name="btn_postdoc" type="button" >Agregar Postdoctorado</button>
              </div>
            </div>
                  

            

            
            <div class='row'>
              <div class="col-12" id="publicacion_card">

              </div>
            <form action="" id="form_publicacion">
                <div class="col" id='publicacion' >
                    
                </div>
              </form>
            </div>
           
            <div class="row" id="boton_publicacion">
              <div class=" col d-grid gap-2 mb-3">
                <button class="btn btn-outline-dark" type="button" id="btn_publicacion" name="btn_publicacion">Agregar Publicación</button>
              </div>
            </div>

            <div class='row'>
              <form action="" id="form_congreso">
              <div class="col-12" id="congreso_card">
                </div>

                  <div class="col" id='congreso' >
                  </div>
              </form>
            </div>
                    

            <div class="row" id="boton_congreso">
              <div class=" col d-grid gap-2 mb-3">
                <button class="btn btn-outline-dark" type="button" id="btn_congreso"> Agregar Congreso</button>
              </div>
            </div>

            <div class='row'>
            <form action="" id="form_proyecto">
            <div class="col-12" id="proyecto_card">
                </div>
                <div class="col" id='proyecto'>
                </div>
                </form>
            </div>
                    
            <div class="row" id="boton_proyecto">
              <div class=" col d-grid gap-2 mb-3">
                <button class="btn btn-outline-dark" type="button" id="btn_proyecto">Agregar Proyecto de Investigación</button>
              </div>
            </div>

            <div class='row' >
              <form action="" id="form_pasantia">
              <div class="col-12" id="pasantia_card">
              </div>
              <div class="col" id='pasantia'>
              </div>
              </form>
            </div>

            <div class="row" id="boton_pasantia">
              <div class=" col d-grid gap-2 mb-3">
                <button class="btn btn-outline-dark" type="button" id="btn_pasantia">Agregar Pasantía</button>
              </div>
            </div>

            

            
                    

           
              
                

         
             


            

            


                  


              

