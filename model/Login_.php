<?php

if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
require "../config/conexion.php";

 class Login 
 {
  
    
    public function __contruct(){

    }
    public function insertarLogin($correo,$pass){
        $sql="INSERT INTO login (id_login,correo,pass) VALUES (NULL,'$correo','$pass')";
        return obtenerIdConsulta($sql);
    }

   
    public function editarLogin($id_login,$correo,$pass){
        $sql="UPDATE login SET correo = '$correo', pass = '$pass' WHERE id_login = '$id_login'";
        return ejecutarConsulta($sql);
    }
    
    public function eliminar($id_login){
        $sql="DELETE FROM login  WHERE id_login ='$id_login'";
        return ejecutarConsulta($sql);

    }

    public function validarPermiso($correo,$pass){
        $sql="SELECT * FROM login l JOIN permiso_login p WHERE l.id_login=p.id_login AND l.correo='$correo' AND l.pass='$pass'";
        return ejecutarConsultaResultados($sql);
    }

    public function retornarIdUsu($id_login){
        $sql= "SELECT id_usuario FROM login l JOIN usuario u WHERE l.id_login=u.login AND l.id_login='$id_login'";
        return ejecutarConsultaResultados($sql);
        
    }
}
?>