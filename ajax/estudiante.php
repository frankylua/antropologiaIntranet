<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
session_start();
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Docente;
use App\Model\Estudiante;
use App\Security\Authorization;

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
    case 'read_est_perfil':
        if (!Authorization::hasCapability('perfil.ver') && !Authorization::hasAny(['admin', 'comite'])) {
            http_response_code(403);
            echo json_encode(['error' => 'FORBIDDEN'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if ($id_est <= 0) {
            echo json_encode([], JSON_UNESCAPED_UNICODE);
            break;
        }
        $resp = $est->mostrarEstId($id_est);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;

    case 'read-est':
        $resp = $est->mostrarEst();
        // header('Content-Type: application/json');
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_est_id':
        $resp = $est->mostrarEstId($id_usu);
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
    case 'update-inf-pers':
        $resp_pers = $est->editarInfPers($nombres, $ap_mat, $ap_pat, $fech_nac, $documento, $nro_doc, $pais_nac, $genero, $pais_res, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em, $correo, $pass, $pueblo, $id_usu, $id_login);
        $resp_log = $est->editarLogin($id_login, $correo, $pass);
        $mensaje = $resp_pers && $resp_log ? 'Datos Editados Correctamente' : 'Los datos no han sido editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
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
    case 'val-mail':
        $resp = $est->validarCorreo($correo);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
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
