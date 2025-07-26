<?php

require "Login_.php";

class Usuario extends Login {
    public function __construct(){
    }
    public function insertarUsuario($pueblo,$pais_res,$pais_nac,$login,$fech_nac,$nombres,$ap_mat,$ap_pat,$tipo_doc,$nro_doc,$institucion,$genero,$region,$comuna,$telefono,$direccion,$cont_em,$tel_em){
        //ingesar permiso
        $sql_usuario="INSERT INTO usuario (id_usuario,pueblo,pais_res,pais_nac,login,fecha_nac,nombres,ap_mat,ap_pat,tipo_doc,nro_doc,inst_usuario,genero,region,comuna,telefono,direccion,cont_em,tel_em) VALUES (NULL," . (empty($pueblo)?1:"'$pueblo'"). ",'$pais_res','$pais_nac','$login','$fech_nac','$nombres','$ap_mat','$ap_pat','$tipo_doc','$nro_doc','$institucion','$genero','$region','$comuna','$telefono','$direccion','$cont_em','$tel_em')";
        return obtenerIdConsulta($sql_usuario);
        //ingresar admin
        //$sql_admin="INSERT INTO admin (id_admin,id_login,nombre) VALUES (NULL,'$id_login','$nombre')";
        // $queryUsuario=ejecutarConsulta($sql_usuario);
        // if($queryUsuario && $queryPermiso){
        //     return true;
        // }else{
        //     return false;
        // }
    }
    public function insertarPermisos($id_login,$permiso){
        $sql="INSERT INTO permiso_login (id_per_log,id_login,id_permiso) VALUES (NULL,'$id_login','$permiso')";
        return ejecutarConsulta($sql);
    }
    public function insertarLineaInv($usuario,$linea){
        $sql_linea="INSERT INTO linea_usuario (id_linea_usuario,usuario,linea_inv) VALUES (NULL,'$usuario','$linea')";
        return ejecutarConsulta($sql_linea);
    }
    public function buscarLinInv($linea,$id_usu){
        $sql="SELECT * FROM linea_usuario WHERE usuario='$id_usu' AND linea_inv='$linea'";
        return ejecutarConsultaResultados($sql);
    }
    public function todasLinInv($id_usu){
        $sql="SELECT * FROM linea_usuario WHERE usuario='$id_usu'";
        return ejecutarConsultaResultados($sql);
    }
    public function borrarLineaInv($id_usu,$linea){
        $sql="DELETE FROM linea_usuario WHERE usuario='$id_usu' AND linea_inv='$linea'";
        return ejecutarConsulta($sql);
    }

    public function validarCorreo($correo){
        $sql="SELECT * FROM login WHERE correo='$correo'";
        return ejecutarConsultaResultados($sql);

    }
    public function mostrarAutor($usuario){
        $sql="SELECT nombres,ap_pat,ap_mat FROM usuario WHERE id_usuario='$usuario'";
        return ejecutarConsultaResultados($sql);
    }
    public function buscarPermiso($permiso,$id_login){
    $sql="SELECT * FROM permiso_login WHERE id_login='$id_login' AND id_permiso='$permiso'";
    return ejecutarConsultaResultados($sql);
    }
    public function eliminarPermiso($per,$id_login){
        $sql="DELETE FROM permiso_login WHERE id_login='$id_login' AND id_permiso='$per'";
        return ejecutarConsultaResultados($sql);
    }

    public function agregarPermiso($id_permiso,$id_login){
        $sql="INSERT INTO permiso_login (id_per_log,id_login,id_permiso) VALUES (NULL,'$id_login','$id_permiso')";
        return ejecutarConsulta($sql);
    }
  
    public function eliminarAcceso($id_login){
        $sql="DELETE FROM permiso_login  WHERE id_login ='$id_login'";
        return ejecutarConsulta($sql);
    }
    public function editarPermiso($per,$id_login){
        $sql="UPDATE permiso_login SET id_permiso='$per' WHERE id_login='$id_login' AND (id_permiso=1 OR id_permiso=2)";
        return ejecutarConsultaResultados($sql);
    }
    public function editarInfPers($nombres,$ap_mat,$ap_pat,$fech_nac,$documento,$nro_doc,$pais_nac,$genero,$pais_res,$region,$comuna,$telefono,$direccion,$cont_em,$tel_em,$correo,$pass,$pueblo,$id_usu,$id_login){
        $sql="UPDATE usuario SET pueblo='$pueblo',pais_res='$pais_res',pais_nac='$pais_nac',login='$id_login',fecha_nac='$fech_nac',nombres='$nombres',ap_mat='$ap_mat',ap_pat='$ap_pat',tipo_doc='$documento',
        nro_doc='$nro_doc',genero='$genero',region='$region',comuna='$comuna',telefono='$telefono',direccion='$direccion',cont_em='$cont_em',tel_em='$tel_em' where id_usuario='$id_usu'";
        return ejecutarConsulta($sql);
        
    }

    public function editarInstDoc($id_usu,$inst_usuario){
        $sql="UPDATE usuario SET inst_usuario='$inst_usuario'  WHERE id_usuario ='$id_usu'";
        return ejecutarConsulta($sql);
    }
       
        
        
        


        
    // public function editar($id_login,$nombre,$permiso){
    //     $sql_adm="UPDATE admin SET nombre='$nombre' where id_login='$id_login'";
    //     $sql_per="UPDATE permiso_login SET id_permiso='$permiso' where id_login='$id_login'";
    //     $respAdm=ejecutarConsulta($sql_adm);
    //     $respPer=ejecutarConsulta($sql_per);
    //     return $respAdm&&$respPer?true:false;
    // }
//     public function mostrarPorId($id_login){
       
//         $sql="SELECT l.id_login,a.nombre,l.correo,id_permiso,l.pass FROM login l JOIN admin a ON l.id_login=a.id_login JOIN permiso_login p ON p.id_login=l.id_login WHERE l.id_login='$id_login'";
//         return resultadoConsultaPorId($sql);
        
//    }
    // public function mostrar(){
    //      //consultar admin (admin,login,permisos)
    //      $sql="SELECT l.id_login,p.id_per_log,a.id_admin,a.nombre,l.correo,p.id_permiso FROM login l JOIN admin a ON l.id_login=a.id_login JOIN permiso_login p ON p.id_login=l.id_login ORDER BY nombre";
    //      return ejecutarConsultaResultados($sql);
        
    // }
  
}
?>