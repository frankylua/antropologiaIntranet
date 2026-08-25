<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit(64);
}

require_once dirname(__DIR__) . '/src/bootstrap/app.php';

use App\Model\Curso;
use App\Model\Documento;

const ROLLBACK_DOC_MAX_BYTES = 5242880;
const ROLLBACK_EXIT_USAGE = 64;
const ROLLBACK_EXIT_DATA = 65;
const ROLLBACK_EXIT_TECHNICAL = 70;
const ROLLBACK_EXIT_FILESYSTEM = 74;

final class RollbackDocumentoException extends RuntimeException
{
    public function __construct(string $message, public readonly int $exitCode)
    {
        parent::__construct($message);
    }
}

function rollbackUso(): string
{
    return 'Uso: php ' . basename(__FILE__)
        . ' --dry-run|--execute [--database=<schema> --output-dir=<ruta>]';
}

/** @return array{help:bool,modo:?string,database:?string,output_dir:?string} */
function rollbackParsearArgumentos(array $argv): array
{
    $argumentos = array_slice($argv, 1);
    if ($argumentos === ['--help']) {
        return ['help' => true, 'modo' => null, 'database' => null, 'output_dir' => null];
    }

    $modo = null;
    $database = null;
    $outputDir = null;
    $databaseRecibida = false;
    $outputDirRecibido = false;

    foreach ($argumentos as $argumento) {
        if (in_array($argumento, ['--dry-run', '--execute'], true)) {
            if ($modo !== null) {
                throw new RollbackDocumentoException(
                    'Debe indicar exactamente un modo de ejecuciÃ³n.',
                    ROLLBACK_EXIT_USAGE
                );
            }
            $modo = $argumento;
            continue;
        }
        if (str_starts_with($argumento, '--database=')) {
            if ($databaseRecibida) {
                throw new RollbackDocumentoException(
                    'La opciÃ³n --database no puede repetirse.',
                    ROLLBACK_EXIT_USAGE
                );
            }
            $databaseRecibida = true;
            $database = substr($argumento, strlen('--database='));
            continue;
        }
        if (str_starts_with($argumento, '--output-dir=')) {
            if ($outputDirRecibido) {
                throw new RollbackDocumentoException(
                    'La opciÃ³n --output-dir no puede repetirse.',
                    ROLLBACK_EXIT_USAGE
                );
            }
            $outputDirRecibido = true;
            $outputDir = substr($argumento, strlen('--output-dir='));
            continue;
        }
        throw new RollbackDocumentoException(
            'La opciÃ³n indicada no estÃ¡ permitida.',
            ROLLBACK_EXIT_USAGE
        );
    }

    if ($modo === null) {
        throw new RollbackDocumentoException(
            'Debe indicar --dry-run o --execute.',
            ROLLBACK_EXIT_USAGE
        );
    }
    if ($databaseRecibida !== $outputDirRecibido) {
        throw new RollbackDocumentoException(
            'Los overrides --database y --output-dir deben utilizarse juntos.',
            ROLLBACK_EXIT_USAGE
        );
    }

    return [
        'help' => false,
        'modo' => $modo,
        'database' => $database,
        'output_dir' => $outputDir,
    ];
}

function rollbackDatabaseEfectiva(PDO $pdo): string
{
    $database = $pdo->query('SELECT DATABASE()')->fetchColumn();
    if (!is_string($database) || $database === '') {
        throw new RollbackDocumentoException(
            'No fue posible determinar la database efectiva.',
            ROLLBACK_EXIT_TECHNICAL
        );
    }
    return $database;
}

function rollbackNombreSchemaSeguro(string $database): bool
{
    return $database !== ''
        && strlen($database) <= 64
        && preg_match('/^[A-Za-z0-9_]+$/D', $database) === 1;
}

function rollbackSeleccionarDatabase(PDO $pdo, string $database, string $databaseConfigurada): string
{
    if (!rollbackNombreSchemaSeguro($database)) {
        throw new RollbackDocumentoException(
            'El identificador recibido en --database no es vÃ¡lido.',
            ROLLBACK_EXIT_USAGE
        );
    }
    if (strcasecmp($database, $databaseConfigurada) === 0) {
        throw new RollbackDocumentoException(
            'El override --database no puede apuntar a la database activa configurada.',
            ROLLBACK_EXIT_USAGE
        );
    }

    $consulta = $pdo->prepare(
        'SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = :schema'
    );
    $consulta->bindValue(':schema', $database, PDO::PARAM_STR);
    $consulta->execute();
    $schema = $consulta->fetchColumn();
    if (!is_string($schema) || !hash_equals($database, $schema)) {
        throw new RollbackDocumentoException(
            'La database solicitada no existe con el identificador exacto indicado.',
            ROLLBACK_EXIT_USAGE
        );
    }

    $pdo->exec('USE `' . $database . '`');
    $efectiva = rollbackDatabaseEfectiva($pdo);
    if (!hash_equals($database, $efectiva)) {
        throw new RollbackDocumentoException(
            'La database efectiva no coincide con --database.',
            ROLLBACK_EXIT_TECHNICAL
        );
    }
    return $efectiva;
}

function rollbackVerificarDatabaseEfectiva(PDO $pdo, string $esperada): void
{
    if (!hash_equals($esperada, rollbackDatabaseEfectiva($pdo))) {
        throw new RollbackDocumentoException(
            'La database efectiva cambiÃ³ antes de una escritura.',
            ROLLBACK_EXIT_TECHNICAL
        );
    }
}

function rollbackRutasIguales(string $izquierda, string $derecha): bool
{
    if (DIRECTORY_SEPARATOR === '\\') {
        return strcasecmp($izquierda, $derecha) === 0;
    }
    return strcmp($izquierda, $derecha) === 0;
}

function rollbackRutaContenida(string $base, string $ruta): bool
{
    if (rollbackRutasIguales($base, $ruta)) {
        return true;
    }
    return rollbackRutaConfinada($base, $ruta . DIRECTORY_SEPARATOR);
}

function rollbackResolverOutput(
    string $outputSolicitado,
    string $outputActivo
): string {
    if ($outputSolicitado === '' || str_contains($outputSolicitado, "\0") || is_link($outputSolicitado)) {
        throw new RollbackDocumentoException(
            'El override --output-dir no es un directorio seguro.',
            ROLLBACK_EXIT_USAGE
        );
    }
    $outputEfectivo = realpath($outputSolicitado);
    if (
        $outputEfectivo === false
        || !is_dir($outputEfectivo)
        || !is_readable($outputEfectivo)
        || !is_writable($outputEfectivo)
    ) {
        throw new RollbackDocumentoException(
            'El override --output-dir debe existir y ser legible/escribible.',
            ROLLBACK_EXIT_FILESYSTEM
        );
    }
    $outputEfectivo = rtrim($outputEfectivo, DIRECTORY_SEPARATOR);
    if (
        rollbackRutaContenida($outputActivo, $outputEfectivo)
        || rollbackRutaContenida($outputEfectivo, $outputActivo)
    ) {
        throw new RollbackDocumentoException(
            'El override --output-dir debe estar separado del destino legacy activo.',
            ROLLBACK_EXIT_USAGE
        );
    }
    return $outputEfectivo;
}

function rollbackSalida(int $idCurso, string $estado, string $resultado): void
{
    fwrite(
        STDOUT,
        'id_curso=' . $idCurso . ' estado=' . $estado . ' resultado=' . $resultado . PHP_EOL
    );
}

function rollbackNombreSeguro(string $nombre): bool
{
    return $nombre !== ''
        && strlen($nombre) <= 45
        && !in_array($nombre, ['.', '..'], true)
        && !str_contains($nombre, "\0")
        && !str_contains($nombre, '/')
        && !str_contains($nombre, '\\')
        && preg_match('/[\x00-\x1F\x7F]/', $nombre) !== 1
        && basename($nombre) === $nombre;
}

function rollbackRutaConfinada(string $base, string $ruta): bool
{
    $prefijo = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (DIRECTORY_SEPARATOR === '\\') {
        return strncasecmp($ruta, $prefijo, strlen($prefijo)) === 0;
    }
    return strncmp($ruta, $prefijo, strlen($prefijo)) === 0;
}

/** @param array<string, mixed> $metadata */
function rollbackFallbackEquivalente(string $base, mixed $referencia, array $metadata): bool
{
    if (!is_string($referencia) || !rollbackNombreSeguro($referencia)) {
        return false;
    }
    $ruta = realpath($base . DIRECTORY_SEPARATOR . $referencia);
    if (
        $ruta === false
        || !rollbackRutaConfinada($base, $ruta)
        || !is_file($ruta)
        || !is_readable($ruta)
    ) {
        return false;
    }
    $tamanio = @filesize($ruta);
    if (
        !is_int($tamanio)
        || $tamanio < 1
        || $tamanio > ROLLBACK_DOC_MAX_BYTES
        || $tamanio !== (int) ($metadata['tamanio'] ?? 0)
        || @(new finfo(FILEINFO_MIME_TYPE))->file($ruta) !== 'application/pdf'
    ) {
        return false;
    }

    $checksum = @hash_file('sha256', $ruta);
    return is_string($checksum)
        && hash_equals((string) ($metadata['checksum_sha256'] ?? ''), $checksum);
}

/** @return array{nombre:string,ruta:string,recurso:resource} */
function rollbackCrearDestinoExclusivo(string $base): array
{
    for ($intento = 0; $intento < 20; $intento++) {
        $nombre = 'curso_' . bin2hex(random_bytes(16)) . '.pdf';
        $ruta = $base . DIRECTORY_SEPARATOR . $nombre;
        clearstatcache(true, $ruta);
        if (file_exists($ruta)) {
            continue;
        }

        $recurso = @fopen($ruta, 'x+b');
        if (is_resource($recurso)) {
            return ['nombre' => $nombre, 'ruta' => $ruta, 'recurso' => $recurso];
        }

        clearstatcache(true, $ruta);
        if (!file_exists($ruta)) {
            throw new RollbackDocumentoException(
                'No fue posible crear de forma exclusiva el archivo final.',
                ROLLBACK_EXIT_FILESYSTEM
            );
        }
    }
    throw new RollbackDocumentoException(
        'No fue posible obtener un basename final exclusivo.',
        ROLLBACK_EXIT_FILESYSTEM
    );
}

/** @return array{ruta:string,recurso:resource} */
function rollbackCrearTemporalExclusivo(string $base): array
{
    for ($intento = 0; $intento < 20; $intento++) {
        $ruta = $base . DIRECTORY_SEPARATOR . '.curso_' . bin2hex(random_bytes(16)) . '.tmp';
        $recurso = @fopen($ruta, 'x+b');
        if (is_resource($recurso)) {
            return ['ruta' => $ruta, 'recurso' => $recurso];
        }
        clearstatcache(true, $ruta);
        if (!file_exists($ruta)) {
            throw new RollbackDocumentoException(
                'No fue posible crear de forma exclusiva el archivo temporal.',
                ROLLBACK_EXIT_FILESYSTEM
            );
        }
    }
    throw new RollbackDocumentoException(
        'No fue posible obtener un nombre temporal exclusivo.',
        ROLLBACK_EXIT_FILESYSTEM
    );
}

function rollbackArchivoCoincide(string $ruta, int $tamanio, string $checksum): bool
{
    clearstatcache(true, $ruta);
    $checksumFisico = @hash_file('sha256', $ruta);
    return is_file($ruta)
        && is_readable($ruta)
        && @filesize($ruta) === $tamanio
        && @(new finfo(FILEINFO_MIME_TYPE))->file($ruta) === 'application/pdf'
        && is_string($checksumFisico)
        && hash_equals($checksum, $checksumFisico);
}

function rollbackLimpiarArchivoCreado(?string $ruta): ?string
{
    if ($ruta === null) {
        return null;
    }
    clearstatcache(true, $ruta);
    if (!file_exists($ruta) && !is_link($ruta)) {
        return null;
    }
    if (@unlink($ruta)) {
        return null;
    }
    return basename($ruta);
}

/** @param array<string, mixed>|null $metadata */
function rollbackMetadataValida(?array $metadata, int $idDocumento): bool
{
    return $metadata !== null
        && (int) ($metadata['id_documento'] ?? 0) === $idDocumento
        && ($metadata['mime_type'] ?? null) === 'application/pdf'
        && (int) ($metadata['tamanio'] ?? 0) >= 1
        && (int) ($metadata['tamanio'] ?? 0) <= ROLLBACK_DOC_MAX_BYTES
        && is_string($metadata['checksum_sha256'] ?? null)
        && preg_match('/^[a-f0-9]{64}$/D', $metadata['checksum_sha256']) === 1;
}

function rollbackEscribirCompleto($recurso, string $bytes): void
{
    $total = strlen($bytes);
    $offset = 0;
    while ($offset < $total) {
        $escritos = @fwrite($recurso, substr($bytes, $offset));
        if (!is_int($escritos) || $escritos < 1) {
            throw new RollbackDocumentoException(
                'No fue posible escribir completamente el archivo de salida.',
                ROLLBACK_EXIT_FILESYSTEM
            );
        }
        $offset += $escritos;
    }
}

function rollbackMain(array $argv): int
{
    try {
        $opciones = rollbackParsearArgumentos($argv);
    } catch (RollbackDocumentoException $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL . rollbackUso() . PHP_EOL);
        return $error->exitCode;
    }
    if ($opciones['help']) {
        fwrite(STDOUT, rollbackUso() . PHP_EOL);
        return 0;
    }
    $ejecutar = $opciones['modo'] === '--execute';

    $outputActivo = realpath(dirname(__DIR__) . '/files/prog_curso');
    if (
        $outputActivo === false
        || !is_dir($outputActivo)
        || !is_readable($outputActivo)
        || ($ejecutar && $opciones['output_dir'] === null && !is_writable($outputActivo))
    ) {
        fwrite(STDERR, 'files/prog_curso no cumple los requisitos del modo solicitado.' . PHP_EOL);
        return ROLLBACK_EXIT_FILESYSTEM;
    }
    $outputActivo = rtrim($outputActivo, DIRECTORY_SEPARATOR);

    try {
        $base = $opciones['output_dir'] === null
            ? $outputActivo
            : rollbackResolverOutput($opciones['output_dir'], $outputActivo);
    } catch (RollbackDocumentoException $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
        return $error->exitCode;
    }

    $pdo = conexion();
    try {
        $databaseConfigurada = rollbackDatabaseEfectiva($pdo);
        $databaseEfectiva = $opciones['database'] === null
            ? $databaseConfigurada
            : rollbackSeleccionarDatabase($pdo, $opciones['database'], $databaseConfigurada);
        if (conexion() !== $pdo) {
            throw new RollbackDocumentoException(
                'La conexiÃ³n reutilizada no conserva una instancia PDO Ãºnica.',
                ROLLBACK_EXIT_TECHNICAL
            );
        }
        rollbackVerificarDatabaseEfectiva($pdo, $databaseEfectiva);
    } catch (Throwable $error) {
        fwrite(STDERR, 'No fue posible seleccionar la database: ' . $error->getMessage() . PHP_EOL);
        return $error instanceof RollbackDocumentoException
            ? $error->exitCode
            : ROLLBACK_EXIT_TECHNICAL;
    }
    fwrite(STDOUT, 'database_configurada=' . $databaseConfigurada . PHP_EOL);
    fwrite(STDOUT, 'database_efectiva=' . $databaseEfectiva . PHP_EOL);
    fwrite(STDOUT, 'output_configurado=' . $outputActivo . PHP_EOL);
    fwrite(STDOUT, 'output_efectivo=' . $base . PHP_EOL);

    $curso = new Curso();
    $documento = new Documento();
    try {
        $consulta = $pdo->prepare(
            'SELECT id_curso, arch_prog, id_documento_programa FROM curso '
            . 'WHERE id_documento_programa IS NOT NULL ORDER BY id_curso'
        );
        $consulta->execute();
        $candidatos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $error) {
        fwrite(STDERR, 'No fue posible inventariar Documentos: ' . $error->getMessage() . PHP_EOL);
        return ROLLBACK_EXIT_TECHNICAL;
    }

    $planes = [];
    foreach ($candidatos as $fila) {
        $idCurso = (int) ($fila['id_curso'] ?? 0);
        $idDocumento = (int) ($fila['id_documento_programa'] ?? 0);
        if ($idCurso < 1 || $idDocumento < 1) {
            rollbackSalida($idCurso, 'INVALIDO', 'IDENTIFICADOR_INVALIDO');
            return ROLLBACK_EXIT_DATA;
        }
        try {
            $metadata = $documento->obtenerMetadata($idDocumento);
        } catch (Throwable $error) {
            rollbackSalida($idCurso, 'ERROR', 'PREFLIGHT_DOCUMENTO_NO_DISPONIBLE');
            fwrite(STDERR, $error->getMessage() . PHP_EOL);
            return ROLLBACK_EXIT_TECHNICAL;
        }
        if (!rollbackMetadataValida($metadata, $idDocumento)) {
            rollbackSalida($idCurso, 'INVALIDO', 'METADATA_DOCUMENTO_INVALIDA');
            return ROLLBACK_EXIT_DATA;
        }
        if (rollbackFallbackEquivalente($base, $fila['arch_prog'] ?? null, $metadata)) {
            rollbackSalida($idCurso, 'OMITIDO', 'FALLBACK_EQUIVALENTE');
            continue;
        }
        $planes[] = ['id_curso' => $idCurso, 'id_documento' => $idDocumento];
        rollbackSalida($idCurso, 'PLANIFICADO', 'EXPORTAR_DOCUMENTO');
    }

    if (!$ejecutar) {
        fwrite(STDOUT, 'dry-run=PASS exportaciones=' . count($planes) . PHP_EOL);
        return 0;
    }

    foreach ($planes as $plan) {
        $idCurso = $plan['id_curso'];
        $idDocumento = $plan['id_documento'];
        $temporal = null;
        $final = null;
        $recurso = null;
        try {
            rollbackVerificarDatabaseEfectiva($pdo, $databaseEfectiva);
            $pdo->beginTransaction();
            $bloqueo = $pdo->prepare(
                'SELECT id_curso, arch_prog, id_documento_programa '
                . 'FROM curso WHERE id_curso = :id_curso FOR UPDATE'
            );
            $bloqueo->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
            $bloqueo->execute();
            $filaCurso = $bloqueo->fetch(PDO::FETCH_ASSOC);
            if ($filaCurso === false || (int) $filaCurso['id_documento_programa'] !== $idDocumento) {
                throw new RollbackDocumentoException(
                    'La asociación Curso/Documento cambió después del dry-run.',
                    ROLLBACK_EXIT_DATA
                );
            }
            $metadata = $documento->obtenerMetadata($idDocumento, true);
            if (!rollbackMetadataValida($metadata, $idDocumento)) {
                throw new RollbackDocumentoException(
                    'Documento ausente o con metadata inválida.',
                    ROLLBACK_EXIT_DATA
                );
            }
            if (rollbackFallbackEquivalente($base, $filaCurso['arch_prog'] ?? null, $metadata)) {
                $pdo->rollBack();
                rollbackSalida($idCurso, 'OMITIDO', 'FALLBACK_EQUIVALENTE_CONCURRENTE');
                continue;
            }

            $contenido = $documento->obtenerContenido($idDocumento);
            if ($contenido === null) {
                throw new RollbackDocumentoException(
                    'Documento ausente o con contenido inválido.',
                    ROLLBACK_EXIT_DATA
                );
            }
            $bytes = $contenido['archivo'] ?? null;
            $tamanio = (int) $metadata['tamanio'];
            $checksum = (string) $metadata['checksum_sha256'];
            if (
                !is_string($bytes)
                || strlen($bytes) !== $tamanio
                || (int) ($contenido['tamanio'] ?? 0) !== $tamanio
                || ($contenido['mime_type'] ?? null) !== 'application/pdf'
                || !hash_equals($checksum, (string) ($contenido['checksum_sha256'] ?? ''))
                || (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes) !== 'application/pdf'
                || !hash_equals($checksum, hash('sha256', $bytes))
            ) {
                throw new RollbackDocumentoException(
                    'Los bytes no coinciden con tamaño, MIME o checksum.',
                    ROLLBACK_EXIT_DATA
                );
            }

            $creacionTemporal = rollbackCrearTemporalExclusivo($base);
            $temporal = $creacionTemporal['ruta'];
            $recurso = $creacionTemporal['recurso'];
            rollbackEscribirCompleto($recurso, $bytes);
            if (!@fflush($recurso)) {
                throw new RollbackDocumentoException(
                    'No fue posible vaciar el archivo temporal.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }
            fclose($recurso);
            $recurso = null;

            if (!rollbackArchivoCoincide($temporal, $tamanio, $checksum)) {
                throw new RollbackDocumentoException(
                    'El archivo temporal no superó la verificación.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }

            $creacionFinal = rollbackCrearDestinoExclusivo($base);
            $nombre = $creacionFinal['nombre'];
            $final = $creacionFinal['ruta'];
            $recurso = $creacionFinal['recurso'];
            rollbackEscribirCompleto($recurso, $bytes);
            if (!@fflush($recurso)) {
                throw new RollbackDocumentoException(
                    'No fue posible vaciar el archivo final.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }
            fclose($recurso);
            $recurso = null;
            if (!rollbackArchivoCoincide($final, $tamanio, $checksum)) {
                throw new RollbackDocumentoException(
                    'El archivo final no superó la verificación.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }
            if (!@unlink($temporal)) {
                throw new RollbackDocumentoException(
                    'No fue posible limpiar el temporal después de publicar el fallback.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }
            $temporal = null;

            rollbackVerificarDatabaseEfectiva($pdo, $databaseEfectiva);
            $actualizacion = $curso->actualizarProgramaLegacy($idCurso, $nombre);
            if ((int) $actualizacion['filasAfectadas'] !== 1) {
                throw new RollbackDocumentoException(
                    'arch_prog no se actualizó exactamente una vez.',
                    ROLLBACK_EXIT_TECHNICAL
                );
            }
            if (!rollbackFallbackEquivalente($base, $nombre, $metadata)) {
                throw new RollbackDocumentoException(
                    'El fallback publicado no quedó disponible.',
                    ROLLBACK_EXIT_FILESYSTEM
                );
            }
            if ($pdo->commit() !== true) {
                throw new RollbackDocumentoException(
                    'No fue posible confirmar arch_prog.',
                    ROLLBACK_EXIT_TECHNICAL
                );
            }

            $final = null;
            rollbackSalida($idCurso, 'EXPORTADO', 'PASS');
        } catch (Throwable $error) {
            if (is_resource($recurso)) {
                fclose($recurso);
            }
            $erroresRecuperacion = [];
            if ($pdo->inTransaction()) {
                try {
                    $pdo->rollBack();
                } catch (Throwable) {
                    $erroresRecuperacion[] = 'ROLLBACK_BD_NO_CONFIRMADO';
                }
            }
            $residualTemporal = rollbackLimpiarArchivoCreado($temporal);
            if ($residualTemporal !== null) {
                $erroresRecuperacion[] = 'ARTEFACTO_RESIDUAL=' . $residualTemporal;
            }
            $residualFinal = rollbackLimpiarArchivoCreado($final);
            if ($residualFinal !== null) {
                $erroresRecuperacion[] = 'ARTEFACTO_RESIDUAL=' . $residualFinal;
            }
            if ($erroresRecuperacion !== []) {
                $detalle = implode(',', $erroresRecuperacion);
                fwrite(STDERR, 'Recuperación incompleta: ' . $detalle . PHP_EOL);
                rollbackSalida($idCurso, 'ERROR_CLEANUP', $detalle);
                return ROLLBACK_EXIT_TECHNICAL;
            }
            rollbackSalida($idCurso, 'ERROR', $error->getMessage());
            return $error instanceof RollbackDocumentoException
                ? $error->exitCode
                : ROLLBACK_EXIT_TECHNICAL;
        }
    }

    try {
        $postflight = $pdo->prepare(
            'SELECT id_curso, arch_prog, id_documento_programa FROM curso '
            . 'WHERE id_documento_programa IS NOT NULL ORDER BY id_curso'
        );
        $postflight->execute();
        foreach ($postflight->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $idDocumento = (int) ($fila['id_documento_programa'] ?? 0);
            $metadata = $documento->obtenerMetadata($idDocumento);
            if (
                !rollbackMetadataValida($metadata, $idDocumento)
                || !rollbackFallbackEquivalente($base, $fila['arch_prog'] ?? null, $metadata)
            ) {
                rollbackSalida((int) $fila['id_curso'], 'SIN_COBERTURA', 'POSTFLIGHT_FAIL');
                return ROLLBACK_EXIT_FILESYSTEM;
            }
        }
    } catch (Throwable $error) {
        fwrite(STDERR, 'No fue posible ejecutar el postflight: ' . $error->getMessage() . PHP_EOL);
        return ROLLBACK_EXIT_TECHNICAL;
    }
    fwrite(STDOUT, 'postflight_cobertura=PASS' . PHP_EOL);

    return 0;
}

exit(rollbackMain($argv));
