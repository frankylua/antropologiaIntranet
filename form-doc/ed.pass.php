<?php

$conexion = conexion($bd_config);
$id_login = rol_id();
$errores = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pass = limpiar_datos($_POST['pass']);
    $pass2 = limpiar_datos($_POST['pass2']);
    if(strlen($pass)>3){
        if($pass == $pass2){
            $pass = hash('sha512',$pass);
            $statement_login= $conexion->prepare('UPDATE login SET pass = :pass where id_login = :id_login');
            $statement_login->execute(array(':pass' => $pass, ':id_login' => $id_login));
            header('Location:'.RUTA.'admin/perfil.php');
        }else{
            $errores.= '<li> Las contraseñas no coinciden</li>';  
        }
    }else{
        $errores.= '<li> La contraseña debe tener minimo 8 caracteres</li>';

    }
}
if(isset($_SESSION['admin']) || isset($_SESSION['comite'])){
}else{
    header('Location:../index.php');
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <link rel="stylesheet" href="../css/style.css">
    <title>Ingreso Doctorado Antropología</title>
</head>
<header>
</header>
<body >
    <div class="container m-5">
        <div class="row mt-5 justify-content-center h-100 ">
            <div class="col-lg-5 align-self-center m-5  ">
                <div class="card  mt-5 shadow-lg p-3 mb-5 bg-body rounded  ">
                    <div class="card-body">
                        <div class="row mt-5">
      
                            <div class="col-12">
                                <h4 class="text-center">Editar Contraseña</h4>
                            </div>
                        </div>
                        <form method='POST' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>">
                            <div class="row   justify-content-center mt-3">
                                <div class=" col-11 mb-3">
                                    <input type="password" class="form-control " id="pass" name='pass' aria-describedby="ayuda-correo" placeholder="Nueva Contraseña">
                                </div>
                            </div>
                            <div class="row justify-content-center ">
                                <div class="col-11 mb-3">
                                    <input type="password" class="form-control" id="pass2" name='pass2' placeholder="Repetir Contraseña">
                                </div>
                            </div>
                            <?php if($errores != ''):?>
                                <div class="row">
                                    <div class="col-auto alert alert-danger " role="alert">
                                    <?php echo $errores?>
                                    </div>
                                </div>
                            <?php endif;?>
                            <div class="row  justify-content-center mb-2">
                                <div class="  d-grid gap-2 col-11 ">
                                    <button type="submit" class=" btn btn-dark" >Editar</button>   
                                </div>
                            </div>
                            <div class="row  justify-content-center mb-2">
                                <div class="  d-grid gap-2 col-11 ">
                                    <button type="button" class=" btn btn-dark inicio" >Salir</button>   
                                </div>
                            </div>
     
                           
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../js/buttons.js"></script>                       
        </html>
 