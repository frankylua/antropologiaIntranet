<?php
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}

    if(!isset($_SESSION['admin']) && !isset($_SESSION['comite'])){
        header('Location: ../index.php');
    }else{
        if(isset($_SESSION['admin'])){
            $id_login = $_SESSION['admin'];
        }
        if(isset($_SESSION['comite'])){
            $id_login = $_SESSION['comite'];
        } 
        require '../form-doc/header.php';
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col" id="perfil_admin">
                <div class="row justify-content-center">
                    <div class="col-md-8 mt-5 ">
                        <div class="card shadow " id="id_usuario" name="<?php echo $id_login;?>">
                            <div class="card-header borde bg-dark">
                            </div>
                            <div class="card-body m-0">
                                <h3 class="text-center  m-0 mb-5">MI PERFIL</h3>
                                <div class="row justify-content-center">
                                    <div class="col-md-10 mb-5" id="tabla_perfil">
            
                                   
                                </div>
                            </div>
                            <div class="row ">
                                <div class="d-grid gap-2 col-6 mx-auto">
                                    <button class="btn btn-dark" type="button" id="editar_perfil_admin" name="<?php echo $id_login;?>" >Editar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>
    <div class="col" id="form_admin_edit">
            <div class="row ">
                <div class="col">
                    <h3 class="text-center mb-5">INGRESO ADMINISTRACIÓN</h3>
                    <form class=" g-3" method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="row ">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <input type='hidden' id='id_admin'></input>
                                    <label for="nom_admin" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nom_admin" name='nom_admin' maxlength="50">
                                </div>

                            </div>
                            <div class="col-md-6 mb-3">
                                <input type='hidden' id='id_login'></input>
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="text" class="form-control" id="correo" name='correo' maxlength="50">

                            </div>
                        </div>

                        <div class="row" id="box-pass" name="0">
                            <div class="col-md-6 mb-3">
                                <label for="pass" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="pass" name='pass' maxlength="20">

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pass2" class="form-label">Repetir Contraseña</label>
                                <input type="password" class="form-control" id="pass2" name='pass2' maxlength="20">

                            </div>
                        </div>
                        <div class="row ">
                            <?php if (isset($_SESSION['admin'])): ?>
                            <div class="col-lg-6 col- ">
                                <label class="form-label mb-2">Tipo de Ingreso</label>
                                <input type='hidden' id='id_per_log'></input>
                                <select class="form-select" name="permiso" id="permiso">
                                    <option value="0" selected>Seleccione</option>
                                    <option value="1">Administrador(a)</option>
                                    <option value="2">Comite Académico</option>
                                </select>

                            </div>
                            <?php endif; ?>
                            <div class="col-md-6 mb-3 mt-2">
                                <input type="button" class="btn btn-outline-dark mt-4" value="Cambiar Constraseña"
                                    id="btn_cambiar_pass">
                            </div>
                        </div>





                        <div class="row justify-content-center p-0" id="mnsj_row_edit">
                            <div class="col-lg-8 alert  mt-3 text-center pb-0 fs-6" role="alert" id="mnsj_edit">

                            </div>
                        </div>

                        <div class="row mt-5 justify-content-between ">
                            <div class="col-4 mb-3">
                                <button type="button" class="col-12 btn btn-dark  col-6"
                                    id="btn-ver-comite">Cancelar</button>
                            </div>
                            <div class="col-4 mb-3 ">
                                <button type="submit" class="col-12 btn btn-dark  col-6 ">Editar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
<?php
    require '../form-doc/footer.php';
?>
<script src="scripts/perfil.js"></script>
<?php
}

?>
</html>
    
        
       
    
       
        

    
    

    