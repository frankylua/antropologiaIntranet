<?php
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite']) && !isset($_SESSION['estudiante'])) {
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

?>
</html>
