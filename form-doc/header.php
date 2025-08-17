<?php
define('RUTA', '/antropologiaIntranet');
// define('RUTA', '/localhost/doctoradoantropologia')

error_reporting(E_ALL);
ini_set('display_errors', 1);
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
// $id = $_SESSION['admin'];
// $user = mostrar_perfil_admin($conexion,$id); 
$miperfil = isset($_SESSION['docente']) ? RUTA . 'form-doc/info.docente.php' : (isset($_SESSION['estudiante']) ? RUTA . 'form-doc/info.estudiante.php' : RUTA . 'admin/perfil.php');
// if(isset($_SESSION['profesor'])){
//     $miperfil = RUTA . 'form-doc/info.docente.php';
// }else if(isset($_SESSION['estudiante'])){
//     $miperfil = RUTA .'form-doc/info.estudiante';
// }else if(isset($_SESSION['admin'])|| isset($_SESSION['admin'])){
//     $miperfil = RUTA .'admin/perfil.php';
// }else{

// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo RUTA; ?>/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo RUTA; ?>/css/nav.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
    <script src="https://kit.fontawesome.com/50f392008f.js" crossorigin="anonymous"></script>
    <title>Doctorado Antropolgía UCN UTA</title>
</head>

<body>
    <header class="fixed-top">
        <div class="row justify-content-between bg-white">
            <div class="col-1 ">
                <img src="<?php echo RUTA; ?>/img/images.png" class="img-fluid" alt="...">
            </div>
            <div class="col-1">
                <img src="<?php echo RUTA; ?>/img/logo-ucn.jpg" class="img-fluid" alt="...">
            </div>
        </div>


        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow ml-2">
            <div class="container">
                <!-- <a class=" nav-link text-secondary"  href="#"></a> -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Mostrar/Ocultar Menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="menu">
                    <ul class="navbar-nav  mb-2 mb-lg-0 ">
                        <?php if (isset($_SESSION['admin']) || isset($_SESSION['comite'])): ?>
                        <li class="nav-item mx-2">
                                <a class="nav-link " aria-current="page"
                                    href="<?php echo RUTA; ?>admin/inicio.php">Inicio</a>
                            </li>
                            <li class="nav-item dropdown mx-2 ">
                                <!-- cambiar a dropleft para que el menu se vea asi en dispositivos moviles -->
                                <a class="nav-link   dropdown-toggle " id="listaIngreso" data-bs-toggle="dropdown"
                                    role="button" aria-expanded="false">Nuevo Ingreso</a>
                                <ul class="dropdown-menu  " aria-labelledby="listaIngreso">
                                    <li><a href="<?php echo RUTA; ?>form-doc/docente.php" class="dropdown-item"
                                            id="pag_doc">Docente</a></li>
                                    <li><a href="<?php echo RUTA; ?>form-doc/estudiante.php" class="dropdown-item"
                                            id="pag_est">Estudiante</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['admin']) || isset($_SESSION['comite'])|| isset($_SESSION['aceptado'])|| isset($_SESSION['estudiante'])|| isset($_SESSION['docente'])): ?>
                                <li class="nav-item dropdown mx-2">
                                    <a class="nav-link  dropdown-toggle" id="listaIngreso" data-bs-toggle="dropdown"
                                    role="button" aria-expanded="false">Programa</a>
                                    <ul class="dropdown-menu" aria-labelledby="listaIngreso">
                                        <?php if (isset($_SESSION['admin']) || isset($_SESSION['comite'])): ?>
                                    <li><a href="<?php echo RUTA; ?>admin/ver.estudiante.php"
                                            class="dropdown-item">Estudiantes</a></li>
                                    <li><a href="<?php echo RUTA; ?>admin/ver.docente.php"
                                            class="dropdown-item">Docentes</a>
                                    </li>
                                    
                                    <li><a href="<?php echo RUTA; ?>admin/act.list.php" class="dropdown-item">Actualizar
                                        Listas</a></li>
                                        <?php endif; ?>
                                        <?php if ((isset($_SESSION['docente']) && isset($_SESSION['aceptado'])) || isset($_SESSION['admin']) || isset($_SESSION['comite']) ): ?>
                                        <li><a href="<?php echo RUTA; ?>form-doc/ver.curso.php" class="dropdown-item">Cursos</a>
                                        </li>
                                        <?php endif; ?>
                                    <?php if (isset($_SESSION['admin'])): ?>
                                        <li><a href="<?php echo RUTA; ?>admin/ver.admin.php" class="dropdown-item">Comite
                                                Académico</a></li>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_SESSION['admin']) || isset($_SESSION['comite']) || isset($_SESSION['aceptado'])): ?>
                                    <li><a href="<?php echo RUTA; ?>form-doc/calend.acad.php"
                                            class="dropdown-item">Calendario Académico</a></li>
                                            <?php endif; ?>
                                    <li><a href="<?php echo RUTA; ?>form-doc/reglamento.php"
                                            class="dropdown-item">Reglamento</a></li>
                                    <li><a href="<?php echo $miperfil ?>" class="dropdown-item">Mi Perfil</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['admin']) || isset($_SESSION['comite'])): ?>
                            <li class="nav-item dropdown mx-2">
                                <a id="listaReporte" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                    role="button" aria-expanded="false">Reporte</a>
                                <ul class="dropdown-menu" aria-labelledby="listaReporte">
                                    <li><a href="<?php echo RUTA; ?>admin/filtrar.informe.php" class="dropdown-item">Filtrar
                                            Datos del Programa</a></li>
                                    <li><a href="<?php echo RUTA; ?>admin/tabla.predefinida.php"
                                            class="dropdown-item">Tablas
                                            Predefinidas</a></li>
                                </ul>
                            </li>
                            <?php endif; ?>
                            <li class="nav-item mx-2">
                                <a class="nav-link " href="<?php echo RUTA; ?>form-doc/cerrar.php">Salir</a>
                            </li>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </header>