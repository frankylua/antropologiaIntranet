<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
session_start();
require_once __DIR__ . '/../src/bootstrap/app.php';
\App\Security\AcademicSession::enforce();
\App\Security\CrudCsrf::enforce('estudiante');
use App\Model\Docente;
use App\Model\Estudiante;
use App\Security\Authorization;
use App\Identity\Cardinality;
use App\Identity\IdentityResolver;
use App\Model\Institucion;

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

function actorCrudEstudiante(\PDO $pdo, bool $bloquear = false): array
{
    $login = idFichaEstudiante($_SESSION['login'] ?? null, 401);
    $sufijo = $bloquear ? ' FOR UPDATE' : '';
    $q = $pdo->prepare('SELECT id_login FROM login WHERE id_login=:login' . $sufijo);
    $q->execute(['login'=>$login]);
    if ($q->fetchColumn() === false) throw new \DomainException('NO_AUTENTICADO', 401);
    $q = $pdo->prepare('SELECT id_permiso FROM permiso_login WHERE id_login=:login' . $sufijo);
    $q->execute(['login'=>$login]);
    $permisos = array_map('intval',$q->fetchAll(\PDO::FETCH_COLUMN));
    $roles = [];
    foreach (['admin'=>1,'comite'=>2] as $rol=>$permiso) {
        $valor = $_SESSION[$rol] ?? null;
        $roles[$rol] = (is_int($valor) || is_string($valor)) && (string)$valor === (string)$login
            && in_array($permiso,$permisos,true);
    }
    return ['login'=>$login,'global'=>$roles['admin'] || $roles['comite']] + $roles;
}

function textoCrudEstudiante(string $campo, int $limite, string $defecto = ''): string
{
    $valor = $_POST[$campo] ?? $defecto;
    if (!is_string($valor)) throw new \DomainException('SOLICITUD_INVALIDA', 400);
    $valor = strtolower(htmlspecialchars(trim($valor), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false));
    if (preg_match('/^.{0,'.$limite.'}$/usD',$valor) !== 1) throw new \DomainException('DATOS_INVALIDOS', 422);
    return $valor;
}

function fechaCrudEstudiante(string $campo, bool $opcional = false): ?string
{
    $valor = $_POST[$campo] ?? '';
    if (!is_string($valor)) throw new \DomainException('SOLICITUD_INVALIDA', 400);
    if ($opcional && $valor === '') return null;
    if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D',$valor) !== 1) throw new \DomainException('DATOS_INVALIDOS', 422);
    $fecha = \DateTimeImmutable::createFromFormat('!Y-m-d',$valor);
    if (!$fecha || $fecha->format('Y-m-d') !== $valor) throw new \DomainException('DATOS_INVALIDOS', 422);
    return $valor;
}

function programaCrudEstudiante(): array
{
    $datos = [];
    $datos['nueva_institucion'] = array_key_exists('nueva_institucion',$_POST)
        ? textoCrudEstudiante('nueva_institucion',80) : null;
    if ($datos['nueva_institucion'] !== null) {
        if ($datos['nueva_institucion'] === '' || !in_array($_POST['inst'] ?? null,[0,'0'],true)) {
            throw new \DomainException('DATOS_INVALIDOS',422);
        }
        $datos['inst'] = 0;
    } else { $datos['inst'] = idFichaEstudiante($_POST['inst'] ?? null,422); }
    $datos['profesor'] = idFichaEstudiante($_POST['profesor'] ?? null,422);
    $promedio = $_POST['promedio'] ?? null;
    if ((!is_string($promedio) && !is_int($promedio) && !is_float($promedio)) || !is_numeric($promedio)
        || !is_finite((float)$promedio) || (float)$promedio < 1 || (float)$promedio > 7) {
        throw new \DomainException('DATOS_INVALIDOS',422);
    }
    $datos['promedio'] = (float)$promedio;
    $trabaja = $_POST['trabaja'] ?? null;
    if (!in_array($trabaja,[0,1,'0','1'],true)) throw new \DomainException('DATOS_INVALIDOS',422);
    $datos['trabaja'] = (int)$trabaja;
    $datos['orientacion'] = idFichaEstudiante($_POST['orientacion'] ?? null,422);
    if (!in_array($datos['orientacion'],[1,2],true)) throw new \DomainException('DATOS_INVALIDOS',422);
    $datos['sit_ocup'] = textoCrudEstudiante('sit_ocup',300);
    $datos['fech_ing'] = fechaCrudEstudiante('fech_ing');
    $datos['fech_grad'] = fechaCrudEstudiante('fech_grad',true);
    if ($datos['fech_grad'] !== null && $datos['fech_grad'] < $datos['fech_ing']) {
        throw new \DomainException('DATOS_INVALIDOS',422);
    }
    $lineas = $_POST['lineaInv'] ?? null;
    if (!is_array($lineas) || !$lineas || count($lineas) > 100) throw new \DomainException('LINEAS_INVALIDAS',422);
    $datos['lineaInv'] = [];
    foreach ($lineas as $linea) { $datos['lineaInv'][] = idFichaEstudiante($linea,422); }
    $datos['lineaInv'] = array_values(array_unique($datos['lineaInv']));
    return $datos;
}

function atenderCrudEstudiante(string $operacion): never
{
    $pdo = null;
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') throw new \DomainException('SOLICITUD_INVALIDA',405);
        $modelo = new Estudiante();
        $crear = $operacion === 'insert-update';
        $autoalta = $crear && !sesionAutenticadaEstudiante() && tokenAutoaltaEstudianteValido();
        if ($crear && !sesionAutenticadaEstudiante() && !$autoalta) throw new \DomainException('NO_AUTORIZADO',403);
        if ($autoalta && !in_array($_POST['acuerdos_muestra'] ?? null,[1,'1'],true)) {
            throw new \DomainException('ACEPTACION_REQUERIDA',422);
        }
        $pdo = conexion();
        $actor = $autoalta ? ['login'=>0,'admin'=>false,'comite'=>false,'global'=>false] : actorCrudEstudiante($pdo);
        $escritura = in_array($operacion,['insert-update','update-inf-prog','update-permiso-tipo-est','delete'],true);
        if (($crear && !$autoalta && !$actor['global'])
            || (!$crear && !in_array($operacion,['read_est_prog','read_nom_prof'],true) && !$actor['global'])
            || ($operacion === 'delete' && !$actor['admin'])) {
            throw new \DomainException('NO_AUTORIZADO',403);
        }
        if (!$crear && $escritura) {
            if (!$pdo->beginTransaction()) throw new \RuntimeException('TRANSACCION_NO_INICIADA');
            $actor = actorCrudEstudiante($pdo,true);
            if (!$actor['global'] || ($operacion === 'delete' && !$actor['admin'])) throw new \DomainException('NO_AUTORIZADO',403);
        }
        if (in_array($operacion,['read-est','read-filtrada','read-est-filtro'],true)) {
            $tipo = $_POST['tipo'] ?? '0';
            if (!in_array($tipo,[0,'0'],true)) {
                $tipo = idFichaEstudiante($tipo,422);
                if (!in_array($tipo,[1,2,3,4,5,6,7],true)) throw new \DomainException('DATOS_INVALIDOS',422);
            }
            responderFichaEstudiante(200,$modelo->listarEstudiantes((int)$tipo,textoCrudEstudiante('busqueda',120)));
        }
        if ($operacion === 'delete') {
            if (!in_array($_POST['confirmar'] ?? null,[1,'1'],true)) throw new \DomainException('CONFIRMACION_REQUERIDA',400);
            $modelo->inactivarRegistro(idFichaEstudiante($_POST['id_login'] ?? null),$actor['login']);
            if (!$pdo->commit()) throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
            responderFichaEstudiante(200,'Estudiante inactivado. Se conserva su historial; puede reactivarlo desde el filtro Inactivos y Datos del programa.');
        }
        if (!$crear) {
            $campo = $operacion === 'read_nom_prof' ? 'id_estudiante' : 'id_usu';
            $id = array_key_exists($campo,$_POST) ? idFichaEstudiante($_POST[$campo]) : null;
            if (!$actor['global']) {
                if (!Authorization::hasCapability('perfil.ver')) throw new \DomainException('FORBIDDEN',403);
                $identidad = (new IdentityResolver($pdo))->resolveByLoginId($actor['login']);
                if ($identidad->userCardinality() !== Cardinality::SINGLE || $identidad->studentCardinality() !== Cardinality::SINGLE
                    || $identidad->studentStatus() === null) throw new \DomainException('IDENTIDAD_INCOMPATIBLE',409);
                if ($id !== null && $id !== $identidad->userId()) throw new \DomainException('NO_AUTORIZADO',403);
                $id = $identidad->userId();
            }
            if ($id === null) throw new \DomainException('IDENTIFICADOR_INVALIDO',400);
            $objetivo = $modelo->resolverEstudiante($id,$escritura);
            if (!$actor['global'] && (int)$objetivo['login'] !== $actor['login']) throw new \DomainException('NO_AUTORIZADO',403);
            if (array_key_exists('id_login',$_POST) && idFichaEstudiante($_POST['id_login']) !== (int)$objetivo['login']) {
                throw new \DomainException('NO_AUTORIZADO',403);
            }
            if ($operacion === 'read_nom_prof') {
                $guia = $modelo->mostrarNomProf($id);
                if (count($guia) !== 1) throw new \DomainException('RELACION_GUIA_INCOMPATIBLE',409);
                responderFichaEstudiante(200,['ok'=>true,'datos'=>$guia]);
            }
            if ($operacion === 'read_est_prog') responderFichaEstudiante(200,$modelo->mostrarDatosProgEst($id));
            if ($operacion === 'update-permiso-tipo-est') {
                $tipo = idFichaEstudiante($_POST['tipo_est'] ?? null,422);
                if (!in_array($tipo,[1,2,3,4,5,6,7],true)) throw new \DomainException('DATOS_INVALIDOS',422);
                $modelo->editarTipoEst($id,$tipo);
                if (!$pdo->commit()) throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
                responderFichaEstudiante(200,$tipo===6 ? 'Estudiante inactivado. Se conserva su historial; puede reactivarlo desde el filtro Inactivos y Datos del programa.' : 'Estado actualizado. Si reactivó el perfil, vuelva a iniciar sesión para recuperar sus accesos.');
            }
            $programa = programaCrudEstudiante();
            $modelo->validarReferenciasPrograma($programa,(int)$objetivo['prof_guia']);
            $institucion = (new Institucion())->resolverId($programa['inst'],$programa['nueva_institucion']);
            $modelo->guardarPrograma($id,$programa,$institucion);
            $tipoPrograma=null;
            if (array_key_exists('tipo_est',$_POST)) {
                $tipoPrograma=idFichaEstudiante($_POST['tipo_est'],422);
                if (!in_array($tipoPrograma,[1,2,3,4,5,6,7],true)) throw new \DomainException('DATOS_INVALIDOS',422);
                $modelo->editarTipoEst($id,$tipoPrograma);
            }
            if (!$pdo->commit()) throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
            responderFichaEstudiante(200,$tipoPrograma===6 ? 'Estudiante inactivado. Se conserva su historial; puede reactivarlo desde el filtro Inactivos y Datos del programa.' : 'Los datos del programa y estado se guardaron correctamente.');
        }
        // CREATE mantiene el contrato de autoalta y determina permisos exclusivamente en servidor.
        $tipo = $autoalta ? 1 : idFichaEstudiante($_POST['tipo_est'] ?? null,422);
        if ($tipo===6) \App\Security\AcademicSession::requireMigration($pdo);
        if (!in_array($tipo,[1,2,3,4,5,6,7],true)) throw new \DomainException('DATOS_INVALIDOS',422);
        if (!$autoalta && array_key_exists('permisos',$_POST)) {
            if (!is_array($_POST['permisos'])) throw new \DomainException('SOLICITUD_INVALIDA',400);
            foreach ($_POST['permisos'] as $permiso) {
                if (!in_array(idFichaEstudiante($permiso),[3,5],true)) throw new \DomainException('ROLES_NO_AUTORIZADOS',403);
            }
        }
        $programa = programaCrudEstudiante();
        $base = array_fill_keys(['nombres','ap_pat','ap_mat','nro_doc','correo','fecha_nac','region','comuna','telefono','direccion','cont_em','tel_em'],'');
        $base += ['tipo_doc'=>0,'genero'=>0,'pais_nac'=>0,'pais_res'=>0,'pueblo'=>1];
        $persona = datosEdicionFichaEstudiante($pdo,$base);
        $persona['fecha_nac'] = fechaCrudEstudiante('fech_nac');
        $pass = $_POST['pass'] ?? null;
        if (!is_string($pass) || trim($pass) === '' || !\App\Security\PasswordCredentials::isValid($pass)
            || (array_key_exists('pass2',$_POST) && $_POST['pass2'] !== $pass)) {
            throw new \DomainException('DATOS_INVALIDOS',422);
        }
        if (!$pdo->beginTransaction()) throw new \RuntimeException('TRANSACCION_NO_INICIADA');
        if (!$autoalta && !actorCrudEstudiante($pdo,true)['global']) throw new \DomainException('NO_AUTORIZADO',403);
        $modelo->validarReferenciasPrograma($programa);
        $usuario = $modelo->crearRegistro($persona,$programa,$tipo,in_array($tipo,[2,3],true) ? [3,5] : [5],$pass);
        if (!$pdo->commit()) throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
        if ($autoalta) unset($_SESSION['autoalta_estudiante']);
        responderFichaEstudiante(200,['ok'=>true,'mensaje'=>'Usuario guardado correctamente','id_usuario'=>$usuario]);
    } catch (\Throwable $error) {
        if ($pdo !== null && $pdo->inTransaction()) {
            try { $pdo->rollBack(); } catch (\Throwable $rollback) { error_log('[ESTUDIANTE_CRUD_ROLLBACK] ' . get_class($rollback)); }
        }
        $estado = 500; $codigo = 'ERROR_INTERNO';
        if ($error instanceof \DomainException) {
            $estado = in_array($error->getCode(),[400,401,403,404,405,409,422],true) ? $error->getCode() : 422;
            $codigo = $error->getCode() ? $error->getMessage() : 'DATOS_INVALIDOS';
        } elseif ($error instanceof \InvalidArgumentException) { $estado=422; $codigo='DATOS_INVALIDOS'; }
        elseif ($error instanceof \PDOException && (string)$error->getCode() === '23000') { $estado=409; $codigo='DATOS_INCOMPATIBLES'; }
        if ($estado === 500) error_log('[ESTUDIANTE_CRUD] ' . get_class($error));
        $mensajes = ['ESTUDIANTE_CON_DEPENDENCIAS'=>'El Estudiante tiene antecedentes o relaciones que deben conservarse.',
            'INACTIVACION_NO_DISPONIBLE'=>'La inactivación aún no está habilitada. Solicite a administración que complete la actualización.',
            'ACEPTACION_REQUERIDA'=>'Debe aceptar los acuerdos y condiciones para completar el registro.',
            'CUENTA_CON_OTROS_ROLES'=>'La cuenta tiene otros roles y no puede eliminarse desde Estudiantes.',
            'GUIA_NO_ELEGIBLE'=>'Seleccione un Profesor aceptado para una nueva asignación.',
            'DATOS_INCOMPATIBLES'=>'Los datos son incompatibles con un registro existente.'];
        responderFichaEstudiante($estado,['ok'=>false,'error'=>$codigo,'mensaje'=>$mensajes[$codigo] ?? 'No fue posible completar la operación. Revise los datos y su autorización.']);
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

$op = $_POST['op'] ?? '';
if (!is_string($op)) responderFichaEstudiante(400,['ok'=>false,'error'=>'SOLICITUD_INVALIDA']);
if (in_array($op,['read-est','read-filtrada','read-est-filtro','read_est_prog','read_nom_prof',
    'insert-update','update-inf-prog','update-permiso-tipo-est','delete'],true)) {
    atenderCrudEstudiante($op);
}
try {
    switch ($op) {
        case 'contexto_autoalta':
            if (sesionAutenticadaEstudiante()) {
                if (!actorCrudEstudiante(conexion())['global']) throw new \DomainException('NO_AUTORIZADO',403);
                responderFichaEstudiante(200,['ok'=>true,'autoalta'=>false,'token'=>'']);
            }
            $contexto = contextoAutoaltaEstudianteVigente();
            if ($contexto === null) throw new \DomainException('CONTEXTO_AUTOALTA_INVALIDO',403);
            responderFichaEstudiante(200,['ok'=>true,'autoalta'=>true,'token'=>$contexto['token']]);
        case 'read_tipo':
            responderFichaEstudiante(200,(new Estudiante())->mostrarTipo());
        case 'read_prof':
            responderFichaEstudiante(410,['ok'=>false,'error'=>'OPERACION_RETIRADA',
                'mensaje'=>'Utilice el selector Profesor centralizado.']);
        case 'read_prof_autoalta':
            if (sesionAutenticadaEstudiante() || !tokenAutoaltaEstudianteValido()) throw new \DomainException('NO_AUTORIZADO',403);
            $busqueda = textoCrudEstudiante('busqueda',120);
            responderFichaEstudiante(200,$busqueda === '' ? [] : (new Docente())->buscarProf($busqueda));
        default:
            responderFichaEstudiante(400,['ok'=>false,'error'=>'SOLICITUD_INVALIDA']);
    }
} catch (\Throwable $error) {
    $estado = $error instanceof \DomainException ? (int)$error->getCode() : 500;
    if (!in_array($estado,[400,401,403,422],true)) $estado=500;
    if ($estado === 500) error_log('[ESTUDIANTE_SELECTOR] '.get_class($error));
    responderFichaEstudiante($estado,['ok'=>false,'error'=>$estado === 500 ? 'ERROR_INTERNO' : $error->getMessage()]);
}
