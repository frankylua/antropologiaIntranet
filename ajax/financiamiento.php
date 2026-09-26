<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Financiamiento;
use App\Model\Usuario;
use App\Security\Authorization;

final class FinanciamientoError extends RuntimeException
{
    public function __construct(public int $http, public string $codigo, string $message)
    {
        parent::__construct($message);
    }
}
function fallarFinanciamiento(int $http, string $codigo, string $mensaje): never
{
    throw new FinanciamientoError($http, $codigo, $mensaje);
}
function responderFinanciamiento(int $http, string $codigo, string $mensaje, mixed $datos = null): never
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['ok' => $http < 400, 'codigo' => $codigo, 'mensaje' => $mensaje, 'datos' => $datos],
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}
function idFinanciamiento(mixed $valor): int
{
    if ((!is_int($valor) && !is_string($valor))
        || preg_match('/^[1-9][0-9]*$/D', (string) $valor) !== 1
    ) {
        fallarFinanciamiento(400, 'ID_INVALIDO', 'El identificador del Financiamiento no es válido.');
    }
    $id = filter_var($valor, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]);
    if ($id === false) {
        fallarFinanciamiento(400, 'ID_INVALIDO', 'El identificador del Financiamiento no es válido.');
    }
    return $id;
}
function nombreFinanciamiento(mixed $valor): string
{
    if (!is_string($valor) || trim($valor) === '') {
        fallarFinanciamiento(400, 'VALIDACION_INVALIDA', 'El nombre del Financiamiento es obligatorio.');
    }
    return trim($valor);
}
/** @return array{global:bool,usuario:int} */
function actorFinanciamiento(Usuario $usuario): array
{
    $loginRaw = $_SESSION['login'] ?? null;
    if (!is_scalar($loginRaw) || preg_match('/^[1-9][0-9]*$/D', (string) $loginRaw) !== 1) {
        fallarFinanciamiento(401, 'NO_AUTENTICADO', 'Debe iniciar sesión.');
    }
    $login = filter_var($loginRaw, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]);
    if ($login === false) fallarFinanciamiento(401, 'NO_AUTENTICADO', 'Debe iniciar sesión.');

    $global = Authorization::hasAny(['admin', 'comite'])
        && ((int) ($_SESSION['admin'] ?? 0) === $login
            || (int) ($_SESSION['comite'] ?? 0) === $login);
    if ($global) return ['global' => true, 'usuario' => 0];

    $identidades = $_SESSION['id_usuario'] ?? null;
    $idUsuario = is_array($identidades)
        && count($identidades) === 1
        && is_array($identidades[0] ?? null)
        && isset($identidades[0]['id_usuario'])
        ? idFinanciamiento($identidades[0]['id_usuario'])
        : 0;
    $esEstudiante = (int) ($_SESSION['estudiante'] ?? 0) === $login
        && Authorization::hasCapability('perfil.ver');
    $esDocente = (int) ($_SESSION['docente'] ?? 0) === $login;
    if ($idUsuario < 1 || $esEstudiante === $esDocente
        || !$usuario->usuarioAcademicoExiste($idUsuario)
    ) {
        fallarFinanciamiento(403, 'NO_AUTORIZADO', 'Perfil académico inválido.');
    }
    return ['global' => false, 'usuario' => $idUsuario];
}
function validarCsrfFinanciamiento(): void
{
    $sesion = $_SESSION['csrf_financiamiento'] ?? null;
    $recibido = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!is_string($sesion) || !is_string($recibido)
        || preg_match('/^[a-f0-9]{64}$/D', $sesion) !== 1
        || !hash_equals($sesion, $recibido)
    ) {
        fallarFinanciamiento(403, 'CSRF_INVALIDO', 'Token CSRF inválido.');
    }
}
function validarContextoProyecto(array $actor, Usuario $usuario): void
{
    $subject = idFinanciamiento($_POST['subject_usuario_id'] ?? null);
    if ($actor['global']) return;
    if ($subject !== $actor['usuario'] || !$usuario->usuarioAcademicoExiste($subject)) {
        fallarFinanciamiento(403, 'NO_AUTORIZADO', 'Contexto de Proyecto no autorizado.');
    }
}
function esConflictoFkFinanciamiento(PDOException $exception): bool
{
    $errorInfo = $exception->errorInfo;
    if ((string) $exception->getCode() !== '23000'
        || !is_array($errorInfo)
        || (int) ($errorInfo[1] ?? 0) !== 1451
    ) {
        return false;
    }
    $detalle = is_string($errorInfo[2] ?? null)
        ? $errorInfo[2]
        : $exception->getMessage();
    return stripos($detalle, 'fk_financ__proyecto') !== false;
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        fallarFinanciamiento(405, 'METODO_NO_PERMITIDO', 'Utilice POST.');
    }
    $financiamiento = new Financiamiento();
    $usuario = new Usuario();
    $actor = actorFinanciamiento($usuario);
    $op = $_POST['op'] ?? null;
    if (!is_string($op)
        || !in_array($op, ['read-admin', 'read-project', 'insert-update', 'delete'], true)
    ) {
        fallarFinanciamiento(400, 'OPERACION_INVALIDA', 'Operación inválida.');
    }
    if ($op === 'read-admin' || $op === 'read-project') {
        if ($op === 'read-admin' && !$actor['global']) {
            fallarFinanciamiento(403, 'NO_AUTORIZADO', 'Lectura administrativa no autorizada.');
        }
        if ($op === 'read-project') validarContextoProyecto($actor, $usuario);
        echo json_encode(
            $financiamiento->mostrar(),
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
        exit;
    }
    if (!$actor['global']) {
        fallarFinanciamiento(
            403,
            'NO_AUTORIZADO',
            'Sólo Admin o Comité puede administrar Financiamientos.'
        );
    }
    validarCsrfFinanciamiento();

    if ($op === 'insert-update') {
        $idRaw = $_POST['id'] ?? '';
        $esCreacion = $idRaw === '' || $idRaw === 0 || $idRaw === '0';
        $nombre = nombreFinanciamiento($_POST['nombre'] ?? null);
        if ($esCreacion) {
            $resultado = $financiamiento->insertar($nombre);
            if ((int) ($resultado['filasAfectadas'] ?? 0) !== 1) {
                fallarFinanciamiento(500, 'ERROR_PERSISTENCIA', 'No fue posible crear el Financiamiento.');
            }
            responderFinanciamiento(
                201,
                'FINANCIAMIENTO_CREADO',
                'Fuente de Financiamiento registrada.'
            );
        }
        $id = idFinanciamiento($idRaw);
        if (!$financiamiento->existe($id)) {
            fallarFinanciamiento(404, 'FINANCIAMIENTO_NO_ENCONTRADO', 'Financiamiento inexistente.');
        }
        $resultado = $financiamiento->editar($id, $nombre);
        $filas = (int) ($resultado['filasAfectadas'] ?? -1);
        if (!in_array($filas, [0, 1], true)) {
            fallarFinanciamiento(500, 'ERROR_PERSISTENCIA', 'No fue posible actualizar el Financiamiento.');
        }
        responderFinanciamiento(
            200,
            'FINANCIAMIENTO_ACTUALIZADO',
            'Fuente de Financiamiento actualizada.',
            ['id_financ' => $id, 'cambios' => $filas]
        );
    }

    $id = idFinanciamiento($_POST['id'] ?? null);
    if (!$financiamiento->existe($id)) {
        fallarFinanciamiento(404, 'FINANCIAMIENTO_NO_ENCONTRADO', 'Financiamiento inexistente.');
    }
    if ($financiamiento->estaEnUso($id)) {
        fallarFinanciamiento(
            409,
            'FINANCIAMIENTO_EN_USO',
            'Financiamiento en uso y no puede eliminarse.'
        );
    }
    try {
        $resultado = $financiamiento->eliminar($id);
    } catch (PDOException $e) {
        if (esConflictoFkFinanciamiento($e)) {
            fallarFinanciamiento(
                409,
                'FINANCIAMIENTO_EN_USO',
                'Financiamiento en uso y no puede eliminarse.'
            );
        }
        throw $e;
    }
    if ((int) ($resultado['filasAfectadas'] ?? 0) !== 1) {
        fallarFinanciamiento(500, 'ERROR_PERSISTENCIA', 'No fue posible eliminar el Financiamiento.');
    }
    responderFinanciamiento(
        200,
        'FINANCIAMIENTO_ELIMINADO',
        'Fuente de Financiamiento eliminada.'
    );
} catch (FinanciamientoError $e) {
    responderFinanciamiento($e->http, $e->codigo, $e->getMessage());
} catch (PDOException $e) {
    error_log('[FINANCIAMIENTO] ' . $e->getMessage());
    responderFinanciamiento(500, 'ERROR_PERSISTENCIA', 'Error de persistencia.');
} catch (Throwable $e) {
    error_log('[FINANCIAMIENTO] ' . $e->getMessage());
    responderFinanciamiento(500, 'ERROR_TECNICO', 'Error técnico.');
}
