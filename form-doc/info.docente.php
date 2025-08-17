<?php
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite']) && !isset($_SESSION['docente'])) {
    header('Location:../index.php');
} else {
    require 'header.php';

    ?>
    <span class="loadPage">
        <img src="../img/loadPage.gif" alt="" width="20%" height="20%">
    </span>
    <div class="container mt-5">
        <?php
        require 'ficha.docente.php';
        ?>
    </div>
    <?php
    require 'footer.php';
    ?>
    <script src="scripts/ficha.docente.js"></script>
    <script src="scripts/info.docente.js"></script>
    <?php
}

?>
</html>
