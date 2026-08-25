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
    bool $obtenerIdInsertado = false,
    array $tipos = []
): array {
    $pdo = conexion();
    $statement = $pdo->prepare($sql);

    if ($tipos === []) {
        $ejecutado = $statement->execute($parametros);
    } else {
        $normalizar = static function (array $valores, string $origen): array {
            $normalizados = [];

            foreach ($valores as $clave => $valor) {
                if (!is_string($clave)) {
                    throw new InvalidArgumentException(
                        "Los parámetros tipados de {$origen} deben utilizar claves nombradas."
                    );
                }

                $nombre = str_starts_with($clave, ':') ? substr($clave, 1) : $clave;
                if ($nombre === '' || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $nombre) !== 1) {
                    throw new InvalidArgumentException(
                        "El placeholder '{$clave}' de {$origen} no es válido."
                    );
                }
                if (array_key_exists($nombre, $normalizados)) {
                    throw new InvalidArgumentException(
                        "El placeholder '{$nombre}' está duplicado después de normalizar {$origen}."
                    );
                }

                $normalizados[$nombre] = $valor;
            }

            return $normalizados;
        };

        $parametrosNormalizados = $normalizar($parametros, 'parametros');
        $tiposNormalizados = $normalizar($tipos, 'tipos');
        $clavesParametros = array_keys($parametrosNormalizados);
        $clavesTipos = array_keys($tiposNormalizados);
        sort($clavesParametros);
        sort($clavesTipos);

        if ($clavesParametros !== $clavesTipos) {
            throw new InvalidArgumentException(
                'Los parámetros y tipos explícitos deben contener exactamente los mismos placeholders.'
            );
        }

        $tiposPermitidos = [
            PDO::PARAM_STR,
            PDO::PARAM_INT,
            PDO::PARAM_BOOL,
            PDO::PARAM_NULL,
            PDO::PARAM_LOB,
        ];

        foreach ($parametrosNormalizados as $nombre => $valor) {
            $tipo = $tiposNormalizados[$nombre];
            if (!is_int($tipo) || !in_array($tipo, $tiposPermitidos, true)) {
                throw new InvalidArgumentException("El tipo PDO de '{$nombre}' no está permitido.");
            }

            $tipoCoherente = match ($tipo) {
                PDO::PARAM_STR => is_string($valor),
                PDO::PARAM_INT => is_int($valor),
                PDO::PARAM_BOOL => is_bool($valor),
                PDO::PARAM_NULL => $valor === null,
                PDO::PARAM_LOB => is_string($valor) || is_resource($valor),
                default => false,
            };
            if (!$tipoCoherente) {
                throw new InvalidArgumentException(
                    "El valor de '{$nombre}' no es coherente con su tipo PDO explícito."
                );
            }

            $statement->bindValue(':' . $nombre, $valor, $tipo);
        }

        $ejecutado = $statement->execute();
    }

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
