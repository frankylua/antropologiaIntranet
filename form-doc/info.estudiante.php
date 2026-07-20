<?php
use App\Security\Authorization;

ob_start();
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
require_once __DIR__ . '/../src/bootstrap/app.php';
if (!Authorization::hasCapability('perfil.ver') && !Authorization::hasAny(['admin', 'comite'])) {
    header('Location:../index.php');
} else {
    require ('header.php');
    $id_estudiante=$_SESSION['estudiante'];
    ?>
    <!-- <span class="loadPage">
        <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
    </span> -->
    <div class="container mt-5" >
        <?php
        require 'ficha.estudiante.php';

        ?>
    </div>

    <?php
    require 'footer.php';
    ?>
    <script src="scripts/info.estudiante.js"></script>
    <script src="scripts/ficha.estudiante.js"></script>
    <?php
}
ob_end_flush();
?>
