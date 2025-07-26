<?php

require "Usuario_.php";
class Estudiante extends Usuario{
    public function __construct(){
    }
    //insertar($profesor,$tipo_est,$fech_ing,$fecha_grad,$promedio,$trabaja,$orientacion,$sit_ocup
    public function insertar($profesor,$tipo_est,$fech_ingr,$fech_grad,$promedio,$trabaja,$orientacion,$sit_ocup,$usuario){
        $sql="INSERT INTO estudiante (id_est,prof_guia,tipo_est,fech_ingr,fecha_egr,prom_notas,trabaja,orient_disc,sit_previa,usuario) VALUES (NULL,'$profesor','$tipo_est','$fech_ingr'," . (empty($fech_grad)? "NULL":"'$fech_grad'"). ",'$promedio','$trabaja','$orientacion','$sit_ocup','$usuario')";
        return ejecutarConsulta($sql);
        //return true;
       
    }



        
    // public function editar($id_curso,$nom_curso,$creditos,$caracter,$periodo,$anio_curso,$programa,$car_hor){
    //     $sql="UPDATE curso SET creditos='$creditos',caracter='$caracter',periodo='$periodo',anio_curso='$anio_curso',carga_hor='$car_hor',nom_curso='$nom_curso' where id_curso='$id_curso'";
    //     return ejecutarConsulta($sql);
        
    // }
    // public function mostrarPorId($id_curso){
    //     $sql="SELECT * FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  WHERE c.id_curso='$id_curso'";
    //     return resultadoConsultaPorId($sql);
    // }
        
    // public function mostrar(){
         
    //     $sql="SELECT c.id_curso,n.nom_curso,c.periodo,c.anio_curso FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  ORDER BY c.anio_curso DESC";
    //     return ejecutarConsultaResultados($sql);
        
    // }

     public function mostrarTipo(){
         
        $sql="SELECT * FROM tipo_estudiante  ORDER BY tipo";
        return ejecutarConsultaResultados($sql);
        
    }
    public function mostrarEst(){    
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat, t.tipo , t.id_tipo_est as id_tipo, l.id_login FROM usuario u INNER JOIN login l ON u.login=l.id_login INNER JOIN estudiante e ON u.id_usuario=e.usuario INNER JOIN tipo_estudiante t ON e.tipo_est=t.id_tipo_est order by u.nombres";
        return ejecutarConsultaResultados($sql);
    }

    public function mostrarEstTipo($tipo){    
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,e.tipo_est,l.id_login, t.tipo FROM usuario u JOIN estudiante e ON u.id_usuario=e.usuario JOIN tipo_estudiante t ON e.tipo_est= JOIN login l ON u.login=l.id_login WHERE e.tipo_est='$tipo' order by u.nombres";
        return ejecutarConsultaResultados($sql);        
    }
    public function mostrarEstId($id_usu){    
        $sql="SELECT *, pr.pais as p_resi, pn.pais as p_nac FROM usuario u JOIN login l ON u.login=l.id_login JOIN estudiante e ON u.id_usuario=e.usuario JOIN institucion i ON u.inst_usuario=i.id_inst JOIN pais pn ON u.pais_nac=pn.id_pais JOIN pais pr ON u.pais_res=pr.id_pais JOIN pueblo pu ON u.pueblo=pu.id_pueblo JOIN tipo_estudiante te ON e.tipo_est=te.id_tipo_est JOIN linea_usuario lu ON u.id_usuario=lu.usuario JOIN linea_investigacion li ON li.id_linea_inv=lu.linea_inv WHERE u.id_usuario='$id_usu'order by u.nombres";
        return ejecutarConsultaResultados($sql);            
    }

    // mostrar respuesta de la busqueda profesores 
    public function mostrarProf($busqueda){    
        $texto = explode(" ", $busqueda);
        $esp= count($texto) - 1;// codigo para validar apellidos --- pendiente ----
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario WHERE u.nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);
    }

    public function mostrarNomProf($id_usuario){    
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario WHERE u.id_usuario='$id_usuario'";
        return ejecutarConsultaResultados($sql);
    }


    public function mostrarEstFiltro($busqueda){    
        $texto = explode(" ", $busqueda);
        $esp= count($texto) - 1;// codigo para validar apellidos --- pendiente ----
        $sql="SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,e.tipo_est,l.id_login, t.tipo FROM usuario u JOIN estudiante e ON u.id_usuario=e.usuario JOIN tipo_estudiante t ON e.tipo_est=t.id_tipo_est JOIN login l ON u.login=l.id_login WHERE u.nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);
            
    }
    public function mostrarDatosProgEst($id_usu){
        $sql="SELECT prof_guia, tipo_est, fech_ingr, fecha_egr, prom_notas, trabaja, orient_disc, sit_previa, id_usuario, l.linea_inv, lo.id_login, u.inst_usuario FROM usuario u JOIN estudiante e ON u.id_usuario=e.usuario JOIN linea_usuario l ON u.id_usuario=l.usuario JOIN login lo ON u.login=lo.id_login JOIN institucion i ON u.inst_usuario=i.id_inst WHERE u.id_usuario='$id_usu'";
        return ejecutarConsultaResultados($sql);
    }

    public function editarDatProg($id_usu, $profesor,$fech_ing, $fech_grad, $promedio,$trabaja, $orientacion, $sit_ocup){
        $sql="UPDATE estudiante SET prof_guia='$profesor', fech_ingr='$fech_ing',fecha_egr='$fech_grad', prom_notas='$promedio', trabaja='$trabaja', orient_disc='$orientacion', sit_previa='$sit_ocup'  WHERE usuario ='$id_usu'";
        return ejecutarConsulta($sql);
    }

    public function editarTipoEst($id_usu,$tipo_est){
        $sql="UPDATE estudiante SET tipo_est='$tipo_est'  WHERE usuario ='$id_usu'";
        return ejecutarConsulta($sql);
    }




  


    // public function eliminar($id_curso){
    //     $sql="DELETE FROM curso  WHERE id_curso ='$id_curso'";
    //     return ejecutarConsulta($sql);

    // }
       
  
}

