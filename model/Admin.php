<?php
require "Login_.php";
//require "Login.php";
class Admin extends Login {
    public function __construct(){
    }
    public function insertar($id_login,$nombre,$permiso){
       
        //ingesar permiso
        $sql_permiso="INSERT INTO permiso_login (id_per_log,id_login,id_permiso) VALUES (NULL,'$id_login','$permiso')";
        $queryPermiso=ejecutarConsulta($sql_permiso);
        //ingresar admin
        $sql_admin="INSERT INTO admin (id_admin,id_login,nombre) VALUES (NULL,'$id_login','$nombre')";
        $queryAdmin=ejecutarConsulta($sql_admin);
        if($queryAdmin && $queryPermiso){
            return true;
        }else{
            return false;
        }

    }

        
    public function editar($id_login,$nombre,$permiso){
        $sql_adm="UPDATE admin SET nombre='$nombre' where id_login='$id_login'";
        $sql_per="UPDATE permiso_login SET id_permiso='$permiso' where id_login='$id_login'";
        $respAdm=ejecutarConsulta($sql_adm);
        $respPer=ejecutarConsulta($sql_per);
        return $respAdm&&$respPer?true:false;
    }
    public function mostrarPorId($id_login){       
        $sql="SELECT l.id_login,a.nombre,l.correo,id_permiso,l.pass FROM login l JOIN admin a ON l.id_login=a.id_login JOIN permiso_login p ON p.id_login=l.id_login WHERE l.id_login='$id_login'";
        return resultadoConsultaPorId($sql);
        
   }
    public function mostrar(){
         //consultar admin (admin,login,permisos)
         $sql="SELECT l.id_login,p.id_per_log,a.id_admin,a.nombre,l.correo,p.id_permiso FROM login l JOIN admin a ON l.id_login=a.id_login JOIN permiso_login p ON p.id_login=l.id_login ORDER BY nombre";
         return ejecutarConsultaResultados($sql);
        
    }
  
}
?>


