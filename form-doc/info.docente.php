<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
ob_start();
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
if (!isset($_SESSION['docente'])) {
    header('Location:../index.php');
} else {
    require ('header.php');
    $id_profesor=$_SESSION['docente'];
    ?>
    <!-- <span class="loadPage">
        <img src="https://i.gifer.com/ZZ5H.gif" alt="" width="20%" height="20%">
    </span> -->
    <div class="container mt-5">
        <?php
        require 'ficha.docente.php';
        ?>
    </div>
    <?php
    require 'footer.php';
    ?>
    <script>window.contextoDocenteTercero = false;</script>
    <script src="scripts/ficha.docente.js"></script>
    <script src="scripts/info.docente.js"></script>
    <?php
}
ob_end_flush();
?>