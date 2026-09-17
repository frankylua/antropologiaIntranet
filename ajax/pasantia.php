<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Pasantia;
use App\Security\Authorization;

final class PasantiaHttpError extends RuntimeException
{
    public function __construct(public readonly int $status, public readonly string $codigo, string $mensaje)
    {
        parent::__construct($mensaje);
    }
}

function responderPasantia(int $status, string $codigo, string $mensaje, mixed $datos = null): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => $status < 400, 'codigo' => $codigo, 'mensaje' => $mensaje, 'datos' => $datos], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function fallarPasantia(int $status, string $codigo, string $mensaje): never
{
    throw new PasantiaHttpError($status, $codigo, $mensaje);
}

function idValidoPasantia(mixed $valor): ?int
{
    if ((!is_int($valor) && !is_string($valor)) || preg_match('/^[1-9][0-9]*$/D', (string) $valor) !== 1) {
        return null;
    }
    $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
    return $id === false ? null : $id;
}

function enteroPasantia(mixed $valor, string $campo): int
{
    $id = idValidoPasantia($valor);
    if ($id === null) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', "El campo {$campo} debe ser un entero positivo válido.");
    }
    return $id;
}

/** @return array{global:bool,usuario:int} */
function actorPasantia(Pasantia $modelo): array
{
    $login = idValidoPasantia($_SESSION['login'] ?? null);
    if ($login === null) {
        fallarPasantia(401, 'NO_AUTENTICADO', 'Debe iniciar sesión para operar Pasantías.');
    }
    $global = Authorization::hasAny(['admin', 'comite']) && (
        idValidoPasantia($_SESSION['admin'] ?? null) === $login
        || idValidoPasantia($_SESSION['comite'] ?? null) === $login
    );
    if ($global) {
        return ['global' => true, 'usuario' => 0];
    }
    $usuarios = $_SESSION['id_usuario'] ?? null;
    $usuario = is_array($usuarios) && count($usuarios) === 1 && is_array($usuarios[0] ?? null)
        ? idValidoPasantia($usuarios[0]['id_usuario'] ?? null) : null;
    if ($usuario === null) {
        fallarPasantia(403, 'NO_AUTORIZADO', 'No tiene una identidad de perfil válida.');
    }
    $estudiante = idValidoPasantia($_SESSION['estudiante'] ?? null) === $login;
    $profesor = idValidoPasantia($_SESSION['docente'] ?? null) === $login;
    $especializacion = $modelo->especializacion($usuario);
    if ($estudiante === $profesor || $especializacion === null
        || ($estudiante && ($especializacion !== 'estudiante' || !Authorization::hasCapability('perfil.ver')))
        || ($profesor && $especializacion !== 'profesor')) {
        fallarPasantia(403, 'NO_AUTORIZADO', 'No tiene acceso a un perfil válido y coherente.');
    }
    return ['global' => false, 'usuario' => $usuario];
}

function usuarioSolicitadoPasantia(): ?int
{
    return array_key_exists('usuario', $_POST) ? enteroPasantia($_POST['usuario'], 'usuario') : null;
}

function usuarioObjetivoPasantia(array $actor, Pasantia $modelo): int
{
    $solicitado = usuarioSolicitadoPasantia();
    if ($actor['global']) {
        if ($solicitado === null || $modelo->especializacion($solicitado) === null) {
            fallarPasantia(400, 'VALIDACION_INVALIDA', 'El objetivo debe corresponder a un Profesor o Estudiante válido.');
        }
        return $solicitado;
    }
    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarPasantia(403, 'OWNERSHIP_INVALIDO', 'No puede operar Pasantías de otro usuario.');
    }
    return $actor['usuario'];
}

function filaAutorizadaPasantia(array $actor, Pasantia $modelo, int $id): array
{
    $solicitado = usuarioSolicitadoPasantia();
    if (!$actor['global'] && $solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarPasantia(403, 'OWNERSHIP_INVALIDO', 'No puede operar Pasantías de otro usuario.');
    }
    $fila = $modelo->detalle($id);
    if ($fila === null) {
        fallarPasantia(404, 'PASANTIA_NO_ENCONTRADA', 'La Pasantía no existe.');
    }
    $propietario = (int) $fila['usuario'];
    if ((!$actor['global'] && $propietario !== $actor['usuario'])
        || ($solicitado !== null && $solicitado !== $propietario)) {
        fallarPasantia(403, 'OWNERSHIP_INVALIDO', 'No puede operar la Pasantía indicada para ese usuario.');
    }
    if ($modelo->especializacion($propietario) === null) {
        fallarPasantia(403, 'NO_AUTORIZADO', 'La Pasantía no pertenece a una identidad válida.');
    }
    return $fila;
}

function exigirCsrfPasantia(): void
{
    $sesion = $_SESSION['csrf_pasantia'] ?? null;
    $cliente = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!is_string($sesion) || !is_string($cliente)
        || preg_match('/^[a-f0-9]{64}$/D', $sesion) !== 1 || !hash_equals($sesion, $cliente)) {
        fallarPasantia(403, 'CSRF_INVALIDO', 'La solicitud no contiene un token de seguridad válido.');
    }
}

function textoPasantia(mixed $valor, string $campo): string
{
    if (!is_string($valor)) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', "El campo {$campo} es obligatorio.");
    }
    $texto = trim($valor);
    $longitud = preg_match_all('/./us', $texto);
    if ($texto === '' || $longitud === false || $longitud > 45) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', "El campo {$campo} debe contener entre 1 y 45 caracteres.");
    }
    return $texto;
}

function fechaPasantia(mixed $valor, string $campo): string
{
    if (!is_string($valor) || preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $valor) !== 1
        || $valor < '1000-01-01') {
        fallarPasantia(400, 'VALIDACION_INVALIDA', "El campo {$campo} requiere una fecha válida YYYY-MM-DD.");
    }
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    $errores = DateTimeImmutable::getLastErrors();
    if ($fecha === false || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))
        || $fecha->format('Y-m-d') !== $valor) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', "El campo {$campo} no contiene una fecha civil real.");
    }
    return $valor;
}

function datosPasantia(Pasantia $modelo): array
{
    $datos = [
        'inst_pasant' => enteroPasantia($_POST['inst_pasant'] ?? null, 'institución'),
        'pais_pasant' => enteroPasantia($_POST['pais_pasant'] ?? null, 'país'),
        'prof_patr' => textoPasantia($_POST['prof_patr'] ?? null, 'patrocinante'),
        'fondo' => textoPasantia($_POST['fondo'] ?? null, 'fondo'),
        'ciudad' => textoPasantia($_POST['ciudad'] ?? null, 'ciudad'),
        'fech_in' => fechaPasantia($_POST['fech_in'] ?? null, 'fecha de inicio'),
        'fech_ter' => fechaPasantia($_POST['fech_ter'] ?? null, 'fecha de término'),
    ];
    if ($datos['fech_in'] > $datos['fech_ter']) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', 'La fecha de inicio no puede ser posterior al término.');
    }
    if (!$modelo->institucionExiste($datos['inst_pasant']) || !$modelo->paisExiste($datos['pais_pasant'])) {
        fallarPasantia(400, 'VALIDACION_INVALIDA', 'La institución y el país deben existir.');
    }
    return $datos;
}

function pasantiaPublica(array $fila): array
{
    unset($fila['usuario']);
    return $fila;
}

try {
    $modelo = new Pasantia();
    $actor = actorPasantia($modelo);
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        fallarPasantia(405, 'METODO_NO_PERMITIDO', 'Utilice POST para operar Pasantías.');
    }
    $op = $_POST['op'] ?? null;
    if (!is_string($op) || !in_array($op, ['list', 'detail', 'create', 'update', 'delete'], true)) {
        fallarPasantia(400, 'OPERACION_INVALIDA', 'La operación solicitada no es válida.');
    }
    if (in_array($op, ['create', 'update', 'delete'], true)) {
        exigirCsrfPasantia();
    }
    if ($op === 'list') {
        $usuario = usuarioObjetivoPasantia($actor, $modelo);
        responderPasantia(200, 'LISTA_OBTENIDA', 'Pasantías obtenidas correctamente.', array_map('pasantiaPublica', $modelo->mostrar($usuario)));
    }
    if ($op === 'create') {
        $usuario = usuarioObjetivoPasantia($actor, $modelo);
        $resultado = $modelo->insertar($usuario, datosPasantia($modelo));
        $id = idValidoPasantia($resultado['idInsertado'] ?? null);
        if ((int) $resultado['filasAfectadas'] !== 1 || $id === null) {
            fallarPasantia(409, 'CONFLICTO_PERSISTENCIA', 'No fue posible confirmar la creación de la Pasantía.');
        }
        responderPasantia(201, 'PASANTIA_CREADA', 'Pasantía registrada correctamente.', ['id_pasantia' => $id]);
    }
    $id = enteroPasantia($_POST['id_pasantia'] ?? null, 'id_pasantia');
    $fila = filaAutorizadaPasantia($actor, $modelo, $id);
    if ($op === 'detail') {
        responderPasantia(200, 'PASANTIA_OBTENIDA', 'Pasantía obtenida correctamente.', pasantiaPublica($fila));
    }
    if ($op === 'update') {
        $datos = datosPasantia($modelo);
        $sinCambios = true;
        foreach ($datos as $campo => $valor) {
            if ((string) $fila[$campo] !== (string) $valor) {
                $sinCambios = false;
                break;
            }
        }
        if ($sinCambios) {
            responderPasantia(200, 'PASANTIA_SIN_CAMBIOS', 'La Pasantía ya contiene esos datos.', ['id_pasantia' => $id, 'cambios' => false]);
        }
        $resultado = $modelo->editar($id, (int) $fila['usuario'], $datos);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarPasantia(409, 'CONFLICTO_PERSISTENCIA', 'No fue posible confirmar la actualización de la Pasantía.');
        }
        responderPasantia(200, 'PASANTIA_ACTUALIZADA', 'Pasantía actualizada correctamente.', ['id_pasantia' => $id, 'cambios' => true]);
    }
    $resultado = $modelo->eliminar($id, (int) $fila['usuario']);
    if ((int) $resultado['filasAfectadas'] !== 1) {
        fallarPasantia(409, 'CONFLICTO_PERSISTENCIA', 'No fue posible confirmar la eliminación de la Pasantía.');
    }
    responderPasantia(200, 'PASANTIA_ELIMINADA', 'Pasantía eliminada correctamente.', ['id_pasantia' => $id]);
} catch (PasantiaHttpError $error) {
    responderPasantia($error->status, $error->codigo, $error->getMessage());
} catch (PDOException $error) {
    error_log('[PASANTIA_PERSISTENCIA] ' . $error->getMessage());
    if ((string) $error->getCode() === '23000') {
        responderPasantia(409, 'CONFLICTO_PERSISTENCIA', 'La operación no cumple las restricciones de integridad vigentes.');
    }
    responderPasantia(500, 'ERROR_PERSISTENCIA', 'No fue posible completar la operación de Pasantía.');
} catch (Throwable $error) {
    error_log('[PASANTIA_ENDPOINT] ' . $error->getMessage());
    responderPasantia(500, 'ERROR_TECNICO', 'No fue posible completar la operación de Pasantía.');
}
