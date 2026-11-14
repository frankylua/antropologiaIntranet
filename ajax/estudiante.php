<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
session_start();
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Docente;
use App\Model\Estudiante;
use App\Security\Authorization;
use App\Identity\Cardinality;
use App\Identity\IdentityResolver;

const AUTOALTA_ESTUDIANTE_TTL = 1800;

function responderEstudiante(int $estado, array $respuesta): void
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function sesionAutenticadaEstudiante(): bool
{
    return isset($_SESSION['login'])
        || Authorization::hasAny(['admin', 'comite', 'docente', 'estudiante', 'aceptado']);
}

function contextoTemporalValMailVigente($contexto): bool
{
    if (!is_array($contexto) || !isset($contexto['token'], $contexto['emitido'])
        || !is_string($contexto['token']) || trim($contexto['token']) === '') {
        return false;
    }
    $emitido = $contexto['emitido'];
    if (!is_int($emitido) && (!is_string($emitido) || !ctype_digit($emitido))) {
        return false;
    }
    $emitido = filter_var($emitido, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $ahora = time();
    return $emitido !== false && $emitido <= $ahora && ($ahora - $emitido) <= 1800;
}

function autorizadoValMail(): bool
{
    $login = $_SESSION['login'] ?? null;
    $loginValido = (is_int($login) || (is_string($login) && ctype_digit($login)))
        && filter_var($login, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
    if ($loginValido && Authorization::hasAny(['admin', 'comite'])) {
        return true;
    }
    return !sesionAutenticadaEstudiante()
        && (contextoTemporalValMailVigente($_SESSION['autoalta_profesor'] ?? null)
            || contextoTemporalValMailVigente($_SESSION['autoalta_estudiante'] ?? null));
}

function contextoAutoaltaEstudianteVigente(): ?array
{
    $contexto = $_SESSION['autoalta_estudiante'] ?? null;
    if (
        !is_array($contexto)
        || !isset($contexto['token'], $contexto['emitido'])
        || !is_string($contexto['token'])
        || !is_scalar($contexto['emitido'])
    ) {
        return null;
    }

    $emitido = (int) $contexto['emitido'];
    $ahora = time();
    if ($emitido <= 0 || $emitido > $ahora || ($ahora - $emitido) > AUTOALTA_ESTUDIANTE_TTL) {
        unset($_SESSION['autoalta_estudiante']);
        return null;
    }
    return $contexto;
}

function tokenAutoaltaEstudianteValido(): bool
{
    $token = isset($_POST['autoalta_token']) && is_string($_POST['autoalta_token'])
        ? $_POST['autoalta_token']
        : '';
    $contexto = contextoAutoaltaEstudianteVigente();
    return $token !== ''
        && $contexto !== null
        && hash_equals($contexto['token'], $token);
}

function contextoAltaEstudiante(): string
{
    if (Authorization::hasAny(['admin', 'comite'])) {
        return 'administrativa';
    }
    if (!sesionAutenticadaEstudiante() && tokenAutoaltaEstudianteValido()) {
        return 'autoalta';
    }

    responderEstudiante(403, [
        'ok' => false,
        'error' => 'NO_AUTORIZADO',
        'mensaje' => 'No tiene autorización para crear un Estudiante.',
    ]);
}

function escrituraCreateEstudianteCompleta($resultado): bool
{
    return $resultado instanceof \PDOStatement && $resultado->rowCount() === 1;
}

function rollbackCreateEstudiante(?\PDO $conexion): void
{
    if ($conexion === null || !$conexion->inTransaction()) {
        return;
    }
    try {
        $conexion->rollBack();
    } catch (\Throwable $errorRollback) {
        error_log('[ESTUDIANTE_CREATE_ROLLBACK] ' . $errorRollback->getMessage());
    }
}

function responderFichaEstudiante(int $estado, array|string $respuesta): never
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function idFichaEstudiante($valor, int $estado = 400): int
{
    if ((!is_int($valor) && !is_string($valor))
        || preg_match('/^[1-9][0-9]*$/D', (string) $valor) !== 1
        || filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['max_range' => 2147483647]]) === false) {
        throw new \DomainException($estado === 401 ? 'NO_AUTENTICADO' : 'IDENTIFICADOR_INVALIDO', $estado);
    }
    return (int) $valor;
}

/** Identidad actual, leída con bloqueos durante UPDATE; nunca selecciona pass. */
function objetivoFichaEstudiante(\PDO $conexion, int $login, bool $bloquear): array
{
    $bloqueo = $bloquear ? ' FOR UPDATE' : '';
    $consulta = $conexion->prepare('SELECT id_login,correo FROM login WHERE id_login=:login' . $bloqueo);
    $consulta->execute(['login' => $login]);
    $cuentas = $consulta->fetchAll(\PDO::FETCH_ASSOC);
    $consulta = $conexion->prepare(
        'SELECT id_usuario,login,pueblo,pais_res,pais_nac,fecha_nac,nombres,ap_mat,ap_pat,'
        . 'tipo_doc,nro_doc,genero,region,comuna,telefono,direccion,cont_em,tel_em '
        . 'FROM usuario WHERE login=:login' . $bloqueo
    );
    $consulta->execute(['login' => $login]);
    $usuarios = $consulta->fetchAll(\PDO::FETCH_ASSOC);
    if (count($cuentas) !== 1 || count($usuarios) !== 1) {
        throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
    }
    $consulta = $conexion->prepare('SELECT id_est,tipo_est FROM estudiante WHERE usuario=:usuario' . $bloqueo);
    $consulta->execute(['usuario' => $usuarios[0]['id_usuario']]);
    $estudiantes = $consulta->fetchAll(\PDO::FETCH_ASSOC);
    if (count($estudiantes) !== 1 || !in_array((int) $estudiantes[0]['tipo_est'], [1, 2, 3, 4, 5, 6, 7], true)) {
        throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
    }
    return $usuarios[0] + ['correo' => $cuentas[0]['correo'], 'tipo_est' => (int) $estudiantes[0]['tipo_est']];
}

function datosEdicionFichaEstudiante(\PDO $conexion, array $actual): array
{
    $datos = $actual;
    // Campos ausentes se conservan en servidor; no se requiere un READ previo desde JS.
    $textos = ['nombres' => 45, 'ap_pat' => 45, 'ap_mat' => 45, 'nro_doc' => 45,
        'region' => 45, 'comuna' => 45, 'telefono' => 15, 'direccion' => 60,
        'cont_em' => 45, 'tel_em' => 15];
    foreach ($textos as $campo => $limite) {
        if (!array_key_exists($campo, $_POST)) {
            continue;
        }
        if (!is_string($_POST[$campo])) {
            throw new \DomainException('SOLICITUD_INVALIDA', 400);
        }
        $valor = strtolower(htmlspecialchars(trim($_POST[$campo]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false));
        if (preg_match('/^.{0,' . $limite . '}$/usD', $valor) !== 1) {
            throw new \DomainException('DATOS_INVALIDOS', 422);
        }
        $datos[$campo] = $valor;
    }
    foreach (['nombres', 'ap_pat', 'nro_doc'] as $campo) {
        if (trim((string) $datos[$campo]) === '') {
            throw new \DomainException('DATOS_INVALIDOS', 422);
        }
    }
    foreach (['documento' => 'tipo_doc', 'genero' => 'genero', 'pais_nac' => 'pais_nac',
        'pais_res' => 'pais_res', 'pueblo' => 'pueblo'] as $campo => $columna) {
        if (array_key_exists($campo, $_POST)) {
            $datos[$columna] = idFichaEstudiante($_POST[$campo], 422);
        }
    }
    if (!in_array((int) $datos['tipo_doc'], [1, 2], true)
        || !in_array((int) $datos['genero'], [1, 2, 3, 4], true)) {
        throw new \DomainException('DATOS_INVALIDOS', 422);
    }
    if (array_key_exists('fech_nac', $_POST)) {
        if (!is_string($_POST['fech_nac'])) {
            throw new \DomainException('SOLICITUD_INVALIDA', 400);
        }
        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $_POST['fech_nac']) !== 1) {
            throw new \DomainException('DATOS_INVALIDOS', 422);
        }
        $fecha = \DateTimeImmutable::createFromFormat('!Y-m-d', $_POST['fech_nac']);
        if (!$fecha || $fecha->format('Y-m-d') !== $_POST['fech_nac']) {
            throw new \DomainException('DATOS_INVALIDOS', 422);
        }
        $datos['fecha_nac'] = $_POST['fech_nac'];
    }
    foreach (['pais_nac' => ['pais', 'id_pais'], 'pais_res' => ['pais', 'id_pais'],
        'pueblo' => ['pueblo', 'id_pueblo']] as $campo => [$tabla, $clave]) {
        // Los identificadores SQL provienen exclusivamente de esta lista fija.
        if ($campo === 'pueblo' && $datos[$campo] === null && !array_key_exists($campo, $_POST)) {
            continue;
        }
        $consulta = $conexion->prepare("SELECT $clave FROM $tabla WHERE $clave=:id");
        $consulta->execute(['id' => $datos[$campo]]);
        if ($consulta->fetchColumn() === false) {
            throw new \DomainException('DATOS_INVALIDOS', 422);
        }
    }
    if (array_key_exists('correo', $_POST)) {
        if (!is_string($_POST['correo'])) {
            throw new \DomainException('SOLICITUD_INVALIDA', 400);
        }
        $datos['correo'] = strtolower(trim($_POST['correo']));
    }
    if (strlen($datos['correo']) > 45 || filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) === false) {
        throw new \DomainException('DATOS_INVALIDOS', 422);
    }
    return $datos;
}

function atenderFichaEstudiante(string $operacion): never
{
    $conexion = null;
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            throw new \DomainException('SOLICITUD_INVALIDA', 405);
        }
        $login = idFichaEstudiante($_SESSION['login'] ?? null, 401);
        $conexion = conexion();
        $identidad = (new IdentityResolver($conexion))->resolveByLoginId($login);
        if (!$identidad->loginFound()) {
            throw new \DomainException('NO_AUTENTICADO', 401);
        }
        $consulta = $conexion->prepare('SELECT id_permiso FROM permiso_login WHERE id_login=:login AND id_permiso IN (1,2)');
        $consulta->execute(['login' => $login]);
        $permisos = array_map('intval', $consulta->fetchAll(\PDO::FETCH_COLUMN));
        $rolAdmin = $_SESSION['admin'] ?? null;
        $rolComite = $_SESSION['comite'] ?? null;
        $administrativo = ((is_int($rolAdmin) || is_string($rolAdmin))
                && (string) $rolAdmin === (string) $login && in_array(1, $permisos, true))
            || ((is_int($rolComite) || is_string($rolComite))
                && (string) $rolComite === (string) $login && in_array(2, $permisos, true));
        $edicion = $operacion === 'update-inf-pers';
        if (!$edicion && !$administrativo && !Authorization::hasCapability('perfil.ver')) {
            throw new \DomainException('FORBIDDEN', 403);
        }
        if ($operacion === 'read_est_perfil' && $administrativo
            && ($identidad->userCardinality() === Cardinality::NONE
                || ($identidad->userCardinality() === Cardinality::SINGLE
                    && $identidad->studentCardinality() === Cardinality::NONE))) {
            responderFichaEstudiante(200, []);
        }
        $idObjetivo = $operacion === 'read_est_perfil' ? $identidad->userId()
            : (array_key_exists('id_usu', $_POST) ? idFichaEstudiante($_POST['id_usu']) : $identidad->userId());
        if (!$administrativo) {
            if ($identidad->userCardinality() !== Cardinality::SINGLE
                || $identidad->studentCardinality() !== Cardinality::SINGLE || $identidad->studentStatus() === null) {
                throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
            }
            if ($idObjetivo !== $identidad->userId()) {
                throw new \DomainException('NO_AUTORIZADO', 403);
            }
        }
        if ($idObjetivo === null) {
            throw new \DomainException('IDENTIFICADOR_INVALIDO', 400);
        }
        if ($edicion) {
            foreach (['pass', 'pass2'] as $campo) {
                if (array_key_exists($campo, $_POST) && $_POST[$campo] !== '') {
                    throw new \DomainException('CAMBIO_PASSWORD_NO_AUTORIZADO', 403);
                }
            }
            if (!$conexion->beginTransaction()) {
                throw new \RuntimeException('TRANSACCION_NO_INICIADA');
            }
        }
        $consulta = $conexion->prepare('SELECT login FROM usuario WHERE id_usuario=:usuario');
        $consulta->execute(['usuario' => $idObjetivo]);
        $loginObjetivo = $consulta->fetchColumn();
        if ($loginObjetivo === false) {
            throw new \DomainException('ESTUDIANTE_NO_ENCONTRADO', 404);
        }
        $actual = objetivoFichaEstudiante($conexion, (int) $loginObjetivo, $edicion);
        if ((int) $actual['id_usuario'] !== $idObjetivo || (!$administrativo && (int) $actual['login'] !== $login)) {
            throw new \DomainException('IDENTIDAD_INCOMPATIBLE', 409);
        }
        if (array_key_exists('id_login', $_POST) && idFichaEstudiante($_POST['id_login']) !== (int) $actual['login']) {
            throw new \DomainException('NO_AUTORIZADO', 403);
        }
        $estudiante = new Estudiante();
        if (!$edicion) {
            responderFichaEstudiante(200, $estudiante->mostrarEstId($idObjetivo));
        }
        $propio = (int) $actual['login'] === $login;
        if ($propio && !in_array($actual['tipo_est'], [1, 2, 3, 4, 5, 7], true)) {
            throw new \DomainException('NO_AUTORIZADO', 403);
        }
        $datos = datosEdicionFichaEstudiante($conexion, $actual);
        if (!$propio && $datos['correo'] !== $actual['correo']) {
            throw new \DomainException('CREDENCIALES_AJENAS_NO_AUTORIZADAS', 403);
        }
        $resultado = $estudiante->editarInfPers(
            $datos['nombres'], $datos['ap_mat'], $datos['ap_pat'], $datos['fecha_nac'],
            $datos['tipo_doc'], $datos['nro_doc'], $datos['pais_nac'], $datos['genero'],
            $datos['pais_res'], $datos['region'], $datos['comuna'], $datos['telefono'],
            $datos['direccion'], $datos['cont_em'], $datos['tel_em'], $datos['correo'], null,
            $datos['pueblo'], $idObjetivo, (int) $actual['login']
        );
        if (!($resultado instanceof \PDOStatement)
            || ($propio && !($estudiante->editarLogin($login, $datos['correo']) instanceof \PDOStatement))) {
            throw new \RuntimeException('ESCRITURA_NO_CONFIRMADA');
        }
        if (!$conexion->commit()) {
            throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
        }
        responderFichaEstudiante(200, 'Datos Editados Correctamente');
    } catch (\Throwable $error) {
        if ($conexion !== null && $conexion->inTransaction()) {
            try {
                $conexion->rollBack();
            } catch (\Throwable $errorRollback) {
                error_log('[ESTUDIANTE_UPDATE_ROLLBACK] ' . get_class($errorRollback));
            }
        }
        if ($error instanceof \DomainException) {
            responderFichaEstudiante($error->getCode(), ['ok' => false, 'error' => $error->getMessage()]);
        }
        error_log('[ESTUDIANTE_FICHA] ' . get_class($error));
        $conflicto = $error instanceof \PDOException && (string) $error->getCode() === '23000';
        responderFichaEstudiante($conflicto ? 409 : 500,
            ['ok' => false, 'error' => $conflicto ? 'DATOS_INCOMPATIBLES' : 'ERROR_INTERNO']);
    }
}

if (($_POST['op'] ?? null) === 'val-mail') {
    if (!isset($_POST['correo']) || !is_scalar($_POST['correo']) || is_bool($_POST['correo'])
        || trim((string) $_POST['correo']) === '') {
        responderEstudiante(400, ['ok' => false, 'error' => 'SOLICITUD_INVALIDA']);
    }
    if (!autorizadoValMail()) {
        responderEstudiante(403, ['ok' => false, 'error' => 'NO_AUTORIZADO']);
    }
    $correoValMail = strtolower(htmlspecialchars(stripslashes(trim((string) $_POST['correo']))));
    try {
        $ocupado = (new Estudiante())->validarCorreo($correoValMail);
        responderEstudiante(200, $ocupado ? [true] : []);
    } catch (\Throwable $error) {
        error_log('[ESTUDIANTE_VAL_MAIL] ' . $error->getMessage());
        responderEstudiante(500, ['ok' => false, 'error' => 'ERROR_INTERNO']);
    }
}

if (in_array($_POST['op'] ?? null, ['read_est_id', 'read_est_perfil', 'update-inf-pers'], true)) {
    atenderFichaEstudiante($_POST['op']);
}

require 'usuario.php';
$est = new Estudiante();
$promedio = isset($_POST['promedio']) ? floatval($_POST['promedio']) : '';
$trabaja = isset($_POST['trabaja']) ? (int) $_POST['trabaja'] : '';
$orientacion = isset($_POST['orientacion']) ? limpiar_datos($_POST['orientacion']) : "";
$sit_ocup = isset($_POST['sit_ocup']) ? limpiar_datos($_POST['sit_ocup']) : "";
$profesor = isset($_POST['profesor']) ? (int) $_POST['profesor'] : '';
$fech_ing = isset($_POST['fech_ing']) ? limpiar_datos($_POST['fech_ing']) : "";
$fech_grad = isset($_POST['fech_grad']) ? limpiar_datos($_POST['fech_grad']) : "";
$tipo_est = isset($_POST['tipo_est']) ? (int) $_POST['tipo_est'] : '';
$op = isset($_POST['op']) ? $_POST['op'] : '';
$lineaInv = isset($_POST['lineaInv']) ? $_POST['lineaInv'] : '';
$id_estudiante_objetivo = isset($_POST['id_estudiante']) && is_scalar($_POST['id_estudiante'])
    ? filter_var($_POST['id_estudiante'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;
$existe_est = isset ($_SESSION['id_usuario']) ?$_SESSION['id_usuario'] : 0;
$id_est=$existe_est!=0?$existe_est[0]['id_usuario']:0;

//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
switch ($op) {
    case 'contexto_autoalta':
        if (Authorization::hasAny(['admin', 'comite'])) {
            responderEstudiante(200, ['ok' => true, 'autoalta' => false, 'token' => '']);
        }
        $contextoAutoalta = contextoAutoaltaEstudianteVigente();
        if (sesionAutenticadaEstudiante() || $contextoAutoalta === null) {
            responderEstudiante(403, [
                'ok' => false,
                'error' => 'CONTEXTO_AUTOALTA_INVALIDO',
                'mensaje' => 'El contexto de autoalta no es válido.',
            ]);
        }
        responderEstudiante(200, [
            'ok' => true,
            'autoalta' => true,
            'token' => $contextoAutoalta['token'],
        ]);
    case 'read_tipo':
        $resp = $est->mostrarTipo();

        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof':
        responderEstudiante(410, [
            'ok' => false,
            'error' => 'OPERACION_RETIRADA',
            'mensaje' => 'Utilice el selector Profesor centralizado.',
        ]);
    case 'read_prof_autoalta':
        if (sesionAutenticadaEstudiante() || !tokenAutoaltaEstudianteValido()) {
            responderEstudiante(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'El contexto de autoalta no es válido.',
            ]);
        }
        $busquedaProfesor = isset($_POST['busqueda']) && is_scalar($_POST['busqueda'])
            ? trim(limpiar_datos((string) $_POST['busqueda']))
            : '';
        $selectorDocente = new Docente();
        echo json_encode(
            $busquedaProfesor === '' ? [] : $selectorDocente->buscarProf($busquedaProfesor),
            JSON_UNESCAPED_UNICODE
        );
        break;
    case 'read_nom_prof':
        if (Authorization::hasAny(['admin', 'comite']) && $id_estudiante_objetivo !== false) {
            $idEstudianteConsulta = (int) $id_estudiante_objetivo;
        } elseif (Authorization::hasAny(['estudiante']) && $id_est > 0) {
            $idEstudianteConsulta = (int) $id_est;
        } elseif (Authorization::hasAny(['admin', 'comite'])) {
            responderEstudiante(400, [
                'ok' => false,
                'error' => 'IDENTIFICADOR_INVALIDO',
                'mensaje' => 'Debe indicar un Estudiante válido.',
            ]);
        } else {
            responderEstudiante(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'No tiene autorización para consultar el profesor guía.',
            ]);
        }

        $resp = $est->mostrarNomProf($idEstudianteConsulta);
        if (count($resp) !== 1) {
            responderEstudiante(409, [
                'ok' => false,
                'error' => 'RELACION_GUIA_INCOMPATIBLE',
                'mensaje' => 'No fue posible resolver un profesor guía válido para el Estudiante.',
            ]);
        }
        responderEstudiante(200, ['ok' => true, 'datos' => $resp]);
    case 'read_est_prog':
        $resp = $est->mostrarDatosProgEst($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;

    case 'read-est':
        $resp = $est->mostrarEst();
        // header('Content-Type: application/json');
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-filtrada':
        $resp = $est->mostrarEstTipo($tipo);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-est-filtro':
        $resp = $est->mostrarEstFiltro($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    // case 'read_est_prog':
    //     $resp = $est->mostrarDatosProgEst($id_usu);
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;

    case 'insert-update':
        $contextoAlta = contextoAltaEstudiante();
        $esAutoalta = $contextoAlta === 'autoalta';
        if ($esAutoalta) {
            $tipo_est = 1;
            $permiso = [5];
        }
        $tipoEstValido = $esAutoalta || (
            isset($_POST['tipo_est'])
            && is_scalar($_POST['tipo_est'])
            && ctype_digit((string) $_POST['tipo_est'])
            && in_array((int) $_POST['tipo_est'], [1, 2, 3, 4, 5, 6, 7], true)
        );
        if (!$tipoEstValido) {
            responderEstudiante(422, [
                'ok' => false,
                'mensaje' => 'Tipo de estudiante inválido.',
            ]);
        }
        if (!is_array($permiso) || $permiso === [] || !is_array($lineaInv) || $lineaInv === []) {
            responderEstudiante(422, [
                'ok' => false,
                'mensaje' => 'Los datos del estudiante son incompatibles.',
            ]);
        }
        if ($esAutoalta) {
            unset($_SESSION['autoalta_estudiante']);
        }
        $conexionCreateEstudiante = null;
        try {
            $conexionCreateEstudiante = conexion();
            if ($conexionCreateEstudiante->beginTransaction() !== true) {
                throw new \RuntimeException('No fue posible iniciar la transacción de creación del estudiante.');
            }
            $login = $est->insertarLogin($correo, $pass);
            if (filter_var($login, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new \RuntimeException('No fue posible confirmar la creación del login.');
            }
            foreach ($permiso as $per) {
                $resultadoPermiso = $est->insertarPermisos($login, (int) $per);
                if (!escrituraCreateEstudianteCompleta($resultadoPermiso)) {
                    throw new \RuntimeException('No fue posible confirmar la asignación de permisos.');
                }
            }
            $usuario = $est->insertarUsuario($pueblo, $pais_res, $pais_nac, $login, $fech_nac, $nombres, $ap_mat, $ap_pat, $documento, $nro_doc, $inst, $genero, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em);
            if (filter_var($usuario, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
                throw new \RuntimeException('No fue posible confirmar la creación del usuario.');
            }
            $resultadoEstudiante = $est->insertar($profesor, $tipo_est, $fech_ing, $fech_grad, $promedio, $trabaja, $orientacion, $sit_ocup, $usuario);
            if (!escrituraCreateEstudianteCompleta($resultadoEstudiante)) {
                throw new \RuntimeException('No fue posible confirmar la creación del estudiante.');
            }
            foreach ($lineaInv as $linea) {
                $resultadoLinea = $est->insertarLineaInv($usuario, $linea);
                if (!escrituraCreateEstudianteCompleta($resultadoLinea)) {
                    throw new \RuntimeException('No fue posible confirmar una línea de investigación.');
                }
            }
            if ($conexionCreateEstudiante->commit() !== true) {
                throw new \RuntimeException('No fue posible confirmar la transacción de creación del estudiante.');
            }
            responderEstudiante(200, [
                'ok' => true,
                'mensaje' => '<p>Usuario guardado correctamente</p>',
                'id_usuario' => (int) $usuario,
            ]);
        } catch (\PDOException $error) {
            rollbackCreateEstudiante($conexionCreateEstudiante);
            error_log('[ESTUDIANTE_CREATE] ' . $error->getMessage());
            $estado = (string) $error->getCode() === '23000' ? 409 : 500;
            responderEstudiante($estado, [
                'ok' => false,
                'mensaje' => $estado === 409
                    ? 'Los datos del estudiante son incompatibles con un registro existente.'
                    : 'No fue posible guardar el estudiante.',
            ]);
        } catch (\Throwable $error) {
            rollbackCreateEstudiante($conexionCreateEstudiante);
            error_log('[ESTUDIANTE_CREATE] ' . $error->getMessage());
            responderEstudiante(500, [
                'ok' => false,
                'mensaje' => 'No fue posible guardar el estudiante.',
            ]);
        }
    case 'update-inf-prog':
        //editar Permisos
        // if (isset($_SESSION['admin']) || isset($_SESSION['comite'])) {
        //     // editar permisos
        //     foreach ($permiso as $p) {
        //         // logica para buscar el permiso y editar en caso que sea comite o admin
        //         if ($p == 3) {
        //             $acep = $est->buscarPermiso(3, $id_login);
        //             if (!(array) $acep) {
        //                 $est->insertarPermisos($id_login, $p);    
        //             }
        //         }
        //         if ($p == 5) {
        //             // $acep = $est->buscarPermiso(5, $id_login);
        //             // if (!(array) $acep) {
        //                 $est->insertarPermisos($id_login, $p);    
        //             //}
        //         }
        //     }
        //     if (!in_array(3, $permiso)) {
        //         $elim = $est->eliminarPermiso(1, $id_login);
        //     }
        //     if (!in_array(5, $permiso)) {
        //         $est->eliminarPermiso(2, $id_login);
        //     }
        // }
        //editar Linea Inv
        //validar las lineas que no existen en la base de datoa
        foreach ($lineaInv as $linea) {
            //buscar si linea esta guardada
            $existLinea = (array) $est->buscarLinInv($linea, $id_usu);
            $existLinea = $existLinea ? true : false;
            if (!$existLinea) {
                $respuesta = $est->insertarLineaInv($id_usu, $linea);
            }
            //array_push($mensaje,$existLinea);
        }
        $lineasDB = $est->todasLinInv($id_usu);
        //validar linea que existen en la base de dato, pero deben ser eliminadas
        foreach ($lineasDB as $lin) {
            if (!in_array($lin['linea_inv'], $lineaInv)) {
                $est->borrarLineaInv($id_usu, $lin['linea_inv']);
                //array_push($mensaje,'no existe en el nuevo array');
            }
        }
        //editar datos programa
        $resp_est = $est->editarDatProg($id_usu, $profesor,$fech_ing, $fech_grad, $promedio,$trabaja, $orientacion, $sit_ocup );
        $resp_inst = $est->editarInstDoc($id_usu, $inst);
        $mensaje = $resp_est && $resp_inst ? 'Los Datos han sido Editados' : 'Los Datos no han sido Editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'delete':
        $respuesta = $est->eliminar($id_login);
        $respuesta ? $mensaje = "<p>Usuario Eliminado</p>" : $mensaje = "<p>Usuario no ha sido eliminado</p>";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        
    case 'update-permiso-tipo-est':
        $resp;
        $idUsuValido = isset($_POST['id_usu'])
            && is_scalar($_POST['id_usu'])
            && !is_bool($_POST['id_usu'])
            && !is_float($_POST['id_usu'])
            && ctype_digit((string) $_POST['id_usu'])
            && (int) $_POST['id_usu'] > 0;
        $tipoEstValido = isset($_POST['tipo_est'])
            && is_scalar($_POST['tipo_est'])
            && !is_bool($_POST['tipo_est'])
            && !is_float($_POST['tipo_est'])
            && ctype_digit((string) $_POST['tipo_est'])
            && in_array((int) $_POST['tipo_est'], [1, 2, 3, 4, 5, 6, 7], true);
        if (!$idUsuValido || !$tipoEstValido) {
            echo json_encode('Los datos no han podido ser acualizados', JSON_UNESCAPED_UNICODE);
            break;
        }
        $id_usu = (int) $_POST['id_usu'];
        $tipo_est = (int) $_POST['tipo_est'];
        $resp=$est->editarTipoEst($id_usu,$tipo_est);
        $resp? $mensaje = 'Los datos han sido  actualizados': $mensaje = 'Los datos no han podido ser acualizados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;

}
?>
