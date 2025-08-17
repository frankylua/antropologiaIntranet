<?php

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite']) && !isset($_SESSION['aceptado'])) {
    header('Location:../index.php');
} else {
    require ('header.php');
    ?>
    <div class="container mt-5">
        <div class="row m mb-5">
            <div class="col">
                <h3 class="text-center mb-5"> REGLAMENTO DOCTORADO </h3>
                <div class="list-group shadow">
                    <li class="list-group-item d-flex justify-content-between">
                        <p>Item I</p><button class="btn btn-link link-dark ">Descargar</button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <p>Item II</p><button class="btn btn-link link-dark">Descargar</button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <p>Item III</p><button class="btn btn-link link-dark">Descargar</button>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <p>Item IV</p><button class="btn btn-link link-dark">Descargar</button>
                    </li>
                </div>
            </div>
        </div>
    </div>
    <?php
    require ('footer.php');
}

?>
</html>
<?php
ob_end_flush();
?>