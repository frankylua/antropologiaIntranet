<?php
require "../config/conexion.php";
Class Pasantia {
    public function __construct(){
    }
    public function insertar($usuario,$prof,$inst,$fech_in,$fech_ter,$ciudad,$fondo,$pais){
        $sql="INSERT INTO pasantia (id_pasantia,inst_pasant,fech_in,fech_ter,prof_patr,fondo,ciudad,pais_pasant,usuario) VALUES (NULL,'$inst','$fech_in','$fech_ter','$prof','$fondo','$ciudad','$pais','$usuario')";
        return ejecutarConsulta($sql);
    }
    // public function editar($id,$nombre){
    //     $sql="UPDATE pueblo SET nombre='$nombre' where id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
    public function mostrar($usuario){
         //consultar pueblos
         $sql="SELECT * FROM pasantia p INNER JOIN institucion i ON p.inst_pasant=i.id_inst INNER JOIN pais pa ON p.pais_pasant=pa.id_pais   WHERE usuario='$usuario'";
         return ejecutarConsultaResultados($sql);
    }
    // public function eliminar($id){
    //     $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
}
?>