<?php
require "../config/conexion.php";
Class Tesis  {
    public function __construct(){
    }
    //insert
    public function insertarTesis($lugar,$grado,$anio,$titulo,$nom_guia,$nom_est,$nom_coguia,$guia,$est,$coguia,$inst_tesis,$pais_tesis,$fecha_aprob,$fecha_def,$panel){        
        $sql="INSERT INTO tesis (id_tesis,lugar,grado,tit_tesis,anio,fecha_aprob,fecha_def,panel_eval,id_est_tes,nom_est_tes,id_prof_guia,id_prof_coguia,nom_prof_guia,nom_prof_coguia,inst_tesis,pais_tesis)  VALUES (NULL,'$lugar','$grado','$titulo','$anio','$fecha_aprob','$fecha_def','$panel'," . (empty($est)? "NULL":"'$est'"). "," . (empty($nom_est)? "NULL":"'$nom_est'"). "," . (empty($guia)? "NULL":"'$guia'"). "," . (empty($coguia)? "NULL":"'$coguia'"). "," . (empty($nom_guia)? "NULL":"'$nom_guia'")."," . (empty($nom_coguia)? "NULL":"'$nom_coguia'").",'$inst_tesis','$pais_tesis')";
        return obtenerIdConsulta($sql);
    }
    public function insertarCotutela($prof_cot,$inst_cot,$fondo,$ciudad_cot,$pais_cot,$id_tesis){        
        $sql="INSERT INTO cotutela (id_cotutela,prof_guia,inst_cot,fondo,ciudad,pais_cot,tesis)  VALUES (NULL,'$prof_cot','$inst_cot','$fondo','$ciudad_cot','$pais_cot','$id_tesis')";
        return ejecutarConsulta($sql);
    }
    //buscar cogresos registrados
    public function cargarTitulos($busqueda){        
        $sql="SELECT id_tesis,tit_tesis FROM tesis  WHERE  tit_tesis LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    public function mostrarProf($busqueda){        
        $sql="SELECT id_usuario,nombres,ap_pat,ap_mat FROM usuario u INNER JOIN profesor p ON u.id_usuario=p.usuario WHERE nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    public function mostrarEst($busqueda){        
        $sql="SELECT id_usuario,nombres,ap_pat,ap_mat FROM usuario u INNER JOIN estudiante e ON u.id_usuario=e.usuario WHERE nombres LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    //mostrar tesis por id tesis
    public function cargarTesisCotutela($id){        
        $sql="SELECT * FROM tesis t INNER JOIN cotutela c ON t.id_tesis=c.tesis WHERE id_tesis='$id'";
        return ejecutarConsultaResultados($sql);        
    }
    public function cargarTesis($id){        
        $sql="SELECT * FROM tesis  WHERE id_tesis='$id'";
        return ejecutarConsultaResultados($sql);        
    }
    //mostrar tesis por id usuario
    public function mostrarTesisCotutela($usu){        
        $sql="SELECT * , it.inst as inst_tesis, pt.pais as pais_tesis, ic.inst as inst_cot, pc.pais as pais_cot FROM tesis t INNER JOIN cotutela c ON t.id_tesis=c.tesis INNER JOIN institucion it ON t.inst_tesis=it.id_inst INNER JOIN pais pt ON t.pais_tesis=pt.id_pais INNER JOIN institucion ic ON c.inst_cot=ic.id_inst INNER JOIN pais pc ON c.pais_cot=pc.id_pais  WHERE id_prof_guia='$usu' OR id_prof_coguia='$usu'";
        return ejecutarConsultaResultados($sql);        
    }
    public function mostrarTesis($usu){        
        $sql="SELECT * , i.inst as it, p.pais as pt FROM tesis t INNER JOIN institucion i ON t.inst_tesis=i.id_inst INNER JOIN pais p ON t.pais_tesis=p.id_pais  WHERE id_prof_guia='$usu' OR id_prof_coguia='$usu'";
        return ejecutarConsultaResultados($sql);        
    }
    public function mostrarCotutela($id_tesis){        
        $sql="SELECT * , inst as ic, pais as pc FROM  cotutela c  INNER JOIN institucion i ON c.inst_cot=i.id_inst INNER JOIN pais p ON c.pais_cot=p.id_pais  WHERE c.tesis='$id_tesis'";
        return ejecutarConsultaResultados($sql);        
    }

    //update
    public function editarGuia($guia,$id_tesis){
        $sql="UPDATE tesis SET nom_prof_guia=NULL, id_prof_guia='$guia' WHERE id_tesis='$id_tesis'";
        return ejecutarConsulta($sql);
    }
    public function editarCoguia($coguia,$id_tesis){
        $sql="UPDATE tesis SET nom_prof_coguia=NULL, id_prof_coguia='$coguia' WHERE id_tesis='$id_tesis'";
        return ejecutarConsulta($sql);
    }
    // public function editar($id,$nombre){
    //     $sql="UPDATE pueblo SET nombre='$nombre' where id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
    public function mostrar($usuario){
         $sql="SELECT * FROM proyecto_investigacion p INNER JOIN financiamiento f ON p.fuente_financiamiento=f.id_financ INNER JOIN institucion i ON p.inst_proy=i.id_inst   WHERE  id_inv='$usuario' OR id_coinv='$usuario' order by folio";
         return ejecutarConsultaResultados($sql);
    }
    public function cargarProf($usuario){
        $sql="SELECT nombres,ap_pat,ap_mat FROM usuario WHERE id_usuario='$usuario'";
        return ejecutarConsultaResultados($sql);
   }

    // public function eliminar($id){
    //     $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
}
?>