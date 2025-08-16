<?php

require "Usuario_.php";
class Docente extends Usuario{
    public function __construct(){
    }
    public function insertar($catAcad,$anioIng,$vinculo,$usuario){  
        //ingesar docente
        $sql="INSERT INTO profesor (id_profesor,usuario,vinculo,cat_academica,anio_ingreso) VALUES (NULL,'$usuario','$vinculo','$catAcad','$anioIng')";
        return ejecutarConsulta($sql);   
    }
   
    // public function mostrarPorId($id_curso){
    //     $sql="SELECT * FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  WHERE c.id_curso='$id_curso'";
    //     return resultadoConsultaPorId($sql);
    // }
        
    // public function mostrar(){
         
    //     $sql="SELECT c.id_curso,n.nom_curso,c.periodo,c.anio_curso FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  ORDER BY c.anio_curso DESC";
    //     return ejecutarConsultaResultados($sql);
        
    // }

    public function buscarProf($busqueda){    
        $texto = explode(" ", $busqueda);
        $esp= count($texto) - 1;// codigo para validar apellidos --- pendiente ----
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario WHERE u.nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);
            
    }
    public function mostrarProf(){    
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,l.id_login FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario JOIN login l ON u.login=l.id_login order by u.nombres";
        return ejecutarConsultaResultados($sql);            
    }
    public function mostrarProfId($id_usu){    
        $sql="SELECT *, pr.pais as p_resi, pn.pais as p_nac FROM usuario u JOIN login l ON u.login=l.id_login JOIN profesor p ON u.id_usuario=p.usuario JOIN institucion i ON u.inst_usuario=i.id_inst JOIN pais pn ON u.pais_nac=pn.id_pais JOIN pais pr ON u.pais_res=pr.id_pais JOIN pueblo pu ON u.pueblo=pu.id_pueblo JOIN linea_usuario lu ON u.id_usuario=lu.usuario JOIN linea_investigacion li ON li.id_linea_inv=lu.linea_inv WHERE u.id_usuario= '$id_usu' order by u.nombres";
        return ejecutarConsultaResultados($sql);            
    }
    public function mostrarDatosProg($id_usu){
        $sql="SELECT u.inst_usuario,u.id_usuario,p.id_profesor,p.vinculo,p.cat_academica,p.anio_ingreso,l.linea_inv,per.id_permiso as permisos, lo.id_login FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario JOIN linea_usuario l ON u.id_usuario=l.usuario JOIN login lo ON u.login=lo.id_login JOIN permiso_login per ON lo.id_login=per.id_login WHERE u.id_usuario='$id_usu'";
        return ejecutarConsultaResultados($sql);
    }
    public function mostrarProfTipo($tipo){    
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,l.id_login FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario JOIN login l ON u.login=l.id_login WHERE vinculo='$tipo' order by u.nombres";
        return ejecutarConsultaResultados($sql);        
    }
    public function editarDatProg($id_usu,$vinculo,$catAcad,$anioIng){
        $sql="UPDATE profesor SET vinculo='$vinculo', cat_academica='$catAcad',anio_ingreso='$anioIng'  WHERE usuario ='$id_usu'";
        return ejecutarConsulta($sql);
    }
  

}