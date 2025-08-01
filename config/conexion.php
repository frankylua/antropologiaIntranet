<?php
require_once "global.php";
function conexion(){
    try {
        $conexion = new PDO('mysql:host=localhost;dbname='.DB_NAME,DB_USERNAME,DB_PASS);
        return $conexion;
    } catch (PDOException $e) {
        return false;
    }
}


if(!function_exists('ejecutarConsulta')){
    function ejecutarConsulta($sql){//sql string,arr_datos array
        $conexion =conexion();
        $statement=$conexion->prepare($sql);
        $statement->execute();
        return $statement;
    }
        
    function ejecutarConsultaResultados($sql){//sql string,arr_datos array
        $conexion =conexion();
        $statement=$conexion->prepare($sql);
        $statement->execute();
        $resultado=$statement->fetchAll();
        return $resultado;
    }

    function obtenerIdConsulta($sql){
        $conexion=conexion();
        $statement=$conexion->prepare($sql);
        $statement->execute();
        $resultado=$conexion->lastInsertId();
        return $resultado;
    }

    function resultadoConsultaPorId($sql){
        $conexion =conexion();
        $statement=$conexion->prepare($sql);
        $statement->execute();
        $resultado=$statement->fetch();
        return $resultado;
    }
        
      
}


    

        

?>