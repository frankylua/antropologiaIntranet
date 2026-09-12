<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Postdoctorado;
use App\Security\Authorization;

final class PostdoctoradoHttpError extends RuntimeException
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
function responderPostdoctorado(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function fallarPostdoctorado(int $statusCode, string $errorCode, string $message): never
{
    throw new PostdoctoradoHttpError($statusCode, $errorCode, $message);
}

/** @return array{global:bool,usuario:int} */
function actorPostdoctorado(Postdoctorado $postdoctorado): array
{
    $login = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
        ? (int) $_SESSION['login']
        : 0;
    if ($login <= 0) {
        fallarPostdoctorado(
            401,
            'NO_AUTENTICADO',
            'Debe iniciar sesión para operar Postdoctorados.'
        );
    }

    $rolGlobal = Authorization::hasAny(['admin', 'comite']) && (
        (isset($_SESSION['admin'])
            && is_scalar($_SESSION['admin'])
            && (int) $_SESSION['admin'] === $login)
        || (isset($_SESSION['comite'])
            && is_scalar($_SESSION['comite'])
            && (int) $_SESSION['comite'] === $login)
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
        fallarPostdoctorado(
            403,
            'PERFIL_NO_VIGENTE',
            'No tiene acceso vigente a un perfil autorizado.'
        );
    }

    $esProfesor = isset($_SESSION['docente'])
        && is_scalar($_SESSION['docente'])
        && (int) $_SESSION['docente'] === $login;
    $esEstudiante = isset($_SESSION['estudiante'])
        && is_scalar($_SESSION['estudiante'])
        && (int) $_SESSION['estudiante'] === $login
        && Authorization::hasCapability('perfil.ver');
    if (!$esProfesor && !$esEstudiante) {
        fallarPostdoctorado(
            403,
            'PERFIL_NO_VIGENTE',
            'No tiene acceso vigente a un perfil autorizado.'
        );
    }

    $usuario = (int) $usuarios[0]['id_usuario'];
    if (!$postdoctorado->usuarioObjetivoValido($usuario)) {
        fallarPostdoctorado(
            403,
            'PERFIL_NO_VIGENTE',
            'El perfil autenticado no corresponde exactamente a Profesor o Estudiante.'
        );
    }

    return ['global' => false, 'usuario' => $usuario];
}

function enteroPositivoPostdoctorado(mixed $value, string $field): int
{
    if (!is_int($value) && !is_string($value)) {
        fallarPostdoctorado(400, 'CAMPO_INVALIDO', "El campo {$field} no es válido.");
    }

    $raw = (string) $value;
    if (preg_match('/^[1-9][0-9]*$/D', $raw) !== 1) {
        fallarPostdoctorado(
            400,
            'CAMPO_INVALIDO',
            "El campo {$field} debe ser un entero positivo."
        );
    }

    $resultado = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($resultado === false) {
        fallarPostdoctorado(
            400,
            'CAMPO_INVALIDO',
            "El campo {$field} está fuera de rango."
        );
    }

    return $resultado;
}

function usuarioSolicitadoPostdoctorado(): ?int
{
    if (!array_key_exists('usuario', $_POST)) {
        return null;
    }

    return enteroPositivoPostdoctorado($_POST['usuario'], 'usuario');
}

/** @param array{global:bool,usuario:int} $actor */
function usuarioObjetivoPostdoctorado(array $actor, Postdoctorado $postdoctorado): int
{
    $solicitado = usuarioSolicitadoPostdoctorado();
    if ($actor['global']) {
        if ($solicitado === null || !$postdoctorado->usuarioObjetivoValido($solicitado)) {
            fallarPostdoctorado(
                404,
                'USUARIO_OBJETIVO_INVALIDO',
                'El usuario objetivo no existe o no corresponde exactamente a Profesor o Estudiante.'
            );
        }
        return $solicitado;
    }

    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarPostdoctorado(
            403,
            'USUARIO_AJENO',
            'No puede operar Postdoctorados para otro usuario.'
        );
    }

    return $actor['usuario'];
}

/**
 * @param array{global:bool,usuario:int} $actor
 * @param array<string, mixed> $fila
 */
function autorizarFilaPostdoctorado(
    array $actor,
    array $fila,
    Postdoctorado $postdoctorado
): int {
    $propietario = isset($fila['usuario']) ? (int) $fila['usuario'] : 0;
    if ($propietario <= 0) {
        throw new RuntimeException('El Postdoctorado no contiene un propietario válido.');
    }

    $solicitado = usuarioSolicitadoPostdoctorado();
    if ($actor['global']) {
        if (!$postdoctorado->usuarioObjetivoValido($propietario)) {
            fallarPostdoctorado(
                409,
                'PROPIETARIO_INVALIDO',
                'El Postdoctorado no pertenece a un Profesor o Estudiante válido.'
            );
        }
        if ($solicitado !== null && $solicitado !== $propietario) {
            fallarPostdoctorado(
                409,
                'USUARIO_NO_COINCIDE',
                'El usuario indicado no coincide con el propietario del Postdoctorado.'
            );
        }
        return $propietario;
    }

    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarPostdoctorado(
            403,
            'USUARIO_AJENO',
            'No puede operar Postdoctorados para otro usuario.'
        );
    }
    if ($propietario !== $actor['usuario']) {
        fallarPostdoctorado(
            403,
            'POSTDOCTORADO_AJENO',
            'No puede operar un Postdoctorado perteneciente a otro usuario.'
        );
    }

    return $propietario;
}

function exigirCsrfPostdoctorado(): void
{
    $tokenSesion = $_SESSION['csrf_postdoctorado'] ?? null;
    $tokenCliente = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (
        !is_string($tokenSesion)
        || !is_string($tokenCliente)
        || strlen($tokenSesion) !== 64
        || preg_match('/^[a-f0-9]{64}$/D', $tokenSesion) !== 1
        || !hash_equals($tokenSesion, $tokenCliente)
    ) {
        fallarPostdoctorado(
            403,
            'CSRF_INVALIDO',
            'La solicitud no contiene un token de seguridad válido.'
        );
    }
}

function fechaPostdoctorado(mixed $value, string $field): string
{
    $fecha = is_string($value) ? $value : '';
    $fechaValida = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTimeImmutable::getLastErrors();
    if (
        $fechaValida === false
        || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))
        || $fechaValida->format('Y-m-d') !== $fecha
    ) {
        fallarPostdoctorado(
            422,
            'FECHA_INVALIDA',
            "El campo {$field} no contiene una fecha civil válida con formato YYYY-MM-DD."
        );
    }

    return $fecha;
}

function profesorPatrocinantePostdoctorado(mixed $value): string
{
    if (!is_string($value)) {
        fallarPostdoctorado(
            422,
            'PROFESOR_INVALIDO',
            'El Profesor o Profesora patrocinante es obligatorio.'
        );
    }

    $profesor = trim($value);
    $longitud = function_exists('mb_strlen') ? mb_strlen($profesor, 'UTF-8') : strlen($profesor);
    $controlInvalido = preg_match('/[\x00-\x1F\x7F]/u', $profesor);
    if (
        $profesor === ''
        || $longitud > 60
        || $controlInvalido !== 0
    ) {
        fallarPostdoctorado(
            422,
            'PROFESOR_INVALIDO',
            'El Profesor o Profesora patrocinante no es válido.'
        );
    }

    return $profesor;
}

/** @return array{profesor:string,institucion:int,fechaInicio:string,fechaTermino:string} */
function datosPostdoctorado(Postdoctorado $postdoctorado): array
{
    $profesor = profesorPatrocinantePostdoctorado($_POST['prof'] ?? null);
    $institucion = enteroPositivoPostdoctorado($_POST['inst'] ?? null, 'inst');
    $fechaInicio = fechaPostdoctorado($_POST['fech_in'] ?? null, 'fecha_inicio');
    $fechaTermino = fechaPostdoctorado($_POST['fech_ter'] ?? null, 'fecha_termino');

    if ($fechaInicio > $fechaTermino) {
        fallarPostdoctorado(
            422,
            'INTERVALO_INVALIDO',
            'La fecha de inicio no puede ser posterior a la fecha de término.'
        );
    }
    if (!$postdoctorado->institucionExiste($institucion)) {
        fallarPostdoctorado(
            404,
            'INSTITUCION_NO_ENCONTRADA',
            'La Institución indicada no existe.'
        );
    }

    return [
        'profesor' => $profesor,
        'institucion' => $institucion,
        'fechaInicio' => $fechaInicio,
        'fechaTermino' => $fechaTermino,
    ];
}

/** @param array<string, mixed> $fila
 *  @return array<string, mixed>
 */
function postdoctoradoPublico(array $fila): array
{
    unset($fila['usuario']);
    return $fila;
}

$postdoctorado = new Postdoctorado();

try {
    $op = isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '';
    if (!in_array($op, ['insert', 'update', 'read_postdoc_id', 'read', 'delete'], true)) {
        fallarPostdoctorado(
            400,
            'OPERACION_INVALIDA',
            'La operación solicitada no es válida.'
        );
    }

    $actor = actorPostdoctorado($postdoctorado);

    if ($op === 'read') {
        $usuario = usuarioObjetivoPostdoctorado($actor, $postdoctorado);
        responderPostdoctorado(200, [
            'ok' => true,
            'datos' => $postdoctorado->mostrar($usuario),
            'mensaje' => 'Postdoctorados obtenidos correctamente.',
        ]);
    }

    if ($op === 'read_postdoc_id') {
        $idPostdoctorado = enteroPositivoPostdoctorado(
            $_POST['id_postdoc'] ?? null,
            'id_postdoc'
        );
        $fila = $postdoctorado->mostrarPostdoc($idPostdoctorado);
        if ($fila === null) {
            fallarPostdoctorado(
                404,
                'POSTDOCTORADO_NO_ENCONTRADO',
                'El Postdoctorado indicado no existe.'
            );
        }
        autorizarFilaPostdoctorado($actor, $fila, $postdoctorado);
        responderPostdoctorado(200, [
            'ok' => true,
            'datos' => postdoctoradoPublico($fila),
            'mensaje' => 'Postdoctorado obtenido correctamente.',
        ]);
    }

    exigirCsrfPostdoctorado();

    if ($op === 'insert') {
        $usuario = usuarioObjetivoPostdoctorado($actor, $postdoctorado);
        $datos = datosPostdoctorado($postdoctorado);
        $resultado = $postdoctorado->insertar(
            $usuario,
            $datos['profesor'],
            $datos['institucion'],
            $datos['fechaInicio'],
            $datos['fechaTermino']
        );
        $idInsertado = isset($resultado['idInsertado']) ? (int) $resultado['idInsertado'] : 0;
        if ((int) $resultado['filasAfectadas'] !== 1 || $idInsertado <= 0) {
            throw new RuntimeException('La creación no afectó exactamente un Postdoctorado.');
        }
        responderPostdoctorado(201, [
            'ok' => true,
            'codigo' => 'POSTDOCTORADO_CREADO',
            'datos' => ['id_postdoc' => $idInsertado],
            'mensaje' => 'Postdoctorado registrado correctamente.',
        ]);
    }

    if ($op === 'update') {
        $idPostdoctorado = enteroPositivoPostdoctorado(
            $_POST['id_postdoc'] ?? null,
            'id_postdoc'
        );
        $fila = $postdoctorado->mostrarPostdoc($idPostdoctorado);
        if ($fila === null) {
            fallarPostdoctorado(
                404,
                'POSTDOCTORADO_NO_ENCONTRADO',
                'El Postdoctorado indicado no existe.'
            );
        }
        $propietario = autorizarFilaPostdoctorado($actor, $fila, $postdoctorado);
        $datos = datosPostdoctorado($postdoctorado);
        $sinCambios = (int) $fila['inst_postdoc'] === $datos['institucion']
            && (string) $fila['prof'] === $datos['profesor']
            && (string) $fila['fecha_inicio'] === $datos['fechaInicio']
            && (string) $fila['fecha_termino'] === $datos['fechaTermino'];
        if ($sinCambios) {
            responderPostdoctorado(200, [
                'ok' => true,
                'codigo' => 'POSTDOCTORADO_SIN_CAMBIOS',
                'datos' => ['id_postdoc' => $idPostdoctorado, 'cambios' => false],
                'mensaje' => 'El Postdoctorado ya contiene esos datos.',
            ]);
        }

        $resultado = $postdoctorado->editarPostdoc(
            $idPostdoctorado,
            $propietario,
            $datos['profesor'],
            $datos['institucion'],
            $datos['fechaInicio'],
            $datos['fechaTermino']
        );
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarPostdoctorado(
                409,
                'POSTDOCTORADO_NO_ACTUALIZADO',
                'No fue posible confirmar la actualización del Postdoctorado.'
            );
        }
        responderPostdoctorado(200, [
            'ok' => true,
            'codigo' => 'POSTDOCTORADO_ACTUALIZADO',
            'datos' => ['id_postdoc' => $idPostdoctorado, 'cambios' => true],
            'mensaje' => 'Postdoctorado actualizado correctamente.',
        ]);
    }

    $idPostdoctorado = enteroPositivoPostdoctorado(
        $_POST['id_postdoc'] ?? null,
        'id_postdoc'
    );
    $fila = $postdoctorado->mostrarPostdoc($idPostdoctorado);
    if ($fila === null) {
        fallarPostdoctorado(
            404,
            'POSTDOCTORADO_NO_ENCONTRADO',
            'El Postdoctorado indicado no existe.'
        );
    }
    $propietario = autorizarFilaPostdoctorado($actor, $fila, $postdoctorado);
    $resultado = $postdoctorado->eliminar($idPostdoctorado, $propietario);
    if ((int) $resultado['filasAfectadas'] !== 1) {
        fallarPostdoctorado(
            409,
            'POSTDOCTORADO_NO_ELIMINADO',
            'No fue posible confirmar la eliminación del Postdoctorado.'
        );
    }
    responderPostdoctorado(200, [
        'ok' => true,
        'codigo' => 'POSTDOCTORADO_ELIMINADO',
        'datos' => ['id_postdoc' => $idPostdoctorado],
        'mensaje' => 'Postdoctorado eliminado correctamente.',
    ]);
} catch (PostdoctoradoHttpError $error) {
    responderPostdoctorado($error->statusCode, [
        'ok' => false,
        'error' => $error->errorCode,
        'mensaje' => $error->getMessage(),
    ]);
} catch (PDOException $error) {
    error_log('[POSTDOCTORADO_PERSISTENCIA] ' . $error->getMessage());
    if ((string) $error->getCode() === '23000') {
        responderPostdoctorado(409, [
            'ok' => false,
            'error' => 'INTEGRIDAD_POSTDOCTORADO',
            'mensaje' => 'La operación no cumple las restricciones de integridad vigentes.',
        ]);
    }
    responderPostdoctorado(500, [
        'ok' => false,
        'error' => 'ERROR_TECNICO',
        'mensaje' => 'No fue posible completar la operación de Postdoctorado.',
    ]);
} catch (Throwable $error) {
    error_log('[POSTDOCTORADO_ENDPOINT] ' . $error->getMessage());
    responderPostdoctorado(500, [
        'ok' => false,
        'error' => 'ERROR_TECNICO',
        'mensaje' => 'No fue posible completar la operación de Postdoctorado.',
    ]);
}
