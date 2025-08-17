<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ob_start();
session_start();

if (!isset($_SESSION['admin']) && !isset($_SESSION['comite'])) {
    header('Location:../index.php');
} else {
    require ('../form-doc/header.php');
    ?>
    <div class="container mt-2">
        <div class="row">
            <div class="col g-3 m-5">
                <h3 class="card-title mb-5 text-center">LISTAS PREDEFINIDAS</h3>
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="list-group">
                            <button type="button" name='btn_list_pueb' class="list-group-item list-group-item-action"
                                id='btn_pueblos'>Pueblos Indígenas</button>
                            <button type="button" name="btn_tit_lic" class="list-group-item list-group-item-action"
                                id='btn_lic'>Licenciatura</button>
                            <button type="button" name="btn_tit_un" class="list-group-item list-group-item-action"
                                id='btn_un'>Título Universitario</button>
                            <button type="button" name="btn_tit_mag" class="list-group-item list-group-item-action"
                                id='btn_mag'>Magíster</button>
                            <button type="button" name="btn_tit_doc" class="list-group-item list-group-item-action"
                                id='btn_doc'>Doctorado</button>
                            <button type="button" name="btn_inst" class="list-group-item list-group-item-action"
                                id='btn_institucion'>Instituciones</button>
                            <button type="button" name="btn_bec_int" class="list-group-item list-group-item-action"
                                id='btn_bec_int'>Becas Internas</button>
                            <button type="button" name="btn_bec_ext" class="list-group-item list-group-item-action"
                                id='btn_bec_ext'>Becas Externas</button>
                            <button type="button" name="btn_financ" class="list-group-item list-group-item-action"
                                id='btn_financ'>Fuente de Financiamiento</button>
                        </div>
                    </div>
                    <div class="col-lg-8" id='tabla-listas'>
                        <div class="row">
                            <div class="col mb-3">
                                <form action="" method="post" id='form_lista'>
                                    <div class="input-group" id="grp-btn">
                                        <input type="hidden" name="0" id="oculto" class='form-control' >
                                        <input type="text" name="" id="dato_lista" class='form-control' maxlength="80">
                                        <!-- <div class="form-group"> -->
                                        <input type="submit" class='btn btn-outline-dark input-group-text'
                                            id="agregar_lista"></input>
                                        <!-- <input type="button" class='btn btn-outline-dark input-group-text' id="canc_edit" value="Cancelar"></input> -->
                                        <!-- </div> -->
                                    </div>
                                </form>
                            </div>
                            <div class="col-12" id='list_pueb'>
                                <ul class="list-group" id='listas'>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <?php
    require ('../form-doc/footer.php');
    ?>
    <script src="scripts/listas.js"></script>
    <?php
}
ob_end_flush();
?>