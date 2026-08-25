<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit(64);
}

require_once dirname(__DIR__) . '/src/bootstrap/app.php';

use App\Model\Curso;
use App\Model\Documento;

const MIG2_MAX_BYTES = 5242880;
const MIG2_EXIT_USAGE = 64;
const MIG2_EXIT_DATA = 65;
const MIG2_EXIT_TECHNICAL = 70;
const MIG2_EXIT_FILESYSTEM = 74;

final class Mig2DocumentoException extends RuntimeException
{
    public function __construct(string $message, public readonly int $exitCode)
    {
        parent::__construct($message);
    }
}

function mig2Salida(int $idCurso, string $estado, ?int $tamanio, ?string $checksum, string $resultado): void
{
    $partes = [
        'id_curso=' . $idCurso,
        'estado=' . $estado,
        'tamanio=' . ($tamanio === null ? '-' : (string) $tamanio),
    ];
    if ($checksum !== null) {
        $partes[] = 'checksum=' . $checksum;
    }
    $partes[] = 'resultado=' . $resultado;
    fwrite(STDOUT, implode(' ', $partes) . PHP_EOL);
}

/** @param array{candidatos:int,migrados:int,faltantes:int,omitidos_fk:int,errores:int} $conteos */
function mig2Resumen(array $conteos): void
{
    fwrite(
        STDOUT,
        'resumen candidatos=' . $conteos['candidatos']
        . ' migrados=' . $conteos['migrados']
        . ' faltantes=' . $conteos['faltantes']
        . ' omitidos_fk=' . $conteos['omitidos_fk']
        . ' errores=' . $conteos['errores']
        . PHP_EOL
    );
}

function mig2NombreSeguro(string $nombre): bool
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

function mig2RutaConfinada(string $base, string $ruta): bool
{
    $prefijo = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (DIRECTORY_SEPARATOR === '\\') {
        return strncasecmp($ruta, $prefijo, strlen($prefijo)) === 0;
    }
    return strncmp($ruta, $prefijo, strlen($prefijo)) === 0;
}

/** @return array{estado:string,ruta:?string,tamanio:?int,checksum:?string} */
function mig2Clasificar(string $base, string $referencia): array
{
    if (!mig2NombreSeguro($referencia)) {
        throw new Mig2DocumentoException('Referencia arch_prog insegura.', MIG2_EXIT_DATA);
    }

    $candidato = $base . DIRECTORY_SEPARATOR . $referencia;
    if (!file_exists($candidato)) {
        return ['estado' => 'FALTANTE', 'ruta' => null, 'tamanio' => null, 'checksum' => null];
    }

    $ruta = realpath($candidato);
    if ($ruta === false || !mig2RutaConfinada($base, $ruta) || !is_file($ruta)) {
        throw new Mig2DocumentoException('La referencia no resuelve a un archivo confinado.', MIG2_EXIT_DATA);
    }
    if (!is_readable($ruta)) {
        throw new Mig2DocumentoException('El archivo referenciado no es legible.', MIG2_EXIT_FILESYSTEM);
    }

    $tamanio = @filesize($ruta);
    if (!is_int($tamanio) || $tamanio < 1 || $tamanio > MIG2_MAX_BYTES) {
        throw new Mig2DocumentoException('El tamaño del PDF histórico es inválido.', MIG2_EXIT_DATA);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (@$finfo->file($ruta) !== 'application/pdf') {
        throw new Mig2DocumentoException('El contenido histórico no es application/pdf.', MIG2_EXIT_DATA);
    }

    $checksum = @hash_file('sha256', $ruta);
    if (!is_string($checksum) || preg_match('/^[a-f0-9]{64}$/D', $checksum) !== 1) {
        throw new Mig2DocumentoException(
            'No fue posible calcular el checksum histórico.',
            MIG2_EXIT_FILESYSTEM
        );
    }

    return [
        'estado' => 'VALIDO',
        'ruta' => $ruta,
        'tamanio' => $tamanio,
        'checksum' => $checksum,
    ];
}

function mig2Main(array $argv): int
{
    $argumentos = array_slice($argv, 1);
    if (count($argumentos) !== 1 || !in_array($argumentos[0], ['--dry-run', '--execute'], true)) {
        fwrite(STDERR, 'Uso: php ' . basename(__FILE__) . ' --dry-run|--execute' . PHP_EOL);
        return MIG2_EXIT_USAGE;
    }
    $ejecutar = $argumentos[0] === '--execute';
    $conteos = [
        'candidatos' => 0,
        'migrados' => 0,
        'faltantes' => 0,
        'omitidos_fk' => 0,
        'errores' => 0,
    ];

    $base = realpath(dirname(__DIR__) . '/files/prog_curso');
    if ($base === false || !is_dir($base) || !is_readable($base)) {
        $conteos['errores']++;
        fwrite(STDERR, 'El directorio histórico files/prog_curso no está disponible.' . PHP_EOL);
        mig2Resumen($conteos);
        return MIG2_EXIT_FILESYSTEM;
    }
    $base = rtrim($base, DIRECTORY_SEPARATOR);

    try {
        $consulta = conexion()->prepare(
            'SELECT id_curso, arch_prog FROM curso '
            . 'WHERE id_documento_programa IS NULL AND arch_prog IS NOT NULL '
            . 'ORDER BY id_curso'
        );
        $consulta->execute();
        $candidatos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        $conteos['candidatos'] = count($candidatos);
    } catch (Throwable $error) {
        $conteos['errores']++;
        fwrite(STDERR, 'No fue posible ejecutar el preflight de MIG-2: ' . $error->getMessage() . PHP_EOL);
        mig2Resumen($conteos);
        return MIG2_EXIT_TECHNICAL;
    }

    $validos = [];
    $codigoPreflight = 0;
    foreach ($candidatos as $fila) {
        $idCurso = isset($fila['id_curso']) ? (int) $fila['id_curso'] : 0;
        $referencia = isset($fila['arch_prog']) && is_string($fila['arch_prog'])
            ? $fila['arch_prog']
            : '';
        try {
            $clasificacion = mig2Clasificar($base, $referencia);
            if ($clasificacion['estado'] === 'FALTANTE') {
                $conteos['faltantes']++;
                mig2Salida($idCurso, 'FALTANTE', null, null, 'SIN_IMPORTAR');
                continue;
            }
            $validos[] = [
                'id_curso' => $idCurso,
                'arch_prog' => $referencia,
                'ruta' => $clasificacion['ruta'],
                'tamanio' => $clasificacion['tamanio'],
                'checksum' => $clasificacion['checksum'],
            ];
            mig2Salida(
                $idCurso,
                'VALIDO',
                $clasificacion['tamanio'],
                $clasificacion['checksum'],
                'PREFLIGHT_PASS'
            );
        } catch (Mig2DocumentoException $error) {
            $conteos['errores']++;
            mig2Salida($idCurso, 'INVALIDO', null, null, $error->getMessage());
            $codigoPreflight = $error->exitCode === MIG2_EXIT_FILESYSTEM
                ? MIG2_EXIT_FILESYSTEM
                : max($codigoPreflight, MIG2_EXIT_DATA);
        }
    }

    if ($codigoPreflight !== 0) {
        fwrite(STDERR, 'MIG-2 detenido antes de cualquier escritura.' . PHP_EOL);
        mig2Resumen($conteos);
        return $codigoPreflight;
    }
    if (!$ejecutar) {
        fwrite(STDOUT, 'dry-run=PASS candidatos_validos=' . count($validos) . PHP_EOL);
        mig2Resumen($conteos);
        return 0;
    }

    $pdo = conexion();
    $curso = new Curso();
    $documento = new Documento();

    foreach ($validos as $candidato) {
        $idCurso = $candidato['id_curso'];
        try {
            $revalidacion = mig2Clasificar($base, $candidato['arch_prog']);
            if ($revalidacion['estado'] !== 'VALIDO') {
                throw new Mig2DocumentoException(
                    'El PDF desapareció después del preflight.',
                    MIG2_EXIT_DATA
                );
            }
            if (
                $revalidacion['ruta'] !== $candidato['ruta']
                || $revalidacion['tamanio'] !== $candidato['tamanio']
                || !is_string($revalidacion['checksum'])
                || !hash_equals((string) $candidato['checksum'], $revalidacion['checksum'])
            ) {
                throw new Mig2DocumentoException(
                    'El PDF cambió después del preflight.',
                    MIG2_EXIT_DATA
                );
            }

            $bytes = @file_get_contents($revalidacion['ruta'], false, null, 0, MIG2_MAX_BYTES + 1);
            if (!is_string($bytes)) {
                throw new Mig2DocumentoException('No fue posible leer el PDF.', MIG2_EXIT_FILESYSTEM);
            }
            $tamanio = strlen($bytes);
            if ($tamanio !== $candidato['tamanio'] || $tamanio < 1 || $tamanio > MIG2_MAX_BYTES) {
                throw new Mig2DocumentoException('El PDF cambió después del preflight.', MIG2_EXIT_DATA);
            }
            if ((new finfo(FILEINFO_MIME_TYPE))->buffer($bytes) !== 'application/pdf') {
                throw new Mig2DocumentoException('El MIME cambió después del preflight.', MIG2_EXIT_DATA);
            }
            $checksum = hash('sha256', $bytes);
            if (!hash_equals((string) $candidato['checksum'], $checksum)) {
                throw new Mig2DocumentoException(
                    'Los bytes cambiaron durante la lectura del PDF.',
                    MIG2_EXIT_DATA
                );
            }

            $pdo->beginTransaction();
            $bloqueo = $pdo->prepare(
                'SELECT id_curso, arch_prog, id_documento_programa '
                . 'FROM curso WHERE id_curso = :id_curso FOR UPDATE'
            );
            $bloqueo->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
            $bloqueo->execute();
            $actual = $bloqueo->fetch(PDO::FETCH_ASSOC);
            if ($actual === false) {
                throw new Mig2DocumentoException('El Curso dejó de existir.', MIG2_EXIT_TECHNICAL);
            }
            if ($actual['id_documento_programa'] !== null) {
                $pdo->rollBack();
                $conteos['omitidos_fk']++;
                mig2Salida($idCurso, 'OMITIDO', $tamanio, null, 'FK_EXISTENTE');
                continue;
            }
            if (!is_string($actual['arch_prog']) || $actual['arch_prog'] !== $candidato['arch_prog']) {
                throw new Mig2DocumentoException('arch_prog cambió después del preflight.', MIG2_EXIT_DATA);
            }

            $insercion = $documento->insertar(null, 'application/pdf', $tamanio, $bytes, $checksum);
            $idDocumento = (int) $insercion['idInsertado'];
            $asociacion = $curso->asociarDocumentoPrograma($idCurso, $idDocumento);
            if ((int) $asociacion['filasAfectadas'] !== 1) {
                throw new Mig2DocumentoException('No se asoció exactamente un Curso.', MIG2_EXIT_TECHNICAL);
            }

            $postDocumento = $pdo->prepare(
                'SELECT nombre_original, mime_type, tamanio, '
                . 'OCTET_LENGTH(archivo) AS bytes, checksum_sha256 '
                . 'FROM documento WHERE id_documento = :id_documento'
            );
            $postDocumento->bindValue(':id_documento', $idDocumento, PDO::PARAM_INT);
            $postDocumento->execute();
            $filaDocumento = $postDocumento->fetch(PDO::FETCH_ASSOC);

            $postCurso = $pdo->prepare(
                'SELECT id_documento_programa FROM curso WHERE id_curso = :id_curso'
            );
            $postCurso->bindValue(':id_curso', $idCurso, PDO::PARAM_INT);
            $postCurso->execute();
            $fk = $postCurso->fetchColumn();

            if (
                $filaDocumento === false
                || $filaDocumento['nombre_original'] !== null
                || ($filaDocumento['mime_type'] ?? null) !== 'application/pdf'
                || (int) $filaDocumento['tamanio'] !== $tamanio
                || (int) $filaDocumento['bytes'] !== $tamanio
                || !hash_equals($checksum, (string) $filaDocumento['checksum_sha256'])
                || (int) $fk !== $idDocumento
            ) {
                throw new Mig2DocumentoException('El postcheck Documento/FK no fue aprobado.', MIG2_EXIT_TECHNICAL);
            }
            if ($pdo->commit() !== true) {
                throw new Mig2DocumentoException('No fue posible confirmar la transacción.', MIG2_EXIT_TECHNICAL);
            }

            $conteos['migrados']++;
            mig2Salida($idCurso, 'MIGRADO', $tamanio, $checksum, 'PASS');
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $conteos['errores']++;
            mig2Salida($idCurso, 'ERROR', null, null, $error->getMessage());
            mig2Resumen($conteos);
            return $error instanceof Mig2DocumentoException
                ? $error->exitCode
                : MIG2_EXIT_TECHNICAL;
        }
    }

    mig2Resumen($conteos);
    return 0;
}

exit(mig2Main($argv));
