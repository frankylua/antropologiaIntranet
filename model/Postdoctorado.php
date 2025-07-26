<?php
require "../config/conexion.php";
Class Postdoctorado {
    public function __construct(){
    }
    public function insertar($usuario,$prof,$inst,$fech_in,$fech_ter){
        $sql="INSERT INTO postdoctorado (id_postdoc,inst_postdoc,fecha_inicio,fecha_termino,prof,usuario) VALUES (NULL,'$inst','$fech_in','$fech_ter','$prof','$usuario')";
        return ejecutarConsulta($sql);
    }
    // public function editar($id,$nombre){
    //     $sql="UPDATE pueblo SET nombre='$nombre' where id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
    public function mostrar($usuario){
         //consultar pueblos
         $sql="SELECT * FROM postdoctorado p INNER JOIN institucion i ON p.inst_postdoc=i.id_inst WHERE usuario='$usuario'";
         return ejecutarConsultaResultados($sql);
    }
    public function mostrarPostdoc($id_postdoc){
        //consultar pueblos
        $sql="SELECT * FROM postdoctorado p JOIN institucion i ON p.inst_postdoc=i.id_inst WHERE p.id_postdoc='$id_postdoc'";
        return ejecutarConsultaResultados($sql);
   }
    public function editarPostdoc($id_postdoc,$prof,$inst,$fech_in,$fech_ter){
        $sql="UPDATE postdoctorado SET inst_postdoc='$inst', fecha_inicio='$fech_in', fecha_termino='$fech_ter', prof='$prof' where id_postdoc='$id_postdoc'";
        return ejecutarConsulta($sql);

   }
    public function eliminar($id){
        $sql="DELETE FROM postdoctorado  WHERE id_postdoc='$id'";
        return ejecutarConsulta($sql);
    }
}
?>