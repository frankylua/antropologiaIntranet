<?php
require "../config/conexion.php";
Class Beca {
    public function __construct(){
    }
    public function insertar($nombre,$tipo_beca,$inst,$fecha_in,$fecha_ter,$alumno){
        $sql="INSERT INTO beca (id_beca,nom_beca,inst_beca,fech_in,fech_ter,alumno) VALUES (NULL,'$nombre','$inst','$fecha_in','$fecha_ter','$alumno')";
        return ejecutarConsulta($sql);
    }
    public function insertarObtenerId($nombre,$tipo_beca){
        $sql="INSERT INTO nombre_beca (id_nom_beca,beca,tipo_beca) VALUES (NULL,'$nombre','$tipo_beca')";
        return obtenerIdConsulta($sql);
    }
    public function insertarList($nombre,$tipo_int){
        $sql="INSERT INTO nombre_beca (id_nom_beca,beca,tipo_beca) VALUES (NULL,'$nombre','$tipo_int')";
        return ejecutarConsulta($sql);

    }
    public function editar($id,$nombre){
        $sql="UPDATE nombre_beca SET beca='$nombre' where id_nom_beca='$id'";
        return ejecutarConsulta($sql);
    }
    public function mostrar($usuario){
         //consultar pueblos
         $sql="SELECT * FROM beca b INNER JOIN institucion i ON b.inst_beca=i.id_inst INNER JOIN nombre_beca n  ON b.nom_beca=n.id_nom_beca  WHERE  b.alumno='$usuario' ORDER BY n.tipo_beca";
         return ejecutarConsultaResultados($sql);
    }

    public function mostrarLista($tipo){
        //consultar pueblos
        $sql="SELECT * FROM nombre_beca WHERE tipo_beca='$tipo'  ORDER BY beca";
        return ejecutarConsultaResultados($sql);
   }
    //cargar nombre beca
    public function mostrarNomBeca($tipo_beca){
        $sql="SELECT * FROM nombre_beca WHERE tipo_beca = '$tipo_beca' ORDER BY beca";
        return ejecutarConsultaResultados($sql);

    }
    // public function eliminar($id){
    //     $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
}
?>