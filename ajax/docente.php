<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Docente;
use App\Security\Authorization;

const AUTOALTA_PROFESOR_TTL = 1800;

function responderDocente(int $estado, array $respuesta): void
{
    http_response_code($estado);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

function exigirAdminDocente(): void
{
    if (!Authorization::hasAny(['admin'])) {
        responderDocente(403, [
            'ok' => false,
            'error' => 'NO_AUTORIZADO',
            'mensaje' => 'No tiene autorización para gestionar esta operación Docente.',
        ]);
    }
}

function loginActorDocente(): int
{
    $candidatos = [];
    foreach (['login', 'admin', 'comite', 'docente'] as $clave) {
        if (isset($_SESSION[$clave]) && is_scalar($_SESSION[$clave])) {
            $idLogin = (int) $_SESSION[$clave];
            if ($idLogin > 0) {
                $candidatos[] = $idLogin;
            }
        }
    }

    $candidatos = array_values(array_unique($candidatos));
    return count($candidatos) === 1 ? $candidatos[0] : 0;
}

function exigirAdminComiteDocente(): void
{
    if (!Authorization::hasAny(['admin', 'comite'])) {
        responderDocente(403, [
            'ok' => false,
            'error' => 'NO_AUTORIZADO',
            'mensaje' => 'No tiene autorización para gestionar esta operación Docente.',
        ]);
    }
}

function sesionAutenticadaProfesor(): bool
{
    foreach (['login', 'admin', 'comite', 'docente', 'estudiante', 'aceptado'] as $clave) {
        if (isset($_SESSION[$clave])) {
            return true;
        }
    }
    return false;
}

function contextoAutoaltaProfesorVigente(): bool
{
    if (
        !isset($_SESSION['autoalta_profesor'])
        || !is_array($_SESSION['autoalta_profesor'])
        || !isset($_SESSION['autoalta_profesor']['token'], $_SESSION['autoalta_profesor']['emitido'])
        || !is_string($_SESSION['autoalta_profesor']['token'])
        || !is_scalar($_SESSION['autoalta_profesor']['emitido'])
    ) {
        return false;
    }
    $emitido = (int) $_SESSION['autoalta_profesor']['emitido'];
    return $emitido > 0 && (time() - $emitido) <= AUTOALTA_PROFESOR_TTL;
}

function tokenAutoaltaProfesorValido(): bool
{
    if (!contextoAutoaltaProfesorVigente()) {
        return false;
    }
    $tokenRecibido = isset($_POST['token_autoalta']) && is_string($_POST['token_autoalta'])
        ? $_POST['token_autoalta']
        : '';
    return $tokenRecibido !== ''
        && hash_equals($_SESSION['autoalta_profesor']['token'], $tokenRecibido);
}

function contextoAltaProfesor(): string
{
    if (Authorization::hasAny(['admin', 'comite'])) {
        return 'administrativa';
    }
    if (!sesionAutenticadaProfesor() && tokenAutoaltaProfesorValido()) {
        return 'autoalta';
    }
    responderDocente(403, [
        'ok' => false,
        'error' => 'NO_AUTORIZADO',
        'mensaje' => 'No existe un contexto autorizado para crear el perfil Docente.',
    ]);
}

function estadoFiltroProfesor(): int
{
    if (!array_key_exists('estado', $_POST)) {
        return 0;
    }
    if (!is_scalar($_POST['estado']) || !ctype_digit((string) $_POST['estado'])) {
        responderDocente(400, [
            'ok' => false,
            'error' => 'ESTADO_FILTRO_INVALIDO',
            'mensaje' => 'El filtro de estado no es valido.',
        ]);
    }
    $estado = (int) $_POST['estado'];
    if (!in_array($estado, [0, 1, 2, 3], true)) {
        responderDocente(400, [
            'ok' => false,
            'error' => 'ESTADO_FILTRO_INVALIDO',
            'mensaje' => 'El filtro de estado no es valido.',
        ]);
    }
    return $estado;
}

function usuarioSesionProfesor(): int
{
    if (
        !Authorization::hasAny(['docente'])
        || !isset($_SESSION['id_usuario'][0]['id_usuario'])
        || !is_scalar($_SESSION['id_usuario'][0]['id_usuario'])
    ) {
        return 0;
    }
    $idUsuario = (int) $_SESSION['id_usuario'][0]['id_usuario'];
    return $idUsuario > 0 ? $idUsuario : 0;
}

function resolverObjetivoEdicionDocente(
    Docente $docente,
    int $actorLogin,
    int $idUsuarioCliente,
    int $idLoginCliente,
    bool $perfilPropio
): array {
    if ($perfilPropio) {
        $idUsuario = usuarioSesionProfesor();
        $idLogin = $actorLogin;
        if ($idUsuario < 1 || $idLogin < 1) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'No existe un perfil Docente válido en la sesión.',
            ]);
        }
    } elseif (Authorization::hasAny(['admin', 'comite'])) {
        $idUsuario = $idUsuarioCliente;
        $idLogin = $idLoginCliente;
    } else {
        responderDocente(403, [
            'ok' => false,
            'error' => 'NO_AUTORIZADO',
            'mensaje' => 'No tiene autorización para editar información Docente.',
        ]);
    }

    $identidad = $docente->obtenerIdentidadProfesorPorUsuario($idUsuario);
    if ($identidad === null || (int) $identidad['id_login'] !== $idLogin) {
        responderDocente(409, [
            'ok' => false,
            'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
            'mensaje' => 'No fue posible validar la identidad del Profesor.',
        ]);
    }
    return ['id_usuario' => $idUsuario, 'id_login' => $idLogin];
}

$operacionSolicitada = isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '';
$idLoginEliminar = null;
if ($operacionSolicitada === 'delete') {
    if (!Authorization::hasAny(['admin'])) {
        responderDocente(403, [
            'ok' => false,
            'error' => 'NO_AUTORIZADO',
            'mensaje' => 'Sólo un administrador puede eliminar un Profesor.',
        ]);
    }
    $idLoginValidado = isset($_POST['id_login']) && is_scalar($_POST['id_login'])
        ? filter_var($_POST['id_login'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
        : false;
    if ($idLoginValidado === false) {
        responderDocente(400, [
            'ok' => false,
            'error' => 'IDENTIFICADOR_INVALIDO',
            'mensaje' => 'El identificador indicado no es válido.',
        ]);
    }
    $idLoginEliminar = (int) $idLoginValidado;
}

require 'usuario.php';

$doc = new Docente();
$busqueda = isset($_POST['busqueda']) && is_scalar($_POST['busqueda']) ? (string) $_POST['busqueda'] : '';
$inst = isset ($_POST['instTrab']) && is_scalar($_POST['instTrab']) ? (int) $_POST['instTrab'] : 0;
$nuevaInstitucion = isset($_POST['nueva_institucion']) && is_string($_POST['nueva_institucion'])
    ? trim(limpiar_datos($_POST['nueva_institucion']))
    : '';
$catAcad = isset ($_POST['catAcad']) ? (int) $_POST['catAcad'] : '';
$anioIng = isset ($_POST['anio_ing']) ? (int) $_POST['anio_ing'] : '';
$vinculo = isset ($_POST['vinculo']) ? (int) $_POST['vinculo'] : '';
$op = $operacionSolicitada;
$lineaInv = isset($_POST['lineaInv']) && is_array($_POST['lineaInv']) ? $_POST['lineaInv'] : [];
$id_prof = usuarioSesionProfesor();
$actorLogin = loginActorDocente();
header('X-Puede-Eliminar-Docente: ' . (Authorization::hasAny(['admin']) ? '1' : '0'));
$excluirLoginListado = $actorLogin > 0
    && Authorization::hasAny(['admin', 'comite'])
        ? $actorLogin
        : 0;
//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
switch ($op) {
    case 'contexto_autoalta':
        if (Authorization::hasAny(['admin', 'comite'])) {
            responderDocente(200, ['ok' => true, 'autoalta' => false, 'token' => '']);
        }
        if (sesionAutenticadaProfesor() || !contextoAutoaltaProfesorVigente()) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'CONTEXTO_AUTOALTA_INVALIDO',
                'mensaje' => 'El contexto de registro Docente no es valido.',
            ]);
        }
        responderDocente(200, [
            'ok' => true,
            'autoalta' => true,
            'token' => $_SESSION['autoalta_profesor']['token'],
        ]);
    case 'read_prof':
        if ($actorLogin < 1) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'Debe iniciar sesión para consultar Profesores.',
            ]);
        }
        if (Authorization::hasAny(['docente']) && !Authorization::hasCapability('docente.habilitado')) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'DOCENTE_NO_HABILITADO',
                'mensaje' => 'El estado actual del Profesor no permite utilizar este selector.',
            ]);
        }
        $resp = $doc->buscarProf($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_id':
        exigirAdminComiteDocente();
        if ((int) $id_usu < 1) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'IDENTIFICADOR_INVALIDO',
                'mensaje' => 'El Profesor indicado no es válido.',
            ]);
        }
        $identidadProfesor = $doc->obtenerIdentidadProfesorPorUsuario((int) $id_usu);
        if ($identidadProfesor === null) {
            responderDocente(409, [
                'ok' => false,
                'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                'mensaje' => 'El objetivo no representa un Profesor válido.',
            ]);
        }
        $resp = $doc->mostrarProfId($id_usu);
        responderDocente(200, [
            'ok' => true,
            'datos' => $resp,
            'puede_gestionar_rol' => Authorization::hasAny(['admin'])
                && $actorLogin !== (int) $identidadProfesor['id_login']
                && (int) $identidadProfesor['estado_profesor'] === 2,
            'rol_administrativo' => Authorization::hasAny(['admin'])
                ? $doc->obtenerRolAdministrativo((int) $identidadProfesor['id_login'])
                : null,
        ]);
    case 'read_prof_perfil':
        if ($id_prof < 1) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'No existe un perfil Docente válido en la sesión.',
            ]);
        }
        $resp = $doc->mostrarProfId($id_prof);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_prog':
        exigirAdminComiteDocente();
        $idPrograma = (int) $id_usu;
        if ($doc->obtenerIdentidadProfesorPorUsuario($idPrograma) === null) {
            responderDocente(409, [
                'ok' => false,
                'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                'mensaje' => 'El objetivo no representa un Profesor válido.',
            ]);
        }
        $resp = $doc->mostrarDatosProg($idPrograma);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_prog_perfil':
        if ($id_prof < 1) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'No existe un perfil Docente válido en la sesión.',
            ]);
        }
        if ($doc->obtenerIdentidadProfesorPorUsuario($id_prof) === null) {
            responderDocente(409, [
                'ok' => false,
                'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                'mensaje' => 'El objetivo no representa un Profesor válido.',
            ]);
        }
        $resp = $doc->mostrarDatosProg($id_prof);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-doc':
        exigirAdminComiteDocente();
        $resp = $doc->mostrarProf($excluirLoginListado, estadoFiltroProfesor());// muestra todos los docentes
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-filtrada':// lista de los docentes segun vinculo con el programa
        exigirAdminComiteDocente();
        $resp = $doc->mostrarProfTipo($tipo, $excluirLoginListado, estadoFiltroProfesor());
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-doc-search':
        exigirAdminComiteDocente();
        $resp = $doc->buscarProfAdministrativo($busqueda, $excluirLoginListado, estadoFiltroProfesor());
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'insert-update':
        $contextoAlta = contextoAltaProfesor();
        $esAutoalta = $contextoAlta === 'autoalta';
        $estadoProfesor = $esAutoalta ? 1 : 2;
        $lineasValidas = array_values(array_unique(array_filter(
            array_map('intval', $lineaInv),
            static fn (int $linea): bool => $linea > 0
        )));
        $datosTexto = [
            $nombres, $ap_mat, $ap_pat, $fech_nac, $nro_doc, $region, $comuna,
            $telefono, $direccion, $cont_em, $tel_em, $correo, $pass,
        ];
        $datosNumericos = [
            (int) $pais_res, (int) $pais_nac, (int) $documento, (int) $genero,
            (int) $catAcad, (int) $anioIng, (int) $vinculo,
        ];
        if (
            in_array('', $datosTexto, true)
            || min($datosNumericos) < 1
            || $lineasValidas === []
            || ($inst < 1 && $nuevaInstitucion === '')
        ) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'DATOS_INVALIDOS',
                'mensaje' => 'Debe completar todos los antecedentes requeridos del Profesor.',
            ]);
        }
        if ($esAutoalta) {
            unset($_SESSION['autoalta_profesor']);
        }
        try {
            $resultadoCrear = $doc->crearProfesorIntegral([
                'correo' => $correo,
                'pass' => $pass,
                'pueblo' => (int) $pueblo,
                'pais_res' => (int) $pais_res,
                'pais_nac' => (int) $pais_nac,
                'fecha_nac' => $fech_nac,
                'nombres' => $nombres,
                'ap_mat' => $ap_mat,
                'ap_pat' => $ap_pat,
                'tipo_doc' => (int) $documento,
                'nro_doc' => $nro_doc,
                'inst_usuario' => $inst,
                'nueva_institucion' => $nuevaInstitucion,
                'genero' => (int) $genero,
                'region' => $region,
                'comuna' => $comuna,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'cont_em' => $cont_em,
                'tel_em' => $tel_em,
                'cat_academica' => $catAcad,
                'anio_ingreso' => $anioIng,
                'vinculo' => $vinculo,
                'lineas' => $lineasValidas,
            ], $estadoProfesor);
            if (!$resultadoCrear['ok']) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => $resultadoCrear['codigo'],
                    'mensaje' => 'Los antecedentes del Profesor no permiten completar el alta.',
                ]);
            }
            responderDocente(200, [
                'ok' => true,
                'codigo' => 'DOCENTE_CREADO',
                'id_usuario' => $resultadoCrear['id_usuario'],
                'mensaje' => 'Usuario guardado correctamente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_CREATE] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible guardar el perfil Docente.',
            ]);
        }
    case 'agregar-docencia':
        exigirAdminDocente();
        $idLoginTransicion = isset($_POST['id_login']) && is_scalar($_POST['id_login'])
            ? filter_var($_POST['id_login'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
        $lineasValidas = array_values(array_filter(
            array_map('intval', $lineaInv),
            static fn (int $linea): bool => $linea > 0
        ));
        $datosTexto = [
            $nombres, $ap_mat, $ap_pat, $fech_nac, $nro_doc, $region, $comuna,
            $telefono, $direccion, $cont_em, $tel_em,
        ];
        $datosNumericos = [
            (int) $pais_res, (int) $pais_nac, (int) $documento, (int) $genero,
            (int) $catAcad, (int) $anioIng, (int) $vinculo,
        ];
        if (
            $idLoginTransicion === false
            || in_array('', $datosTexto, true)
            || min($datosNumericos) < 1
            || $lineasValidas === []
            || ($inst < 1 && $nuevaInstitucion === '')
        ) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'DATOS_INVALIDOS',
                'mensaje' => 'Debe completar todos los antecedentes requeridos para agregar Docencia.',
            ]);
        }
        try {
            $resultadoTransicion = $doc->agregarDocenciaAdministrativa((int) $idLoginTransicion, [
                'pueblo' => (int) $pueblo,
                'pais_res' => (int) $pais_res,
                'pais_nac' => (int) $pais_nac,
                'fecha_nac' => $fech_nac,
                'nombres' => $nombres,
                'ap_mat' => $ap_mat,
                'ap_pat' => $ap_pat,
                'tipo_doc' => (int) $documento,
                'nro_doc' => $nro_doc,
                'inst_usuario' => $inst,
                'nueva_institucion' => $nuevaInstitucion,
                'genero' => (int) $genero,
                'region' => $region,
                'comuna' => $comuna,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'cont_em' => $cont_em,
                'tel_em' => $tel_em,
                'cat_academica' => $catAcad,
                'anio_ingreso' => $anioIng,
                'vinculo' => $vinculo,
                'lineas' => $lineasValidas,
            ]);
            if (!$resultadoTransicion['ok']) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => $resultadoTransicion['codigo'],
                    'mensaje' => 'La cuenta ya no cumple las condiciones para agregar Docencia.',
                ]);
            }
            if ($actorLogin === (int) $idLoginTransicion) {
                $_SESSION['login'] = $actorLogin;
                $_SESSION['docente'] = (int) $idLoginTransicion;
                $_SESSION['id_usuario'] = [['id_usuario' => $resultadoTransicion['id_usuario']]];
                $_SESSION['estado_profesor'] = 2;
                $capacidades = isset($_SESSION['capacidades']) && is_array($_SESSION['capacidades'])
                    ? array_filter($_SESSION['capacidades'], 'is_string')
                    : [];
                $capacidades[] = 'docente.habilitado';
                $_SESSION['capacidades'] = array_values(array_unique($capacidades));
            }
            responderDocente(200, [
                'ok' => true,
                'codigo' => 'DOCENCIA_AGREGADA',
                'id_login' => $resultadoTransicion['id_login'],
                'id_usuario' => $resultadoTransicion['id_usuario'],
                'correo' => $resultadoTransicion['correo'],
                'mensaje' => 'Docencia agregada correctamente sobre la cuenta existente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENCIA_TRANSICION] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible agregar Docencia; la cuenta administrativa se conserva.',
            ]);
        }
        break;
    case 'update-estado-profesor':
        exigirAdminComiteDocente();
        $idLoginEstado = isset($_POST['id_login']) && is_scalar($_POST['id_login'])
            ? filter_var($_POST['id_login'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
        $estadoObjetivo = isset($_POST['estado_objetivo']) && is_scalar($_POST['estado_objetivo'])
            && ctype_digit((string) $_POST['estado_objetivo'])
                ? (int) $_POST['estado_objetivo']
                : 0;
        if ($idLoginEstado === false || !in_array($estadoObjetivo, [2, 3], true)) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'ESTADO_OBJETIVO_INVALIDO',
                'mensaje' => 'La transicion de estado solicitada no es valida.',
            ]);
        }
        try {
            $resultadoEstado = $doc->cambiarEstadoProfesor((int) $idLoginEstado, $estadoObjetivo);
            if (!$resultadoEstado['ok']) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => $resultadoEstado['codigo'],
                    'mensaje' => 'El estado actual del Profesor no permite esta transicion.',
                ]);
            }
            responderDocente(200, [
                'ok' => true,
                'codigo' => $resultadoEstado['codigo'],
                'estado_profesor' => $resultadoEstado['estado_profesor'],
                'mensaje' => $estadoObjetivo === 2
                    ? 'Profesor aceptado. Debe volver a iniciar sesion para reflejar la habilitacion.'
                    : 'Solicitud de Profesor rechazada.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_ESTADO] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible actualizar el estado del Profesor.',
            ]);
        }
    case 'update-rol-admin':
        exigirAdminDocente();
        $idLoginRol = isset($_POST['id_login']) && is_scalar($_POST['id_login'])
            ? filter_var($_POST['id_login'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
        $rolObjetivo = isset($_POST['rol_admin']) && is_string($_POST['rol_admin'])
            ? $_POST['rol_admin']
            : '';
        if ($idLoginRol === false || !in_array($rolObjetivo, ['none', 'admin', 'comite'], true)) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'ROL_OBJETIVO_INVALIDO',
                'mensaje' => 'El rol administrativo solicitado no es válido.',
            ]);
        }
        if ($actorLogin === (int) $idLoginRol) {
            responderDocente(409, [
                'ok' => false,
                'error' => 'AUTORROL_PROHIBIDO',
                'mensaje' => 'No puede modificar su propio rol administrativo.',
            ]);
        }
        try {
            $resultadoRol = $doc->cambiarRolAdministrativo((int) $idLoginRol, $rolObjetivo);
            if (!$resultadoRol['ok']) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => $resultadoRol['codigo'],
                    'mensaje' => 'El estado actual del Profesor no permite modificar su rol.',
                ]);
            }
            responderDocente(200, [
                'ok' => true,
                'codigo' => $resultadoRol['codigo'],
                'rol' => $resultadoRol['rol'],
                'mensaje' => 'Rol administrativo actualizado correctamente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_ROL] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible actualizar el rol administrativo.',
            ]);
        }
    case 'delete':
        if (!Authorization::hasAny(['admin'])) {
            responderDocente(403, [
                'ok' => false,
                'error' => 'NO_AUTORIZADO',
                'mensaje' => 'Sólo un administrador puede eliminar un Profesor.',
            ]);
        }
        if ($idLoginEliminar === null) {
            responderDocente(400, [
                'ok' => false,
                'error' => 'IDENTIFICADOR_INVALIDO',
                'mensaje' => 'El identificador indicado no es válido.',
            ]);
        }
        if ($actorLogin > 0 && $idLoginEliminar === $actorLogin) {
            responderDocente(409, [
                'ok' => false,
                'error' => 'AUTOELIMINACION_DOCENTE_PROHIBIDA',
                'mensaje' => 'No puede eliminar su propio registro Docente.',
            ]);
        }
        try {
            $resultadoEliminar = $doc->eliminarProfesorInequivoco($idLoginEliminar);
            if (!$resultadoEliminar['ok']) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => $resultadoEliminar['codigo'],
                    'mensaje' => $resultadoEliminar['codigo'] === 'DOCENTE_CON_ROL_ADMINISTRATIVO'
                        ? 'Docente con rol administrativo; eliminación completa no permitida.'
                        : 'La identidad seleccionada no corresponde a un Profesor simple eliminable.',
                ]);
            }
            responderDocente(200, [
                'ok' => true,
                'mensaje' => 'Profesor eliminado correctamente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_DELETE] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible eliminar al Profesor.',
            ]);
        }
        break;
    case 'update-inf-pers':
    case 'update-inf-pers-perfil':
        try {
            $objetivoEdicion = resolverObjetivoEdicionDocente(
                $doc,
                $actorLogin,
                (int) $id_usu,
                (int) $id_login,
                $op === 'update-inf-pers-perfil'
            );
            $id_usu = $objetivoEdicion['id_usuario'];
            $id_login = $objetivoEdicion['id_login'];

            $resp_pers = $doc->editarInfPers($nombres, $ap_mat, $ap_pat, $fech_nac, $documento, $nro_doc, $pais_nac, $genero, $pais_res, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em, $correo, $pass, $pueblo, $id_usu, $id_login);
            if (!($resp_pers instanceof \PDOStatement)) {
                throw new \RuntimeException('No fue posible actualizar los datos personales del Profesor.');
            }

            $resp_log = $doc->editarCredencialesDocente((int) $id_usu, (int) $id_login, (string) $correo, (string) $pass);
            if (!$resp_log) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                    'mensaje' => 'No fue posible validar la identidad del Profesor para editar sus credenciales.',
                ]);
            }

            responderDocente(200, [
                'ok' => true,
                'codigo' => 'DOCENTE_ACTUALIZADO',
                'mensaje' => 'Datos editados correctamente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_UPDATE_PERSONAL] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible editar los datos personales del Profesor.',
            ]);
        }
    case 'update-inf-prog':
    case 'update-inf-prog-perfil':
        try {
            $objetivoEdicion = resolverObjetivoEdicionDocente(
                $doc,
                $actorLogin,
                (int) $id_usu,
                (int) $id_login,
                $op === 'update-inf-prog-perfil'
            );
            $id_usu = $objetivoEdicion['id_usuario'];
            $id_login = $objetivoEdicion['id_login'];

            //editar Linea Inv
            //validar las lineas que no existen en la base de datoa
            foreach ($lineaInv as $linea) {
                //buscar si linea esta guardada
                $existLinea = (array) $doc->buscarLinInv($linea, $id_usu);
                $existLinea = $existLinea ? true : false;
                if (!$existLinea) {
                    $respuesta = $doc->insertarLineaInv($id_usu, $linea);
                    if (!($respuesta instanceof \PDOStatement) || $respuesta->rowCount() !== 1) {
                        throw new \RuntimeException('No fue posible agregar una línea de investigación.');
                    }
                }
            }
            $lineasDB = $doc->todasLinInv($id_usu);
            //validar linea que existen en la base de dato, pero deben ser eliminadas
            foreach ($lineasDB as $lin) {
                if (!in_array($lin['linea_inv'], $lineaInv)) {
                    $respuestaLinea = $doc->borrarLineaInv($id_usu, $lin['linea_inv']);
                    if (!($respuestaLinea instanceof \PDOStatement)) {
                        throw new \RuntimeException('No fue posible retirar una línea de investigación.');
                    }
                }
            }

            //editar datos programa; rowCount 0 puede representar datos idénticos
            $resp_prof = $doc->editarDatProg($id_usu, $vinculo, $catAcad, $anioIng);
            $resp_inst = $doc->editarInstDoc($id_usu, $inst);
            if (!($resp_prof instanceof \PDOStatement) || !($resp_inst instanceof \PDOStatement)) {
                throw new \RuntimeException('No fue posible actualizar los datos de programa del Profesor.');
            }

            responderDocente(200, [
                'ok' => true,
                'codigo' => 'PROGRAMA_DOCENTE_ACTUALIZADO',
                'mensaje' => 'Los datos de programa fueron editados correctamente.',
            ]);
        } catch (\Throwable $error) {
            error_log('[DOCENTE_UPDATE_PROGRAMA] ' . $error->getMessage());
            responderDocente(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible editar los datos de programa del Profesor.',
            ]);
        }
}
