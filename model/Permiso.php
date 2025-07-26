<?php
require "../config/conexion.php";

Class Permiso {
  
    
    public function __contruct(){

    }
   
    public function listar(){
        $sql="SELECT * FROM permiso";
        return ejecutarConsultaResultados($sql);
    }


}
