<?php
declare(strict_types=1);

use App\Config\ConnectionAuthority;

/**
 * Devuelve una conexión PDO singleton, con charset utf8mb4,
 * excepciones habilitadas y modo emulación desactivado.
 */
function conexion(): PDO {
    try {
        return ConnectionAuthority::connection(app_config());
    } catch (PDOException $e) {
        if (app_config('APP_ENV') === 'dev') {
            throw $e; // en dev, ver el error real
        }
        error_log('[DB] ' . $e->getMessage());
        http_response_code(500);
        exit('Error de conexión a la base de datos.');
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


    

        

function ejecutarEscritura(
    string $sql,
    array $parametros = [],
    bool $obtenerIdInsertado = false
): array {
    $pdo = conexion();
    $statement = $pdo->prepare($sql);
    $ejecutado = $statement->execute($parametros);

    if ($ejecutado === false) {
        throw new RuntimeException('No fue posible completar la operación de escritura.');
    }

    $idInsertado = null;

    if ($obtenerIdInsertado === true) {
        $ultimoIdInsertado = $pdo->lastInsertId();
        $idInsertado = $ultimoIdInsertado === false ? null : $ultimoIdInsertado;
    }

    return [
        'filasAfectadas' => $statement->rowCount(),
        'idInsertado' => $idInsertado,
    ];
}
?>
