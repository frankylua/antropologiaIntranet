<?php
require "../model/Login_.php";
//require('validaciones.php');
$login= new Login();
$correo=isset($_POST['correo'])?$_POST['correo']:"";
$pass=isset($_POST['pass'])?$_POST['pass']:"";
$mensaje='';
if(isset($correo) && isset($pass)){
    $permisos=$login->validarPermiso($correo,$pass); //funcion para validar permisos desde la base de datos retorna areglos con permisos    
    foreach($permisos as $permiso){  
        $_SESSION['login']=$permiso['id_login'];    
       if($permiso['id_permiso'] == 1){
            $_SESSION['admin']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 2){
            $_SESSION['comite']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 3){// este permiso es aquel que se ha ingresado en el sistema con permiso de docente o estudiante aceptado
            $_SESSION['aceptado']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 4){
            $_SESSION['docente']=$permiso['id_login'];
            //creo la variable de sesion con el id usuario
            $id_login=(int)$permiso['id_login'];
            
        }
        
        if($permiso['id_permiso']== 5){
            $_SESSION['estudiante']=$permiso['id_login'];
            // $id_usuario=$login->retornarIdUsu($id_login);
            // $_SESSION['id_usuario']=$id_usuario;
        }
    
    }
}
    if(isset($_SESSION['docente'])||isset($_SESSION['estudiante'])){
            $id_usuario=$login->retornarIdUsu($_SESSION['login']);
            $_SESSION['id_usuario']=$id_usuario;
    }
    echo json_encode($permisos, JSON_UNESCAPED_UNICODE);
    
    
?>