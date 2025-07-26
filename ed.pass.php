<?php
require 'functions.php';
require 'config.php';
session_start();
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
    require 'views/ed.pass.view.php';
}else{
    header('Location: index.php');
}


?>
        
        
        

