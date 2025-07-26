<?php
require "../config/conexion.php";
Class Pueblo {
    public function __construct(){
    }
    public function insertar($nombre){
        $sql="INSERT INTO pueblo (id_pueblo,pueblo) VALUES (NULL,'$nombre')";
        return ejecutarConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE pueblo SET pueblo='$nombre' where id_pueblo='$id'";
        return ejecutarConsulta($sql);
    }
    public function mostrar(){
         //consultar pueblos
         $sql="SELECT * FROM pueblo ORDER BY pueblo";
         return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id){
        $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
        return ejecutarConsulta($sql);
    }
}
?>


    
    
        
        


        
         
         

        
    
    
    
    