<?php
declare(strict_types=1);
namespace App\Model;

class Permiso {
  
    
    public function __contruct(){

    }
   
    public function listar(){
        $sql="SELECT * FROM permiso";
        return ejecutarConsultaResultados($sql);
    }


}
