<?php
require "../config/conexion.php";
Class Institucion {
    public function __construct(){
    }
    public function insertar($nombre){
        $sql="INSERT INTO institucion (id_inst,inst) VALUES (NULL,'$nombre')";
        return ejecutarConsulta($sql);
    }
    public function insertarObtenerId($nombre){
        $sql="INSERT INTO institucion (id_inst,inst) VALUES (NULL,'$nombre')";
        return obtenerIdConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE institucion SET inst='$nombre' where id_inst='$id'";
        return ejecutarConsulta($sql);
    }
    public function mostrarConsultaOrdenada(){
         //consultar pueblos
         $sql1="SELECT * FROM institucion WHERE inst = 'universidad catolica del norte' OR inst='universidad de tarapaca'";
         $sql2="SELECT * FROM institucion WHERE inst LIKE 'universidad%' AND inst!='universidad de tarapaca' AND inst != 'universidad catolica del norte' ORDER BY inst";
         $sql3="SELECT * FROM institucion WHERE inst NOT LIKE 'universidad%' AND inst!='universidad de tarapaca' AND inst != 'universidad catolica del norte' ORDER BY inst";
         $respuesta1=ejecutarConsultaResultados($sql1);
         $respuesta2=ejecutarConsultaResultados($sql2);
         $respuesta3=ejecutarConsultaResultados($sql3);
         $institutos = array_merge_recursive($respuesta1,$respuesta2,$respuesta3);
         return $institutos;

    }
    public function eliminar($id){
        $sql="DELETE FROM institucion  WHERE id_inst='$id'";
        return ejecutarConsulta($sql);
    }
}
?>