<?php
    ob_start();
    if (strlen(session_id()) < 1){
        session_start();//Validamos si existe o no la sesión
    }
    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite'])){
        header('Location:../index.php');
    }else
    {
    require('../form-doc/header.php');
?>

<div class="container mt-5">
    <form class=" g-3 m-5" method='post'>
        <div class="row">
            <div class="col">
                <h3 class="card-title mb-5 text-center">FILTRAR DATOS PARA INFORME</h3>
                <div class="row ">
                    <label for="inf_desde" class="form-check-label mb-3">Rango de años</label>
                    <div class="col-md-6 mb-4">
                        <select  id="inf_desde" class="form-select mb-3"></select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <select  id="inf_hasta" class="form-select"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                    <div class="form-check   ">
                        <input type="radio" class="form-check-input" name="tipo-ingreso" id="doc">
                        <label for="doc" class="form-check-label">Docente</label>
                    </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check ">
                            <input type="radio" class="form-check-input" name="tipo-ingreso" id="est">
                            <label for="est" class="form-check-label">Estudiante</label>
                        </div>
                    </div>
                </div>

                
                

                                
                            
                            
                        
                        
                        <div class="row" >
                            <div class="col-md-6 mb-3" id="tip_doc"></div>
                            <div class="col-md-6 mb-3" id="tip_est"></div>
                        </div>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="proy">
                                    <label class="form-check-label" for="proy">Proyecto de Investigación</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="proy">
                                    <label class="form-check-label" for="proy">Linea de Investigación</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="proy">
                                    <label class="form-check-label" for="proy">Pregrado</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="bec">
                                    <label class="form-check-label" for="bec">Postgrado</label>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="pub">
                                    <label class="form-check-label" for="pub">Publicación</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="con">
                                    <label class="form-check-label" for="con">Congreso</label>
                                </div>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-6" id="tip_pub"></div>
                            <div class="col-md-6" id="tip_con"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"  id="bec">
                                    <label class="form-check-label" for="bec">Pasantía</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="beca_tesis">
                                
                            </div> 
                        </div>
                        <div class="row">
                            
                            <div class="d-grid gap-2 col-6 mx-auto mt-5">
                                <button class="btn btn-dark" type="button">Generar Tabla</button>
                                
                            </div>
                        </div>

                      
                        <!-- Tablas predefinidas -->
                            
                       
        <?php require('../form-doc/footer.php');?>
    
        <?php
}
ob_end_flush();
?>