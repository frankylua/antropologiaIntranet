<?php
require "../config/conexion.php";
Class Titulo {
    public function __construct(){
    }
    public function insertar($nombre,$tipo_grado){
        $sql="INSERT INTO titulo_grado (id_titulo,tit_grado,tipo_grado) VALUES (NULL,'$nombre','$tipo_grado')";
        return ejecutarConsulta($sql);
    }
    public function insertarObtenerId($nombre,$tipo_grado){
        $sql="INSERT INTO titulo_grado (id_titulo,tit_grado,tipo_grado) VALUES (NULL,'$nombre','$tipo_grado')";
        return obtenerIdConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE titulo_grado SET tit_grado='$nombre' where id_titulo='$id'";
        return ejecutarConsulta($sql);
    }
    public function mostrarPorGrado($tipo){
         //consultar pueblos
         $sql="SELECT * FROM titulo_grado WHERE tipo_grado = '$tipo' ORDER BY tit_grado";
         return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id){
        $sql="DELETE FROM titulo_grado  WHERE id_titulo='$id'";
        return ejecutarConsulta($sql);
    }
}
?>