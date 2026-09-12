<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Grado;
use App\Security\Authorization;

final class GradoHttpError extends RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $errorCode,
        string $message
    ) {
        parent::__construct($message);
    }
}

/** @param array<string, mixed> $payload */
function responderGrado(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function fallarGrado(int $statusCode, string $errorCode, string $message): never
{
    throw new GradoHttpError($statusCode, $errorCode, $message);
}

/** @return array{global:bool,usuario:int} */
function actorGrado(): array
{
    $login = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
        ? (int) $_SESSION['login']
        : 0;
    if ($login <= 0) {
        fallarGrado(401, 'NO_AUTENTICADO', 'Debe iniciar sesión para operar Grados académicos.');
    }

    $rolGlobal = Authorization::hasAny(['admin', 'comite']) && (
        (isset($_SESSION['admin']) && is_scalar($_SESSION['admin']) && (int) $_SESSION['admin'] === $login)
        || (isset($_SESSION['comite']) && is_scalar($_SESSION['comite']) && (int) $_SESSION['comite'] === $login)
    );
    if ($rolGlobal) {
        return ['global' => true, 'usuario' => 0];
    }

    $usuarios = $_SESSION['id_usuario'] ?? null;
    if (
        !is_array($usuarios)
        || count($usuarios) !== 1
        || !isset($usuarios[0]['id_usuario'])
        || !is_scalar($usuarios[0]['id_usuario'])
        || (int) $usuarios[0]['id_usuario'] <= 0
    ) {
        fallarGrado(403, 'PERFIL_NO_VIGENTE', 'No tiene acceso vigente a un perfil autorizado.');
    }

    $esProfesor = isset($_SESSION['docente'])
        && is_scalar($_SESSION['docente'])
        && (int) $_SESSION['docente'] === $login;
    $esEstudiante = isset($_SESSION['estudiante'])
        && is_scalar($_SESSION['estudiante'])
        && (int) $_SESSION['estudiante'] === $login
        && Authorization::hasCapability('perfil.ver');
    if (!$esProfesor && !$esEstudiante) {
        fallarGrado(403, 'PERFIL_NO_VIGENTE', 'No tiene acceso vigente a un perfil autorizado.');
    }

    return ['global' => false, 'usuario' => (int) $usuarios[0]['id_usuario']];
}

function enteroPositivoGrado(mixed $value, string $field): int
{
    if (!is_int($value) && !is_string($value)) {
        fallarGrado(400, 'CAMPO_INVALIDO', "El campo {$field} no es válido.");
    }
    $raw = (string) $value;
    if (preg_match('/^[1-9][0-9]*$/D', $raw) !== 1) {
        fallarGrado(400, 'CAMPO_INVALIDO', "El campo {$field} debe ser un entero positivo.");
    }
    $result = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($result === false) {
        fallarGrado(400, 'CAMPO_INVALIDO', "El campo {$field} está fuera de rango.");
    }
    return $result;
}

function idGradoInsertUpdate(): int
{
    if (!array_key_exists('id_grado', $_POST) || $_POST['id_grado'] === '' || $_POST['id_grado'] === '0') {
        return 0;
    }
    return enteroPositivoGrado($_POST['id_grado'], 'id_grado');
}

function usuarioSolicitadoGrado(): ?int
{
    if (!array_key_exists('usuario', $_POST)) {
        return null;
    }
    return enteroPositivoGrado($_POST['usuario'], 'usuario');
}

/** @param array{global:bool,usuario:int} $actor */
function usuarioObjetivoGrado(array $actor, Grado $grado): int
{
    $solicitado = usuarioSolicitadoGrado();
    if ($actor['global']) {
        if ($solicitado === null || !$grado->usuarioObjetivoValido($solicitado)) {
            fallarGrado(404, 'USUARIO_OBJETIVO_INVALIDO', 'El usuario objetivo no existe o no es Profesor/Estudiante.');
        }
        return $solicitado;
    }
    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarGrado(403, 'USUARIO_AJENO', 'No puede operar Grados para otro usuario.');
    }
    if (!$grado->usuarioObjetivoValido($actor['usuario'])) {
        fallarGrado(403, 'PERFIL_NO_VIGENTE', 'El perfil autenticado no corresponde a Profesor/Estudiante.');
    }
    return $actor['usuario'];
}

/**
 * @param array{global:bool,usuario:int} $actor
 * @param array<string, mixed> $fila
 */
function autorizarFilaGrado(array $actor, array $fila, Grado $grado): int
{
    $propietario = isset($fila['usuario']) ? (int) $fila['usuario'] : 0;
    if ($propietario <= 0) {
        throw new RuntimeException('El Grado no contiene un propietario válido.');
    }

    $solicitado = usuarioSolicitadoGrado();
    if ($actor['global']) {
        if (!$grado->usuarioObjetivoValido($propietario)) {
            fallarGrado(409, 'PROPIETARIO_INVALIDO', 'El Grado no pertenece a un Profesor/Estudiante válido.');
        }
        if ($solicitado !== null && $solicitado !== $propietario) {
            fallarGrado(409, 'USUARIO_NO_COINCIDE', 'El usuario indicado no coincide con el propietario del Grado.');
        }
        return $propietario;
    }

    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarGrado(403, 'USUARIO_AJENO', 'No puede operar Grados para otro usuario.');
    }
    if ($propietario !== $actor['usuario']) {
        fallarGrado(403, 'GRADO_AJENO', 'No puede operar un Grado perteneciente a otro usuario.');
    }
    return $propietario;
}

function exigirCsrfGrado(): void
{
    $tokenSesion = $_SESSION['csrf_grado'] ?? null;
    $tokenCliente = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (
        !is_string($tokenSesion)
        || !is_string($tokenCliente)
        || strlen($tokenSesion) !== 64
        || !hash_equals($tokenSesion, $tokenCliente)
    ) {
        fallarGrado(403, 'CSRF_INVALIDO', 'La solicitud no contiene un token de seguridad válido.');
    }
}

/** @return array{instituto:int,titulo:int,fecha:string} */
function datosGrado(Grado $grado): array
{
    $instituto = enteroPositivoGrado($_POST['inst'] ?? null, 'inst');
    $titulo = enteroPositivoGrado($_POST['titulo'] ?? null, 'titulo');
    $fecha = isset($_POST['fecha']) && is_string($_POST['fecha']) ? $_POST['fecha'] : '';
    $fechaValida = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $erroresFecha = DateTimeImmutable::getLastErrors();
    if (
        $fechaValida === false
        || ($erroresFecha !== false && ($erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0))
        || $fechaValida->format('Y-m-d') !== $fecha
    ) {
        fallarGrado(422, 'FECHA_INVALIDA', 'La fecha de graduación no es válida.');
    }
    if (!$grado->institucionExiste($instituto)) {
        fallarGrado(404, 'INSTITUCION_NO_ENCONTRADA', 'La Institución indicada no existe.');
    }
    if (!$grado->tituloExiste($titulo)) {
        fallarGrado(404, 'TITULO_NO_ENCONTRADO', 'El Título indicado no existe.');
    }
    return ['instituto' => $instituto, 'titulo' => $titulo, 'fecha' => $fecha];
}

/** @param array<string, mixed> $fila
 *  @return array<string, mixed>
 */
function gradoPublico(array $fila): array
{
    unset($fila['usuario']);
    return $fila;
}

$grado = new Grado();

try {
    $op = isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '';
    if (!in_array($op, ['insert-update', 'read', 'read_grado_id', 'delete'], true)) {
        fallarGrado(400, 'OPERACION_INVALIDA', 'La operación solicitada no es válida.');
    }

    $actor = actorGrado();

    if ($op === 'read') {
        $usuario = usuarioObjetivoGrado($actor, $grado);
        responderGrado(200, [
            'ok' => true,
            'datos' => $grado->mostrar($usuario),
            'mensaje' => 'Grados académicos obtenidos correctamente.',
        ]);
    }

    if ($op === 'read_grado_id') {
        $idGrado = enteroPositivoGrado($_POST['id_grado'] ?? null, 'id_grado');
        $fila = $grado->mostrarGrado($idGrado);
        if ($fila === null) {
            fallarGrado(404, 'GRADO_NO_ENCONTRADO', 'El Grado académico indicado no existe.');
        }
        autorizarFilaGrado($actor, $fila, $grado);
        responderGrado(200, [
            'ok' => true,
            'datos' => gradoPublico($fila),
            'mensaje' => 'Grado académico obtenido correctamente.',
        ]);
    }

    exigirCsrfGrado();

    if ($op === 'insert-update') {
        $idGrado = idGradoInsertUpdate();

        if ($idGrado === 0) {
            $usuario = usuarioObjetivoGrado($actor, $grado);
            $datos = datosGrado($grado);
            $resultado = $grado->insertar($usuario, $datos['instituto'], $datos['titulo'], $datos['fecha']);
            $idInsertado = isset($resultado['idInsertado']) ? (int) $resultado['idInsertado'] : 0;
            if ((int) $resultado['filasAfectadas'] !== 1 || $idInsertado <= 0) {
                throw new RuntimeException('La creación no afectó exactamente un Grado.');
            }
            responderGrado(201, [
                'ok' => true,
                'codigo' => 'GRADO_CREADO',
                'datos' => ['id_grado' => $idInsertado],
                'mensaje' => 'Grado académico registrado correctamente.',
            ]);
        }

        $fila = $grado->mostrarGrado($idGrado);
        if ($fila === null) {
            fallarGrado(404, 'GRADO_NO_ENCONTRADO', 'El Grado académico indicado no existe.');
        }
        $propietario = autorizarFilaGrado($actor, $fila, $grado);
        $datos = datosGrado($grado);
        $sinCambios = (int) $fila['inst_grado'] === $datos['instituto']
            && (int) $fila['tit_grado'] === $datos['titulo']
            && (string) $fila['fech_graduacion'] === $datos['fecha'];
        if ($sinCambios) {
            responderGrado(200, [
                'ok' => true,
                'codigo' => 'GRADO_SIN_CAMBIOS',
                'datos' => ['id_grado' => $idGrado, 'cambios' => false],
                'mensaje' => 'El Grado académico ya contiene esos datos.',
            ]);
        }
        $resultado = $grado->editar(
            $idGrado,
            $propietario,
            $datos['instituto'],
            $datos['titulo'],
            $datos['fecha']
        );
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarGrado(409, 'GRADO_NO_ACTUALIZADO', 'No fue posible confirmar la actualización del Grado.');
        }
        responderGrado(200, [
            'ok' => true,
            'codigo' => 'GRADO_ACTUALIZADO',
            'datos' => ['id_grado' => $idGrado, 'cambios' => true],
            'mensaje' => 'Grado académico actualizado correctamente.',
        ]);
    }

    $idGrado = enteroPositivoGrado($_POST['id_grado'] ?? null, 'id_grado');
    $fila = $grado->mostrarGrado($idGrado);
    if ($fila === null) {
        fallarGrado(404, 'GRADO_NO_ENCONTRADO', 'El Grado académico indicado no existe.');
    }
    $propietario = autorizarFilaGrado($actor, $fila, $grado);
    $resultado = $grado->eliminar($idGrado, $propietario);
    if ((int) $resultado['filasAfectadas'] !== 1) {
        fallarGrado(409, 'GRADO_NO_ELIMINADO', 'No fue posible confirmar la eliminación del Grado.');
    }
    responderGrado(200, [
        'ok' => true,
        'codigo' => 'GRADO_ELIMINADO',
        'datos' => ['id_grado' => $idGrado],
        'mensaje' => 'Grado académico eliminado correctamente.',
    ]);
} catch (GradoHttpError $error) {
    responderGrado($error->statusCode, [
        'ok' => false,
        'error' => $error->errorCode,
        'mensaje' => $error->getMessage(),
    ]);
} catch (PDOException $error) {
    error_log('[GRADO_PERSISTENCIA] ' . $error->getMessage());
    if ((string) $error->getCode() === '23000') {
        responderGrado(409, [
            'ok' => false,
            'error' => 'INTEGRIDAD_GRADO',
            'mensaje' => 'La operación no cumple las restricciones de integridad vigentes.',
        ]);
    }
    responderGrado(500, [
        'ok' => false,
        'error' => 'ERROR_TECNICO',
        'mensaje' => 'No fue posible completar la operación de Grado académico.',
    ]);
} catch (Throwable $error) {
    error_log('[GRADO_ENDPOINT] ' . $error->getMessage());
    responderGrado(500, [
        'ok' => false,
        'error' => 'ERROR_TECNICO',
        'mensaje' => 'No fue posible completar la operación de Grado académico.',
    ]);
}
