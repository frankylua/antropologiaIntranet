<?php
require "../config/conexion.php";
Class Financiamiento {
    public function __construct(){
    }
    public function insertar($nombre){
        $sql="INSERT INTO financiamiento (id_financ,financiamiento) VALUES (NULL,'$nombre')";
        return ejecutarConsulta($sql);
    }
    public function insertarObtenerId($nombre){
        $sql="INSERT INTO financiamiento (id_financ,financiamiento) VALUES (NULL,'$nombre')";
        return obtenerIdConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE financiamientp SET financiamiento='$nombre' where id_financ='$id'";
        return ejecutarConsulta($sql);
    }
    public function mostrar(){
         //consultar pueblos
         $sql="SELECT * FROM financiamiento ORDER BY financiamiento";
         return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id){
        $sql="DELETE FROM financiamiento  WHERE id_financ='$id'";
        return ejecutarConsulta($sql);
    }
}
?>