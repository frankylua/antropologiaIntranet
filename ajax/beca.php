<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Beca;
use App\Security\Authorization;

final class BecaHttpError extends RuntimeException
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
function responderBeca(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function fallarBeca(int $statusCode, string $errorCode, string $message): never
{
    throw new BecaHttpError($statusCode, $errorCode, $message);
}

/** @return array{global:bool,usuario:int} */
function actorBeca(Beca $beca): array
{
    $login = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
        ? (int) $_SESSION['login']
        : 0;
    if ($login <= 0) {
        fallarBeca(401, 'NO_AUTENTICADO', 'Debe iniciar sesión para operar Becas.');
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
    $esEstudiante = isset($_SESSION['estudiante'])
        && is_scalar($_SESSION['estudiante'])
        && (int) $_SESSION['estudiante'] === $login
        && Authorization::hasCapability('perfil.ver');
    if (
        !$esEstudiante
        || !is_array($usuarios)
        || count($usuarios) !== 1
        || !isset($usuarios[0]['id_usuario'])
        || !is_scalar($usuarios[0]['id_usuario'])
        || (int) $usuarios[0]['id_usuario'] <= 0
    ) {
        fallarBeca(403, 'PERFIL_NO_VIGENTE', 'No tiene acceso vigente a un perfil de Estudiante.');
    }

    $usuario = (int) $usuarios[0]['id_usuario'];
    if (!$beca->usuarioEstudianteValido($usuario)) {
        fallarBeca(403, 'PERFIL_NO_VIGENTE', 'El perfil autenticado no corresponde a un Estudiante válido.');
    }

    return ['global' => false, 'usuario' => $usuario];
}

function enteroPositivoBeca(mixed $value, string $field): int
{
    if (!is_int($value) && !is_string($value)) {
        fallarBeca(400, 'CAMPO_INVALIDO', "El campo {$field} no es válido.");
    }
    $raw = (string) $value;
    if (preg_match('/^[1-9][0-9]*$/D', $raw) !== 1) {
        fallarBeca(400, 'CAMPO_INVALIDO', "El campo {$field} debe ser un entero positivo.");
    }
    $resultado = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($resultado === false) {
        fallarBeca(400, 'CAMPO_INVALIDO', "El campo {$field} está fuera de rango.");
    }
    return $resultado;
}

function tipoBeca(mixed $value): int
{
    if ($value === 'bec_int') {
        $value = '1';
    } elseif ($value === 'bec_ext') {
        $value = '2';
    }
    $tipo = enteroPositivoBeca($value, 'tipo_beca');
    if (!in_array($tipo, [1, 2], true)) {
        fallarBeca(422, 'TIPO_BECA_INVALIDO', 'El tipo de Beca debe ser Interna o Externa.');
    }
    return $tipo;
}

function nombreCatalogoBeca(mixed $value): string
{
    if (!is_string($value)) {
        fallarBeca(400, 'NOMBRE_INVALIDO', 'El nombre de la Beca no es válido.');
    }
    $nombre = trim($value);
    $longitud = function_exists('mb_strlen') ? mb_strlen($nombre, 'UTF-8') : strlen($nombre);
    if ($nombre === '' || $longitud > 80) {
        fallarBeca(422, 'NOMBRE_INVALIDO', 'El nombre debe contener entre 1 y 80 caracteres.');
    }
    return $nombre;
}

function fechaBeca(mixed $value, string $field): string
{
    $fecha = is_string($value) ? $value : '';
    $fechaValida = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errores = DateTimeImmutable::getLastErrors();
    if (
        $fechaValida === false
        || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))
        || $fechaValida->format('Y-m-d') !== $fecha
    ) {
        fallarBeca(422, 'FECHA_INVALIDA', "El campo {$field} debe contener una fecha civil válida.");
    }
    return $fecha;
}

function estadoCatalogoBeca(mixed $value): string
{
    if (!is_string($value) || !in_array($value, [
        Beca::ESTADO_APROBADA,
        Beca::ESTADO_PENDIENTE,
        Beca::ESTADO_INACTIVA,
    ], true)) {
        fallarBeca(422, 'ESTADO_INVALIDO', 'El estado de catálogo solicitado no está permitido.');
    }
    return $value;
}

function usuarioSolicitadoBeca(): ?int
{
    if (!array_key_exists('usuario', $_POST) || $_POST['usuario'] === '') {
        return null;
    }
    return enteroPositivoBeca($_POST['usuario'], 'usuario');
}

/** @param array{global:bool,usuario:int} $actor */
function usuarioObjetivoBeca(array $actor, Beca $beca): int
{
    $solicitado = usuarioSolicitadoBeca();
    if ($actor['global']) {
        if ($solicitado === null || !$beca->usuarioEstudianteValido($solicitado)) {
            fallarBeca(404, 'ESTUDIANTE_OBJETIVO_INVALIDO', 'El Estudiante objetivo no existe o no es válido.');
        }
        return $solicitado;
    }
    if ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarBeca(403, 'USUARIO_AJENO', 'No puede operar Becas para otro usuario.');
    }
    return $actor['usuario'];
}

/**
 * @param array{global:bool,usuario:int} $actor
 * @return array{fila:array<string,mixed>,propietario:int}
 */
function filaAutorizadaBeca(array $actor, Beca $beca, int $idBeca): array
{
    $fila = $beca->obtenerBeca($idBeca, $actor['global'] ? null : $actor['usuario']);
    if ($fila === null) {
        fallarBeca(404, 'BECA_NO_ENCONTRADA', 'La Beca indicada no existe.');
    }
    $propietario = isset($fila['alumno']) ? (int) $fila['alumno'] : 0;
    if ($propietario <= 0 || !$beca->usuarioEstudianteValido($propietario)) {
        fallarBeca(409, 'PROPIETARIO_INVALIDO', 'La Beca no pertenece a un Estudiante válido.');
    }
    $solicitado = usuarioSolicitadoBeca();
    if ($actor['global']) {
        if ($solicitado !== null && $solicitado !== $propietario) {
            fallarBeca(409, 'USUARIO_NO_COINCIDE', 'El usuario indicado no coincide con el propietario de la Beca.');
        }
    } elseif ($solicitado !== null && $solicitado !== $actor['usuario']) {
        fallarBeca(403, 'USUARIO_AJENO', 'No puede operar una Beca ajena.');
    }
    return ['fila' => $fila, 'propietario' => $propietario];
}

function exigirCsrfBeca(): void
{
    $tokenSesion = $_SESSION['csrf_beca'] ?? null;
    $tokenCliente = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (
        !is_string($tokenSesion)
        || !is_string($tokenCliente)
        || preg_match('/^[a-f0-9]{64}$/D', $tokenSesion) !== 1
        || !hash_equals($tokenSesion, $tokenCliente)
    ) {
        fallarBeca(403, 'CSRF_INVALIDO', 'La solicitud no contiene un token de seguridad válido.');
    }
}

/**
 * @param array{global:bool,usuario:int} $actor
 * @param array<string,mixed>|null $actual
 * @return array{tipo:int,institucion:int,fecha_inicio:string,fecha_termino:string,id_nombre_beca:?int,nuevo_nombre:?string}
 */
function datosBeca(Beca $beca, array $actor, ?array $actual = null): array
{
    $tipo = tipoBeca($_POST['tipo_beca'] ?? $_POST['tipo'] ?? null);
    $institucion = enteroPositivoBeca($_POST['institucion'] ?? $_POST['inst'] ?? null, 'institucion');
    $fechaInicio = fechaBeca($_POST['fecha_inicio'] ?? $_POST['fecha_in'] ?? null, 'fecha_inicio');
    $fechaTermino = fechaBeca($_POST['fecha_termino'] ?? $_POST['fecha_ter'] ?? null, 'fecha_termino');
    if ($fechaInicio > $fechaTermino) {
        fallarBeca(422, 'RANGO_FECHAS_INVALIDO', 'La fecha de inicio no puede ser posterior a la fecha de término.');
    }
    if (!$beca->institucionExiste($institucion)) {
        fallarBeca(404, 'INSTITUCION_NO_ENCONTRADA', 'La Institución indicada no existe.');
    }

    $tieneId = array_key_exists('id_nombre_beca', $_POST) && $_POST['id_nombre_beca'] !== '';
    $tieneNombre = array_key_exists('nuevo_nombre', $_POST) && trim((string) $_POST['nuevo_nombre']) !== '';
    if ($tieneId === $tieneNombre) {
        fallarBeca(422, 'CATALOGO_INVALIDO', 'Debe seleccionar un nombre o ingresar una propuesta nueva.');
    }
    if ($tieneNombre) {
        if ($actual !== null) {
            fallarBeca(422, 'PROPUESTA_EN_UPDATE_NO_PERMITIDA', 'Para editar debe seleccionar una entrada disponible del catálogo.');
        }
        if ($actor['global']) {
            fallarBeca(403, 'PROPUESTA_NO_PERMITIDA', 'Admin y Comité deben crear primero una entrada oficial en Editar listas.');
        }
        return [
            'tipo' => $tipo,
            'institucion' => $institucion,
            'fecha_inicio' => $fechaInicio,
            'fecha_termino' => $fechaTermino,
            'id_nombre_beca' => null,
            'nuevo_nombre' => nombreCatalogoBeca($_POST['nuevo_nombre']),
        ];
    }

    $idNombreBeca = enteroPositivoBeca($_POST['id_nombre_beca'], 'id_nombre_beca');
    $catalogo = $beca->obtenerNombreBeca($idNombreBeca);
    if ($catalogo === null) {
        fallarBeca(404, 'CATALOGO_NO_ENCONTRADO', 'El nombre de Beca indicado no existe.');
    }
    $esCatalogoActual = $actual !== null
        && isset($actual['nom_beca'])
        && (int) $actual['nom_beca'] === $idNombreBeca
        && (int) $catalogo['tipo_beca'] === $tipo;
    $proponente = $actor['global'] ? null : $actor['usuario'];
    if (!$esCatalogoActual && !$beca->catalogoSeleccionable($catalogo, $tipo, $proponente)) {
        fallarBeca(422, 'CATALOGO_NO_SELECCIONABLE', 'El nombre de Beca no está disponible para esta operación.');
    }
    return [
        'tipo' => $tipo,
        'institucion' => $institucion,
        'fecha_inicio' => $fechaInicio,
        'fecha_termino' => $fechaTermino,
        'id_nombre_beca' => $idNombreBeca,
        'nuevo_nombre' => null,
    ];
}

/** @param array{global:bool,usuario:int} $actor */
function exigirAdministracionCatalogo(array $actor): void
{
    if (!$actor['global']) {
        fallarBeca(403, 'CATALOGO_NO_AUTORIZADO', 'No tiene autorización para administrar el catálogo de Becas.');
    }
}

/** @param array<string,mixed> $fila @return array<string,mixed> */
function becaPublica(array $fila): array
{
    unset($fila['alumno'], $fila['propuesto_por']);
    return $fila;
}

$beca = new Beca();

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        fallarBeca(400, 'METODO_INVALIDO', 'La operación requiere una solicitud POST.');
    }
    $op = isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '';
    $op = ['read' => 'list', 'read-nombre' => 'options', 'read_lista' => 'catalog-list'][$op] ?? $op;
    if (!in_array($op, [
        'create', 'list', 'detail', 'update', 'delete', 'options',
        'catalog-list', 'catalog-create', 'catalog-update',
        'catalog-approve', 'catalog-unify', 'catalog-inactivate',
    ], true)) {
        fallarBeca(400, 'OPERACION_INVALIDA', 'La operación solicitada no es válida.');
    }

    $actor = actorBeca($beca);

    if ($op === 'list') {
        $usuario = usuarioObjetivoBeca($actor, $beca);
        responderBeca(200, ['ok' => true, 'codigo' => 'BECAS_OBTENIDAS', 'datos' => $beca->listarBecas($usuario), 'mensaje' => 'Becas obtenidas correctamente.']);
    }
    if ($op === 'detail') {
        $idBeca = enteroPositivoBeca($_POST['id_beca'] ?? $_POST['id'] ?? null, 'id_beca');
        $autorizada = filaAutorizadaBeca($actor, $beca, $idBeca);
        responderBeca(200, ['ok' => true, 'codigo' => 'BECA_OBTENIDA', 'datos' => becaPublica($autorizada['fila']), 'mensaje' => 'Beca obtenida correctamente.']);
    }
    if ($op === 'options') {
        $tipo = tipoBeca($_POST['tipo_beca'] ?? $_POST['tipo'] ?? null);
        responderBeca(200, [
            'ok' => true,
            'codigo' => 'OPCIONES_OBTENIDAS',
            'datos' => $beca->listarOpcionesCatalogo($tipo, $actor['global'] ? null : $actor['usuario']),
            'mensaje' => 'Opciones de Beca obtenidas correctamente.',
        ]);
    }
    if ($op === 'catalog-list') {
        exigirAdministracionCatalogo($actor);
        $tipo = null;
        if (array_key_exists('tipo_beca', $_POST) || array_key_exists('tipo', $_POST)) {
            $tipo = tipoBeca($_POST['tipo_beca'] ?? $_POST['tipo']);
        }
        $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? estadoCatalogoBeca($_POST['estado']) : null;
        responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_OBTENIDO', 'datos' => $beca->listarCatalogo($tipo, $estado), 'mensaje' => 'Catálogo de Becas obtenido correctamente.']);
    }

    exigirCsrfBeca();

    if ($op === 'create') {
        $usuario = usuarioObjetivoBeca($actor, $beca);
        $datos = datosBeca($beca, $actor);
        $resultado = $datos['nuevo_nombre'] !== null
            ? $beca->crearBecaConPropuesta($usuario, $datos['nuevo_nombre'], $datos['tipo'], $datos['institucion'], $datos['fecha_inicio'], $datos['fecha_termino'])
            : $beca->crearBeca($usuario, (int) $datos['id_nombre_beca'], $datos['institucion'], $datos['fecha_inicio'], $datos['fecha_termino']);
        $idInsertado = isset($resultado['idInsertado']) ? (int) $resultado['idInsertado'] : 0;
        if ((int) $resultado['filasAfectadas'] !== 1 || $idInsertado <= 0) {
            throw new RuntimeException('La creación no afectó exactamente una Beca.');
        }
        responderBeca(201, [
            'ok' => true,
            'codigo' => 'BECA_CREADA',
            'datos' => ['id_beca' => $idInsertado, 'id_nombre_beca' => $resultado['idNombreBeca'] ?? $datos['id_nombre_beca'], 'catalogo_creado' => $resultado['catalogoCreado'] ?? false],
            'mensaje' => 'Beca registrada correctamente.',
        ]);
    }
    if ($op === 'update') {
        $idBeca = enteroPositivoBeca($_POST['id_beca'] ?? $_POST['id'] ?? null, 'id_beca');
        $autorizada = filaAutorizadaBeca($actor, $beca, $idBeca);
        $fila = $autorizada['fila'];
        $datos = datosBeca($beca, $actor, $fila);
        $sinCambios = (int) $fila['nom_beca'] === (int) $datos['id_nombre_beca']
            && (int) $fila['inst_beca'] === $datos['institucion']
            && (string) $fila['fech_in'] === $datos['fecha_inicio']
            && (string) $fila['fech_ter'] === $datos['fecha_termino'];
        if ($sinCambios) {
            responderBeca(200, ['ok' => true, 'codigo' => 'BECA_SIN_CAMBIOS', 'datos' => ['id_beca' => $idBeca, 'cambios' => false], 'mensaje' => 'La Beca ya contiene esos datos.']);
        }
        $resultado = $beca->actualizarBeca($idBeca, $autorizada['propietario'], (int) $datos['id_nombre_beca'], $datos['institucion'], $datos['fecha_inicio'], $datos['fecha_termino']);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarBeca(409, 'BECA_NO_ACTUALIZADA', 'No fue posible confirmar la actualización de la Beca.');
        }
        responderBeca(200, ['ok' => true, 'codigo' => 'BECA_ACTUALIZADA', 'datos' => ['id_beca' => $idBeca, 'cambios' => true], 'mensaje' => 'Beca actualizada correctamente.']);
    }
    if ($op === 'delete') {
        $idBeca = enteroPositivoBeca($_POST['id_beca'] ?? $_POST['id'] ?? null, 'id_beca');
        $autorizada = filaAutorizadaBeca($actor, $beca, $idBeca);
        $resultado = $beca->eliminarBeca($idBeca, $autorizada['propietario']);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarBeca(409, 'BECA_NO_ELIMINADA', 'No fue posible confirmar la eliminación de la Beca.');
        }
        responderBeca(200, ['ok' => true, 'codigo' => 'BECA_ELIMINADA', 'datos' => ['id_beca' => $idBeca], 'mensaje' => 'Beca eliminada correctamente.']);
    }

    exigirAdministracionCatalogo($actor);

    if ($op === 'catalog-create') {
        $nombre = nombreCatalogoBeca($_POST['nombre'] ?? null);
        $tipo = tipoBeca($_POST['tipo_beca'] ?? $_POST['tipo'] ?? null);
        if ($beca->buscarCoincidenciasCatalogo($nombre, $tipo) !== []) {
            fallarBeca(409, 'CATALOGO_DUPLICADO', 'Ya existe una entrada equivalente para ese tipo de Beca.');
        }
        $resultado = $beca->crearCatalogoOficial($nombre, $tipo);
        $idInsertado = isset($resultado['idInsertado']) ? (int) $resultado['idInsertado'] : 0;
        if ((int) $resultado['filasAfectadas'] !== 1 || $idInsertado <= 0) {
            throw new RuntimeException('La creación no afectó exactamente una entrada de catálogo.');
        }
        responderBeca(201, ['ok' => true, 'codigo' => 'CATALOGO_CREADO', 'datos' => ['id_nom_beca' => $idInsertado], 'mensaje' => 'Nombre de Beca oficial creado correctamente.']);
    }

    $idNombreBeca = enteroPositivoBeca($_POST['id_nombre_beca'] ?? $_POST['id'] ?? null, 'id_nombre_beca');
    $catalogo = $beca->obtenerNombreBeca($idNombreBeca);
    if ($catalogo === null) {
        fallarBeca(404, 'CATALOGO_NO_ENCONTRADO', 'La entrada de catálogo indicada no existe.');
    }
    if ($op === 'catalog-update') {
        $nombre = nombreCatalogoBeca($_POST['nombre'] ?? null);
        $tipo = tipoBeca($_POST['tipo_beca'] ?? $_POST['tipo'] ?? null);
        if ($beca->buscarCoincidenciasCatalogo($nombre, $tipo, $idNombreBeca) !== []) {
            fallarBeca(409, 'CATALOGO_DUPLICADO', 'Ya existe otra entrada equivalente para ese tipo de Beca.');
        }
        if ((string) $catalogo['beca'] === $nombre && (int) $catalogo['tipo_beca'] === $tipo) {
            responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_SIN_CAMBIOS', 'datos' => ['id_nom_beca' => $idNombreBeca, 'cambios' => false], 'mensaje' => 'La entrada de catálogo ya contiene esos datos.']);
        }
        $resultado = $beca->actualizarCatalogo($idNombreBeca, $nombre, $tipo);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarBeca(409, 'CATALOGO_NO_ACTUALIZADO', 'No fue posible confirmar la actualización del catálogo.');
        }
        responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_ACTUALIZADO', 'datos' => ['id_nom_beca' => $idNombreBeca, 'referencias' => $beca->contarReferenciasCatalogo($idNombreBeca), 'cambios' => true], 'mensaje' => 'Entrada de catálogo actualizada correctamente.']);
    }
    if ($op === 'catalog-approve') {
        if ($catalogo['estado_catalogo'] !== Beca::ESTADO_PENDIENTE) {
            fallarBeca(409, 'TRANSICION_INVALIDA', 'Sólo una entrada pendiente puede aprobarse.');
        }
        $nombre = nombreCatalogoBeca($catalogo['beca']);
        $tipo = tipoBeca($catalogo['tipo_beca']);
        foreach ($beca->buscarCoincidenciasCatalogo($nombre, $tipo, $idNombreBeca) as $coincidencia) {
            if ($coincidencia['estado_catalogo'] === Beca::ESTADO_APROBADA) {
                fallarBeca(409, 'CATALOGO_DUPLICADO', 'Existe una entrada aprobada equivalente; debe unificar en lugar de aprobar.');
            }
        }
        $resultado = $beca->aprobarCatalogo($idNombreBeca);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarBeca(409, 'CATALOGO_NO_APROBADO', 'No fue posible confirmar la aprobación del catálogo.');
        }
        responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_APROBADO', 'datos' => ['id_nom_beca' => $idNombreBeca], 'mensaje' => 'Entrada de catálogo aprobada correctamente.']);
    }
    if ($op === 'catalog-inactivate') {
        if ($catalogo['estado_catalogo'] === Beca::ESTADO_INACTIVA) {
            responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_SIN_CAMBIOS', 'datos' => ['id_nom_beca' => $idNombreBeca, 'cambios' => false], 'mensaje' => 'La entrada de catálogo ya está inactiva.']);
        }
        $referencias = $beca->contarReferenciasCatalogo($idNombreBeca);
        $resultado = $beca->inactivarCatalogo($idNombreBeca);
        if ((int) $resultado['filasAfectadas'] !== 1) {
            fallarBeca(409, 'CATALOGO_NO_INACTIVADO', 'No fue posible confirmar la inactivación del catálogo.');
        }
        responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_INACTIVADO', 'datos' => ['id_nom_beca' => $idNombreBeca, 'referencias' => $referencias], 'mensaje' => 'Entrada de catálogo inactivada correctamente.']);
    }

    $destino = enteroPositivoBeca($_POST['destino'] ?? null, 'destino');
    $resultado = $beca->unificarCatalogo($idNombreBeca, $destino);
    responderBeca(200, ['ok' => true, 'codigo' => 'CATALOGO_UNIFICADO', 'datos' => $resultado, 'mensaje' => 'Entradas de catálogo unificadas correctamente.']);
} catch (BecaHttpError $error) {
    responderBeca($error->statusCode, ['ok' => false, 'error' => $error->errorCode, 'mensaje' => $error->getMessage()]);
} catch (DomainException|InvalidArgumentException|UnexpectedValueException $error) {
    responderBeca(409, ['ok' => false, 'error' => 'CONFLICTO_CATALOGO', 'mensaje' => $error->getMessage()]);
} catch (PDOException $error) {
    error_log('[BECA_PERSISTENCIA] ' . $error->getMessage());
    if ((string) $error->getCode() === '23000') {
        responderBeca(409, ['ok' => false, 'error' => 'INTEGRIDAD_BECA', 'mensaje' => 'La operación no cumple las restricciones de integridad vigentes.']);
    }
    responderBeca(500, ['ok' => false, 'error' => 'ERROR_TECNICO', 'mensaje' => 'No fue posible completar la operación de Beca.']);
} catch (Throwable $error) {
    error_log('[BECA_ENDPOINT] ' . $error->getMessage());
    responderBeca(500, ['ok' => false, 'error' => 'ERROR_TECNICO', 'mensaje' => 'No fue posible completar la operación de Beca.']);
}
