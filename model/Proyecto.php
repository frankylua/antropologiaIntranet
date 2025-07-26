<?php
require "../config/conexion.php";
Class Proyecto {
    public function __construct(){
    }
    //insert
    public function insertar($folio,$anio,$duracion,$titulo,$nom_inv,$nom_coinv,$inv,$coinv,$inst,$financ){  
        
        $sql="INSERT INTO proyecto_investigacion (id_proyecto,titulo,folio,fuente_financiamiento,anio_adjud,duracion,inst_proy,id_inv,id_coinv,nom_inv,nom_coinv) VALUES (NULL,'$titulo','$folio','$financ','$anio','$duracion'," . (empty($inst)? "NULL":"'$inst'"). "," . (empty($inv)? "NULL":"'$inv'"). "," . (empty($coinv)? "NULL":"'$coinv'"). "," . (empty($nom_inv)? "NULL":"'$nom_inv'"). "," . (empty($nom_coinv)? "NULL":"'$nom_coinv'").")";
        return ejecutarConsulta($sql);
    }
    //buscar cogresos registrados
    public function cargarFolio($busqueda){        
        $sql="SELECT id_proyecto, folio FROM proyecto_investigacion WHERE folio LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    public function cargarProyecto($id){        
        $sql="SELECT * FROM proyecto_investigacion WHERE id_proyecto='$id'";
        return ejecutarConsultaResultados($sql);        
    }

    //update
    public function editarInv($inv,$id_proy){
        $sql="UPDATE proyecto_investigacion SET nom_inv=NULL, id_inv='$inv' WHERE id_proyecto='$id_proy'";
        return ejecutarConsulta($sql);
    }
    public function editarCoinv($coinv,$id_proy,$inst){
        $sql="UPDATE proyecto_investigacion SET nom_coinv=NULL, id_coinv='$coinv', inst_proy='$inst' WHERE id_proyecto='$id_proy'";
        return ejecutarConsulta($sql);
    }
    // public function editar($id,$nombre){
    //     $sql="UPDATE pueblo SET nombre='$nombre' where id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
    public function mostrar($usuario){
         $sql="SELECT * FROM proyecto_investigacion p INNER JOIN financiamiento f ON p.fuente_financiamiento=f.id_financ  WHERE  id_inv='$usuario' OR id_coinv='$usuario' order by folio";
         return ejecutarConsultaResultados($sql);
    }
    public function mostrarInst($id_inst){
        $sql="SELECT * FROM institucion WHERE id_inst='$id_inst'";
        return ejecutarConsultaResultados($sql);
   }

    public function mostrarInvCoinv($usuario){
        $sql="SELECT nombres,ap_pat,ap_mat FROM usuario WHERE id_usuario='$usuario'";
        return ejecutarConsultaResultados($sql);
   }

    // public function eliminar($id){
    //     $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
}
?>