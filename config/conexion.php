<?php
require_once "global.php";

function conexion(){
    try {
        $conexion = new PDO('mysql:host=localhost;dbname='.DB_NAME, DB_USERNAME, DB_PASS);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de excepciones
        return $conexion;
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage()); // Log de error de conexión
        return false;
    }
}

if (!function_exists('ejecutarConsulta')) {
    function ejecutarConsulta($sql) { // SQL string
        $conexion = conexion();
        if (!$conexion) {
            return false; // Si no se conecta, retorna falso
        }
        $statement = $conexion->prepare($sql);
        $statement->execute();
        return $statement;
    }
    
    function ejecutarConsultaResultados($sql) { // SQL string
        $conexion = conexion();
        if (!$conexion) {
            return []; // Si no se conecta, devuelve array vacío
        }
        $statement = $conexion->prepare($sql);
        $statement->execute();
        $resultado = $statement->fetchAll(PDO::FETCH_ASSOC); // Devuelve un array asociativo
        return $resultado ? $resultado : []; // Si no hay resultados, devuelve array vacío
    }

    function obtenerIdConsulta($sql) {
        $conexion = conexion();
        if (!$conexion) {
            return false; // Si no hay conexión, retorna falso
        }
        $statement = $conexion->prepare($sql);
        $statement->execute();
        return $conexion->lastInsertId(); // Retorna el ID de la última inserción
    }

    function resultadoConsultaPorId($sql) {
        $conexion = conexion();
        if (!$conexion) {
            return false; // Si no hay conexión, retorna falso
        }
        $statement = $conexion->prepare($sql);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC); // Devuelve solo el primer resultado
    }
}
?>


    

        

?>