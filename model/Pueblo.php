<?php
require "../config/conexion.php";

class Pueblo {
    public function __construct() {
    }

    // Método para insertar un pueblo
    public function insertar($nombre) {
        $sql = "INSERT INTO pueblo (id_pueblo, pueblo) VALUES (NULL, '$nombre')";
        return ejecutarConsulta($sql);
    }

    // Método para editar un pueblo
    public function editar($id, $nombre) {
        $sql = "UPDATE pueblo SET pueblo = '$nombre' WHERE id_pueblo = '$id'";
        return ejecutarConsulta($sql);
    }

    // Método para mostrar todos los pueblos
    public function mostrar() {
        $sql = "SELECT * FROM pueblo ORDER BY pueblo";
        return ejecutarConsultaResultados($sql); // Devuelve array de resultados
    }

    // Método para eliminar un pueblo
    public function eliminar($id) {
        $sql = "DELETE FROM pueblo WHERE id_pueblo = '$id'";
        return ejecutarConsulta($sql);
    }
}
?>



    
    
        
        


        
         
         

        
    
    
    
    