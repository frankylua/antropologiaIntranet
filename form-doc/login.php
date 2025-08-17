<?php
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <title>Ingreso Doctorado Antropología</title>
</head>
<header>
</header>

<body>
    <div class="container m-5">
        <div class="row mt-5 justify-content-center h-100 ">
            <div class="col-lg-5 align-self-center m-5  ">
                <div class="card  mt-5 shadow-lg p-3 mb-5 bg-body rounded  ">
                    <div class="card-body">
                        <div class="row mt-5">
                            <div class="col-12">
                                <h1 class="card-title text-center">BIENVENID@</h1>
                            </div>
                            <div class="col-12">
                                <h5 class="text-center">Doctorado en Antropología UCN UTA</h5>
                            </div>
                        </div>
                        <form method='POST' action="" id="form_ingr">
                            <div class="row   justify-content-center mt-3">
                                <div class=" col-md-11 mb-3">
                                    <input type="text" class="form-control " id="correo_login" name='correo_login'
                                        aria-describedby="ayuda-correo" placeholder="Correo Electrónico">
                                </div>
                            </div>
                            <div class="row justify-content-center ">
                                <div class="col-md-11 mb-3">
                                    <input type="password" class="form-control" id="pass_login" name='pass_login'
                                        placeholder="Contraseña">
                                </div>
                            </div>
                            <div class="row justify-content-center " id="mnsj_row_login">
                                <div class="col-md-11 alert  text-center alert-danger" role="alert" id="mnsj_login">
                                </div>
                            </div>
                            <div class="row  justify-content-center mb-2">
                                <div class="  d-grid gap-2 col-11  mb-3">
                                    <button type="submit" class=" btn btn-dark">Ingresar</button>
                                </div>
                            </div>
                            <div class="row mb-3 d-flex justify-content-around">
                                <div class="col-auto ">


                                    <!-- desde aca se marca modal -->
                                    <a href="" class="link-dark " data-bs-toggle="modal"
                                        data-bs-target="#btn_registro">Registrarme</a>
                                    <div class="modal fade" id="btn_registro" tabindex="-1" aria-labelledby="modal_reg"
                                        aria-hidden="true">
                                        <form action="" method="post" id="form_tipo">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modal_reg">Seleccione tipo de
                                                            ingreso</h5>

                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body row">

                                                        <div class="col-md-6">
                                                            <div class="form-check   ms-2">
                                                                <input type="radio" class="form-check-input tipo"
                                                                    name="tipo-ingreso" id="doc" value='1'>
                                                                <label for="admin"
                                                                    class="form-check-label">Docente</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check  ms-2">
                                                                <input type="radio" class="form-check-input tipo"
                                                                    name="tipo-ingreso" id="est" value="2">
                                                                <label for="comite" class="form-check-label">Alumno/a
                                                                    Postulante</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <!-- evento ajax en js/buttons -->
                                                        <button type="button" class="btn btn-dark" id="ingreso"
                                                            name="ingreso">Ingresar</button>
                                                    </div>

                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="#" class="link-dark">Olvide mi Contraseña</a>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../js/buttons.js"></script>
    <script src="scripts/login.js"></script>

</html>
