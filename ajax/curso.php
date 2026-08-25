<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Curso;
use App\Model\Documento;
use App\Security\Authorization;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

const CURSO_PDF_MAX_BYTES = 5242880;
const CURSO_ESTADO_PROFESOR_ACEPTADO = 2;

final class CursoHttpError extends RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $errorCode,
        string $publicMessage
    ) {
        parent::__construct($publicMessage);
    }
}

/** @param array<string, mixed> $payload */
function responderCurso(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function fallarCurso(int $statusCode, string $errorCode, string $message): never
{
    throw new CursoHttpError($statusCode, $errorCode, $message);
}

function exigirLecturaCurso(): void
{
    if (!Authorization::hasCapability('cursos.ver')) {
        fallarCurso(403, 'NO_AUTORIZADO', 'No tiene autorización para consultar Cursos.');
    }
}

function actorCurso(): string
{
    if (Authorization::hasAny(['admin'])) {
        return 'admin';
    }
    if (Authorization::hasAny(['comite'])) {
        return 'comite';
    }
    if (
        Authorization::hasAny(['docente'])
        && Authorization::hasCapability('docente.habilitado')
    ) {
        return 'profesor';
    }
    return 'sin_mutacion';
}

function exigirActorEscritura(string $operacion): string
{
    $actor = actorCurso();
    if ($operacion === 'delete') {
        if ($actor !== 'admin') {
            fallarCurso(403, 'NO_AUTORIZADO', 'Sólo un administrador puede eliminar Cursos.');
        }
        return $actor;
    }
    if (!in_array($actor, ['admin', 'comite', 'profesor'], true)) {
        fallarCurso(403, 'NO_AUTORIZADO', 'No tiene autorización para modificar Cursos.');
    }
    return $actor;
}

function exigirCsrfCurso(): void
{
    $tokenSesion = $_SESSION['csrf_curso'] ?? null;
    $tokenCliente = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (
        !is_string($tokenSesion)
        || !is_string($tokenCliente)
        || $tokenSesion === ''
        || !hash_equals($tokenSesion, $tokenCliente)
    ) {
        fallarCurso(403, 'CSRF_INVALIDO', 'La solicitud no contiene un token de seguridad válido.');
    }
}

function enteroPositivoCurso(mixed $valor, string $campo): int
{
    if (!is_scalar($valor) || is_bool($valor) || !ctype_digit((string) $valor)) {
        fallarCurso(400, 'FORMATO_INVALIDO', "El campo {$campo} debe ser un entero positivo.");
    }
    $entero = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($entero === false) {
        fallarCurso(400, 'FORMATO_INVALIDO', "El campo {$campo} debe ser un entero positivo.");
    }
    return (int) $entero;
}

function enteroCurso(mixed $valor, string $campo): int
{
    if (!is_scalar($valor) || is_bool($valor) || !preg_match('/^-?\d+$/D', (string) $valor)) {
        fallarCurso(400, 'FORMATO_INVALIDO', "El campo {$campo} debe ser un entero.");
    }
    $entero = filter_var($valor, FILTER_VALIDATE_INT);
    if ($entero === false || $entero < -2147483648 || $entero > 2147483647) {
        fallarCurso(400, 'FORMATO_INVALIDO', "El campo {$campo} no es compatible con INT.");
    }
    return (int) $entero;
}

function idCursoDesde(array $fuente): int
{
    return enteroPositivoCurso($fuente['id_curso'] ?? null, 'id_curso');
}

function usuarioProfesorSesion(): int
{
    $filas = $_SESSION['id_usuario'] ?? null;
    if (
        !is_array($filas)
        || count($filas) !== 1
        || !isset($filas[0]['id_usuario'])
        || !is_scalar($filas[0]['id_usuario'])
    ) {
        fallarCurso(403, 'IDENTIDAD_PROFESOR_INVALIDA', 'No existe una identidad Profesor válida en la sesión.');
    }
    $valor = $filas[0]['id_usuario'];
    if (!ctype_digit((string) $valor) || (int) $valor < 1) {
        fallarCurso(403, 'IDENTIDAD_PROFESOR_INVALIDA', 'No existe una identidad Profesor válida en la sesión.');
    }
    return (int) $valor;
}

/** @return array{nom_curso:int,creditos:string,caracter:int,periodo:int,anio_curso:int,carga_hor:int,profesor:int} */
function validarPayloadCurso(string $actor): array
{
    $nombreCurso = enteroPositivoCurso($_POST['nom_curso'] ?? null, 'nom_curso');
    $creditos = isset($_POST['creditos']) && is_string($_POST['creditos'])
        ? trim($_POST['creditos'])
        : '';
    if ($creditos === '' || strlen($creditos) > 45 || !preg_match('/^\d+(?:\.\d+)?$/D', $creditos)) {
        fallarCurso(422, 'CREDITOS_INVALIDOS', 'Los créditos deben tener un formato decimal válido.');
    }

    $caracter = enteroCurso($_POST['caracter'] ?? null, 'caracter');
    if (!in_array($caracter, [1, 2], true)) {
        fallarCurso(422, 'CARACTER_INVALIDO', 'El carácter del Curso no es válido.');
    }
    $periodo = enteroCurso($_POST['periodo'] ?? null, 'periodo');
    if (!in_array($periodo, [1, 2, 3], true)) {
        fallarCurso(422, 'PERIODO_INVALIDO', 'El periodo del Curso no es válido.');
    }
    $anioCurso = enteroCurso($_POST['anio_curso'] ?? null, 'anio_curso');
    $anioActual = (int) date('Y');
    if ($anioCurso < 2006 || $anioCurso > $anioActual) {
        fallarCurso(422, 'ANIO_INVALIDO', "El año debe estar entre 2006 y {$anioActual}.");
    }
    $cargaHoraria = enteroCurso($_POST['carga_hor'] ?? null, 'carga_hor');

    if ($actor === 'profesor') {
        if (array_key_exists('profesor', $_POST)) {
            fallarCurso(409, 'PROFESOR_NO_PERMITIDO', 'Un Profesor no puede asignar o transferir el Curso.');
        }
        $profesor = usuarioProfesorSesion();
    } else {
        $profesor = enteroPositivoCurso($_POST['profesor'] ?? null, 'profesor');
    }

    return [
        'nom_curso' => $nombreCurso,
        'creditos' => $creditos,
        'caracter' => $caracter,
        'periodo' => $periodo,
        'anio_curso' => $anioCurso,
        'carga_hor' => $cargaHoraria,
        'profesor' => $profesor,
    ];
}

function validarProfesorDestino(Curso $curso, int $idUsuario, bool $bloquear, bool $esSesion): int
{
    $identidades = $curso->buscarIdentidadProfesor($idUsuario, $bloquear);
    if ($esSesion) {
        if (
            count($identidades) !== 1
            || !isset($identidades[0]['id_profesor'], $identidades[0]['estado_profesor'])
            || (int) $identidades[0]['estado_profesor'] !== CURSO_ESTADO_PROFESOR_ACEPTADO
        ) {
            fallarCurso(403, 'DOCENTE_NO_HABILITADO', 'El estado actual del Profesor no permite modificar Cursos.');
        }
        return $idUsuario;
    }
    if ($identidades === []) {
        fallarCurso(404, 'USUARIO_PROFESOR_NO_ENCONTRADO', 'El usuario Profesor indicado no existe.');
    }
    if (
        count($identidades) !== 1
        || !isset($identidades[0]['id_profesor'], $identidades[0]['estado_profesor'])
    ) {
        fallarCurso(409, 'IDENTIDAD_PROFESOR_INCOMPATIBLE', 'El usuario no representa un Profesor inequívoco.');
    }
    if ((int) $identidades[0]['estado_profesor'] !== CURSO_ESTADO_PROFESOR_ACEPTADO) {
        fallarCurso(409, 'PROFESOR_NO_ACEPTADO', 'El Profesor indicado no se encuentra Aceptado.');
    }
    return $idUsuario;
}

function directorioProgramasCurso(): string
{
    $directorio = realpath(__DIR__ . '/../files/prog_curso');
    if ($directorio === false || !is_dir($directorio)) {
        fallarCurso(500, 'ALMACENAMIENTO_NO_DISPONIBLE', 'El almacenamiento de programas no está disponible.');
    }
    return rtrim($directorio, DIRECTORY_SEPARATOR);
}

function nombrePersistidoSeguro(string $nombre): bool
{
    return $nombre !== ''
        && strlen($nombre) <= 45
        && !in_array($nombre, ['.', '..'], true)
        && !str_contains($nombre, "\0")
        && !str_contains($nombre, '/')
        && !str_contains($nombre, '\\')
        && !preg_match('/[<>:"|?*\x00-\x1F\x7F]/', $nombre)
        && basename($nombre) === $nombre;
}

function rutaProgramaPersistido(string $nombre): ?string
{
    if (!nombrePersistidoSeguro($nombre)) {
        fallarCurso(409, 'RUTA_PROGRAMA_INVALIDA', 'La referencia persistida del programa no es segura.');
    }
    $directorio = directorioProgramasCurso();
    $candidato = $directorio . DIRECTORY_SEPARATOR . $nombre;
    if (!file_exists($candidato)) {
        return null;
    }
    $rutaReal = realpath($candidato);
    $prefijo = $directorio . DIRECTORY_SEPARATOR;
    if (
        $rutaReal === false
        || !is_file($rutaReal)
        || !is_readable($rutaReal)
        || !str_starts_with($rutaReal, $prefijo)
    ) {
        fallarCurso(409, 'RUTA_PROGRAMA_INVALIDA', 'La referencia persistida del programa no es segura.');
    }
    return $rutaReal;
}

/** @return array{ruta:string,tamanio:int}|null */
function fallbackProgramaCurso(mixed $nombre): ?array
{
    if ($nombre === null || $nombre === '') {
        return null;
    }
    if (!is_string($nombre)) {
        fallarCurso(409, 'RUTA_PROGRAMA_INVALIDA', 'La referencia persistida del programa no es segura.');
    }
    $ruta = rutaProgramaPersistido($nombre);
    if ($ruta === null) {
        return null;
    }
    $tamanio = @filesize($ruta);
    if (!is_int($tamanio) || $tamanio < 1 || $tamanio > CURSO_PDF_MAX_BYTES) {
        fallarCurso(409, 'PROGRAMA_INVALIDO', 'El programa histórico no tiene un tamaño válido.');
    }
    if (@(new finfo(FILEINFO_MIME_TYPE))->file($ruta) !== 'application/pdf') {
        fallarCurso(409, 'PROGRAMA_INVALIDO', 'El programa histórico no corresponde a un PDF válido.');
    }
    return ['ruta' => $ruta, 'tamanio' => $tamanio];
}

function sanitizarNombreOriginalCurso(mixed $nombre): ?string
{
    if (!is_string($nombre) || $nombre === '' || preg_match('//u', $nombre) !== 1) {
        return null;
    }
    $logico = basename(str_replace('\\', '/', str_replace("\0", '', $nombre)));
    $seguro = preg_replace('/[\x00-\x1F\x7F\/\\\\"]+/u', '', $logico);
    if (!is_string($seguro)) {
        return null;
    }
    $seguro = trim($seguro);
    if ($seguro === '') {
        return null;
    }
    if (strlen($seguro) > 255) {
        if (function_exists('mb_strcut')) {
            $seguro = mb_strcut($seguro, 0, 255, 'UTF-8');
        } else {
            $seguro = substr($seguro, 0, 255);
            while ($seguro !== '' && preg_match('//u', $seguro) !== 1) {
                $seguro = substr($seguro, 0, -1);
            }
        }
    }
    return $seguro === '' ? null : $seguro;
}

/** @return array{nombre_original:?string,mime_type:string,tamanio:int,archivo:string,checksum_sha256:string}|null */
function validarUploadCurso(bool $obligatorio): ?array
{
    if (!isset($_FILES['arch_prog']) || !is_array($_FILES['arch_prog'])) {
        if ($obligatorio) {
            fallarCurso(422, 'PDF_REQUERIDO', 'Debe adjuntar el programa PDF del Curso.');
        }
        return null;
    }
    $archivo = $_FILES['arch_prog'];
    $error = isset($archivo['error']) ? (int) $archivo['error'] : UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE && !$obligatorio) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        fallarCurso(422, 'PDF_UPLOAD_INVALIDO', 'No fue posible recibir el programa PDF.');
    }
    $tmpName = isset($archivo['tmp_name']) && is_string($archivo['tmp_name']) ? $archivo['tmp_name'] : '';
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        fallarCurso(422, 'PDF_UPLOAD_INVALIDO', 'El archivo recibido no corresponde a un upload válido.');
    }
    clearstatcache(true, $tmpName);
    $tamanio = @filesize($tmpName);
    if (!is_int($tamanio) || $tamanio < 1 || $tamanio > CURSO_PDF_MAX_BYTES) {
        fallarCurso(422, 'PDF_TAMANIO_INVALIDO', 'El programa debe ser un PDF no vacío de hasta 5 MiB.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (@$finfo->file($tmpName) !== 'application/pdf') {
        fallarCurso(422, 'PDF_MIME_INVALIDO', 'El contenido adjunto no corresponde a un PDF válido.');
    }
    $bytes = @file_get_contents($tmpName, false, null, 0, CURSO_PDF_MAX_BYTES + 1);
    if (!is_string($bytes) || strlen($bytes) !== $tamanio) {
        fallarCurso(422, 'PDF_LECTURA_INVALIDA', 'No fue posible leer íntegramente el programa PDF.');
    }
    return [
        'nombre_original' => sanitizarNombreOriginalCurso($archivo['name'] ?? null),
        'mime_type' => 'application/pdf',
        'tamanio' => $tamanio,
        'archivo' => $bytes,
        'checksum_sha256' => hash('sha256', $bytes),
    ];
}

/** @param array<string, mixed> $fila */
function puedeEditarCurso(array $fila): bool
{
    $actor = actorCurso();
    if (in_array($actor, ['admin', 'comite'], true)) {
        return true;
    }
    return $actor === 'profesor' && (int) $fila['profesor'] === usuarioProfesorSesion();
}

/** @return array<string, mixed> */
function metadataDocumentoCurso(Documento $documento, int $idDocumento, bool $bloquear = false): array
{
    $metadata = $documento->obtenerMetadata($idDocumento, $bloquear);
    if (
        $metadata === null
        || (int) ($metadata['id_documento'] ?? 0) !== $idDocumento
        || ($metadata['mime_type'] ?? null) !== 'application/pdf'
        || (int) ($metadata['tamanio'] ?? 0) < 1
        || (int) ($metadata['tamanio'] ?? 0) > CURSO_PDF_MAX_BYTES
        || !is_string($metadata['checksum_sha256'] ?? null)
        || preg_match('/^[a-f0-9]{64}$/D', $metadata['checksum_sha256']) !== 1
    ) {
        fallarCurso(500, 'DOCUMENTO_INCONSISTENTE', 'El Documento asociado no supera las validaciones de integridad.');
    }
    return $metadata;
}

/** @param array<string, mixed> $fila */
function serializarCurso(array $fila, bool $detalle, Documento $documento): array
{
    $datos = [
        'id_curso' => (int) $fila['id_curso'],
        'id_nom_curso' => (int) $fila['id_nom_curso'],
        'nom_curso' => (string) $fila['nom_curso'],
        'creditos' => (string) $fila['creditos'],
        'caracter' => (int) $fila['caracter'],
        'periodo' => (int) $fila['periodo'],
        'anio_curso' => (int) $fila['anio_curso'],
        'carga_hor' => (int) $fila['carga_hor'],
        'profesor' => (int) $fila['profesor'],
        'puede_editar' => puedeEditarCurso($fila),
        'puede_eliminar' => actorCurso() === 'admin',
    ];
    if (isset($fila['nombres'], $fila['ap_pat'], $fila['ap_mat'])) {
        $datos['nombre_profesor'] = trim(
            (string) $fila['nombres'] . ' ' . (string) $fila['ap_pat'] . ' ' . (string) $fila['ap_mat']
        );
    }
    if ($detalle) {
        if ($fila['id_documento_programa'] !== null) {
            metadataDocumentoCurso($documento, (int) $fila['id_documento_programa']);
            $datos['programa_disponible'] = true;
        } else {
            $datos['programa_disponible'] = fallbackProgramaCurso($fila['arch_prog'] ?? null) !== null;
        }
    }
    return $datos;
}

/** @param array<string, mixed> $actual @param array<string, mixed> $datos */
function cursoCoincide(array $actual, array $datos): bool
{
    return (string) $actual['creditos'] === $datos['creditos']
        && (int) $actual['caracter'] === $datos['caracter']
        && (int) $actual['periodo'] === $datos['periodo']
        && (int) $actual['anio_curso'] === $datos['anio_curso']
        && (int) $actual['carga_hor'] === $datos['carga_hor']
        && (int) $actual['id_nom_curso'] === $datos['nom_curso']
        && (int) $actual['profesor'] === $datos['profesor'];
}

function crearCurso(Curso $curso, Documento $documento): never
{
    $actor = exigirActorEscritura('create');
    exigirCsrfCurso();
    $datos = validarPayloadCurso($actor);
    $upload = validarUploadCurso(true);
    if ($upload === null) {
        fallarCurso(422, 'PDF_REQUERIDO', 'Debe adjuntar el programa PDF del Curso.');
    }
    $pdo = conexion();
    try {
        $pdo->beginTransaction();
        if (!$curso->nombreCursoExiste($datos['nom_curso'])) {
            fallarCurso(404, 'NOMBRE_CURSO_NO_ENCONTRADO', 'El nombre de Curso seleccionado no existe.');
        }
        validarProfesorDestino($curso, $datos['profesor'], true, $actor === 'profesor');
        $resultadoDocumento = $documento->insertar(
            $upload['nombre_original'],
            $upload['mime_type'],
            $upload['tamanio'],
            $upload['archivo'],
            $upload['checksum_sha256']
        );
        $idDocumento = (int) $resultadoDocumento['idInsertado'];
        $resultado = $curso->insertar(
            $datos['creditos'],
            $datos['caracter'],
            $datos['periodo'],
            $datos['anio_curso'],
            $datos['carga_hor'],
            $datos['nom_curso'],
            $idDocumento,
            $datos['profesor']
        );
        $idCurso = isset($resultado['idInsertado']) ? (int) $resultado['idInsertado'] : 0;
        if ((int) $resultado['filasAfectadas'] !== 1 || $idCurso < 1) {
            throw new RuntimeException('No fue posible confirmar el registro y su programa.');
        }
        if ($pdo->commit() !== true) {
            throw new RuntimeException('No fue posible confirmar la creación del Curso.');
        }
        responderCurso(201, [
            'ok' => true,
            'datos' => ['id_curso' => $idCurso],
            'mensaje' => 'Curso creado correctamente.',
        ]);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($error instanceof CursoHttpError) {
            throw $error;
        }
        error_log('[CURSO_CREATE] ' . $error->getMessage());
        fallarCurso(500, 'ERROR_TECNICO', 'No fue posible crear el Curso; no se confirmó la operación.');
    }
}

function actualizarCurso(Curso $curso, Documento $documento): never
{
    $actor = exigirActorEscritura('update');
    exigirCsrfCurso();
    $idCurso = idCursoDesde($_POST);
    $datos = validarPayloadCurso($actor);
    $upload = validarUploadCurso(false);
    $preliminar = $curso->mostrarPorId($idCurso);
    if ($preliminar === null) {
        fallarCurso(404, 'CURSO_NO_ENCONTRADO', 'El Curso indicado no existe.');
    }
    if ($actor === 'profesor' && (int) $preliminar['profesor'] !== $datos['profesor']) {
        fallarCurso(409, 'CURSO_AJENO', 'No puede actualizar un Curso perteneciente a otro Profesor.');
    }
    if (
        $upload === null
        && $preliminar['id_documento_programa'] === null
        && fallbackProgramaCurso($preliminar['arch_prog'] ?? null) === null
    ) {
        fallarCurso(409, 'PROGRAMA_AUSENTE', 'El programa vigente no existe; debe adjuntar un reemplazo.');
    }

    $pdo = conexion();
    try {
        $pdo->beginTransaction();
        $actual = $curso->mostrarPorId($idCurso, true);
        if ($actual === null) {
            fallarCurso(404, 'CURSO_NO_ENCONTRADO', 'El Curso indicado no existe.');
        }
        if ($actor === 'profesor' && (int) $actual['profesor'] !== $datos['profesor']) {
            fallarCurso(409, 'CURSO_AJENO', 'No puede actualizar un Curso perteneciente a otro Profesor.');
        }
        if (!$curso->nombreCursoExiste($datos['nom_curso'])) {
            fallarCurso(404, 'NOMBRE_CURSO_NO_ENCONTRADO', 'El nombre de Curso seleccionado no existe.');
        }
        validarProfesorDestino($curso, $datos['profesor'], true, $actor === 'profesor');

        $idDocumento = $actual['id_documento_programa'] === null
            ? null
            : (int) $actual['id_documento_programa'];
        if ($upload === null && $idDocumento === null) {
            if (fallbackProgramaCurso($actual['arch_prog'] ?? null) === null) {
                fallarCurso(409, 'PROGRAMA_AUSENTE', 'El programa vigente no existe; debe adjuntar un reemplazo.');
            }
        }

        $documentoCambiado = false;
        $fkCambiada = false;
        if ($upload !== null) {
            if ($idDocumento !== null) {
                metadataDocumentoCurso($documento, $idDocumento, true);
                $resultadoDocumento = $documento->actualizar(
                    $idDocumento,
                    $upload['nombre_original'],
                    $upload['mime_type'],
                    $upload['tamanio'],
                    $upload['archivo'],
                    $upload['checksum_sha256']
                );
                if (!in_array((int) $resultadoDocumento['filasAfectadas'], [0, 1], true)) {
                    throw new RuntimeException('La actualización de Documento produjo un resultado inválido.');
                }
            } else {
                $resultadoDocumento = $documento->insertar(
                    $upload['nombre_original'],
                    $upload['mime_type'],
                    $upload['tamanio'],
                    $upload['archivo'],
                    $upload['checksum_sha256']
                );
                $idDocumento = (int) $resultadoDocumento['idInsertado'];
                $fkCambiada = true;
            }
            $postDocumento = metadataDocumentoCurso($documento, $idDocumento);
            if (
                $postDocumento['nombre_original'] !== $upload['nombre_original']
                || (int) $postDocumento['tamanio'] !== $upload['tamanio']
                || !hash_equals($upload['checksum_sha256'], (string) $postDocumento['checksum_sha256'])
            ) {
                throw new RuntimeException('El postcheck de Documento no coincide con el upload validado.');
            }
            $documentoCambiado = true;
        }

        $cursoCambiado = !cursoCoincide($actual, $datos) || $fkCambiada;
        if ($cursoCambiado) {
            $resultado = $curso->editar(
                $idCurso,
                $datos['creditos'],
                $datos['caracter'],
                $datos['periodo'],
                $datos['anio_curso'],
                $datos['carga_hor'],
                $datos['nom_curso'],
                $idDocumento,
                $datos['profesor']
            );
            if ((int) $resultado['filasAfectadas'] !== 1) {
                throw new RuntimeException('La actualización no afectó exactamente un Curso.');
            }
        }

        if ($pdo->commit() !== true) {
            throw new RuntimeException('No fue posible confirmar la actualización del Curso.');
        }
        $huboCambios = $cursoCambiado || $documentoCambiado;
        responderCurso(200, [
            'ok' => true,
            'datos' => ['id_curso' => $idCurso, 'cambios' => $huboCambios],
            'mensaje' => $huboCambios
                ? 'Curso actualizado correctamente.'
                : 'El Curso no presenta cambios.',
        ]);
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($error instanceof CursoHttpError) {
            throw $error;
        }
        error_log('[CURSO_UPDATE] ' . $error->getMessage());
        fallarCurso(500, 'ERROR_TECNICO', 'No fue posible actualizar el Curso; no se confirmó la operación.');
    }
}

function eliminarCurso(Curso $curso, Documento $documento): never
{
    exigirActorEscritura('delete');
    exigirCsrfCurso();
    $idCurso = idCursoDesde($_POST);
    $pdo = conexion();
    try {
        $pdo->beginTransaction();
        $actual = $curso->mostrarPorId($idCurso, true);
        if ($actual === null) {
            fallarCurso(404, 'CURSO_NO_ENCONTRADO', 'El Curso indicado no existe.');
        }
        $idDocumento = $actual['id_documento_programa'] === null
            ? null
            : (int) $actual['id_documento_programa'];
        $resultado = $curso->eliminar($idCurso);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            throw new RuntimeException('La eliminación no afectó exactamente un Curso.');
        }
        if ($idDocumento !== null) {
            $resultadoDocumento = $documento->eliminar($idDocumento);
            if ((int) $resultadoDocumento['filasAfectadas'] !== 1) {
                throw new RuntimeException('La eliminación no afectó exactamente un Documento.');
            }
        }
        if ($pdo->commit() !== true) {
            throw new RuntimeException('No fue posible confirmar la eliminación del Curso.');
        }
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($error instanceof CursoHttpError) {
            throw $error;
        }
        error_log('[CURSO_DELETE] ' . $error->getMessage());
        fallarCurso(500, 'ERROR_TECNICO', 'No fue posible eliminar el Curso; no se confirmó la operación.');
    }

    responderCurso(200, [
        'ok' => true,
        'datos' => ['id_curso' => $idCurso],
        'mensaje' => 'Curso eliminado correctamente.',
    ]);
}

function listarCursos(Curso $curso, Documento $documento): never
{
    exigirLecturaCurso();
    $datos = array_map(
        static fn(array $fila): array => serializarCurso($fila, false, $documento),
        $curso->mostrar()
    );
    responderCurso(200, ['ok' => true, 'datos' => $datos, 'mensaje' => 'Cursos obtenidos correctamente.']);
}

function catalogoCursos(Curso $curso): never
{
    exigirLecturaCurso();
    $datos = array_map(
        static fn(array $fila): array => [
            'id_nom_curso' => (int) $fila['id_nom_curso'],
            'nom_curso' => (string) $fila['nom_curso'],
        ],
        $curso->mostrarCursos()
    );
    responderCurso(200, ['ok' => true, 'datos' => $datos, 'mensaje' => 'Catálogo obtenido correctamente.']);
}

function detalleCurso(Curso $curso, Documento $documento): never
{
    exigirLecturaCurso();
    $idCurso = idCursoDesde($_POST);
    $fila = $curso->mostrarPorId($idCurso);
    if ($fila === null) {
        fallarCurso(404, 'CURSO_NO_ENCONTRADO', 'El Curso indicado no existe.');
    }
    responderCurso(200, [
        'ok' => true,
        'datos' => serializarCurso($fila, true, $documento),
        'mensaje' => 'Curso obtenido correctamente.',
    ]);
}

/** @return array{ascii:string,utf8:string} */
function nombresDescargaCurso(mixed $nombreOriginal, int $idCurso): array
{
    $ascii = 'programa_curso_' . $idCurso . '.pdf';
    $utf8 = sanitizarNombreOriginalCurso($nombreOriginal);
    if ($utf8 === null) {
        $utf8 = $ascii;
    }
    return ['ascii' => $ascii, 'utf8' => $utf8];
}

function descargarPrograma(Curso $curso, Documento $documento): never
{
    exigirLecturaCurso();
    $idCurso = idCursoDesde($_GET);
    $fila = $curso->mostrarPorId($idCurso);
    if ($fila === null) {
        fallarCurso(404, 'CURSO_NO_ENCONTRADO', 'El Curso indicado no existe.');
    }
    $idDocumento = $fila['id_documento_programa'] === null
        ? null
        : (int) $fila['id_documento_programa'];
    $bytes = null;
    $recurso = null;
    $tamanio = 0;
    $nombres = nombresDescargaCurso(null, $idCurso);

    if ($idDocumento !== null) {
        $metadata = metadataDocumentoCurso($documento, $idDocumento);
        $contenido = $documento->obtenerContenido($idDocumento);
        if ($contenido === null || !is_string($contenido['archivo'] ?? null)) {
            fallarCurso(500, 'DOCUMENTO_INCONSISTENTE', 'No fue posible recuperar el Documento asociado.');
        }
        $bytes = $contenido['archivo'];
        $tamanio = (int) $metadata['tamanio'];
        if (
            strlen($bytes) !== $tamanio
            || (int) ($contenido['tamanio'] ?? 0) !== $tamanio
            || ($contenido['mime_type'] ?? null) !== 'application/pdf'
            || (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes) !== 'application/pdf'
            || !hash_equals(
                (string) $metadata['checksum_sha256'],
                (string) ($contenido['checksum_sha256'] ?? '')
            )
        ) {
            fallarCurso(500, 'DOCUMENTO_INCONSISTENTE', 'El contenido no coincide con la metadata de Documento.');
        }
        $nombres = nombresDescargaCurso($metadata['nombre_original'] ?? null, $idCurso);
    } else {
        $fallback = fallbackProgramaCurso($fila['arch_prog'] ?? null);
        if ($fallback === null) {
            fallarCurso(404, 'PROGRAMA_NO_ENCONTRADO', 'El programa del Curso no está disponible.');
        }
        $tamanio = $fallback['tamanio'];
        $recurso = @fopen($fallback['ruta'], 'rb');
        if ($recurso === false) {
            fallarCurso(500, 'ERROR_ARCHIVO', 'No fue posible abrir el programa histórico solicitado.');
        }
    }

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(200);
    header('Content-Type: application/pdf');
    header(
        'Content-Disposition: attachment; filename="' . $nombres['ascii']
        . '"; filename*=UTF-8\'\'' . rawurlencode($nombres['utf8'])
    );
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store, max-age=0');
    header('Content-Length: ' . (string) $tamanio);

    if ($bytes !== null) {
        echo $bytes;
        exit;
    }

    try {
        while (!feof($recurso)) {
            $bloque = fread($recurso, 8192);
            if ($bloque === false) {
                error_log('[CURSO_DOWNLOAD] Falló la lectura del programa durante la transmisión.');
                break;
            }
            echo $bloque;
        }
    } finally {
        fclose($recurso);
    }
    exit;
}

$curso = new Curso();
$documento = new Documento();

try {
    $metodo = $_SERVER['REQUEST_METHOD'] ?? '';
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    if ($metodo === 'POST' && $contentLength > 0 && $_POST === [] && $_FILES === []) {
        fallarCurso(422, 'PAYLOAD_EXCEDIDO', 'La solicitud excede el tamaño permitido o no pudo ser procesada.');
    }
    $operacion = $metodo === 'GET'
        ? (isset($_GET['op']) && is_string($_GET['op']) ? $_GET['op'] : '')
        : (isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '');

    if ($operacion === 'download') {
        if ($metodo !== 'GET') {
            fallarCurso(400, 'METODO_INVALIDO', 'La descarga requiere una solicitud GET.');
        }
        descargarPrograma($curso, $documento);
    }
    if ($metodo !== 'POST') {
        fallarCurso(400, 'METODO_INVALIDO', 'La operación requiere una solicitud POST.');
    }

    match ($operacion) {
        'create' => crearCurso($curso, $documento),
        'read' => listarCursos($curso, $documento),
        'read_cursos' => catalogoCursos($curso),
        'query_id' => detalleCurso($curso, $documento),
        'update' => actualizarCurso($curso, $documento),
        'delete' => eliminarCurso($curso, $documento),
        default => fallarCurso(400, 'OPERACION_INVALIDA', 'La operación solicitada no es válida.'),
    };
} catch (CursoHttpError $error) {
    responderCurso($error->statusCode, [
        'ok' => false,
        'error' => $error->errorCode,
        'mensaje' => $error->getMessage(),
    ]);
} catch (Throwable $error) {
    error_log('[CURSO_ENDPOINT] ' . $error->getMessage());
    responderCurso(500, [
        'ok' => false,
        'error' => 'ERROR_TECNICO',
        'mensaje' => 'No fue posible completar la operación de Curso.',
    ]);
}
