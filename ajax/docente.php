<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Docente;
use App\Security\Authorization;

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

function identidadDocenteValida(int $idUsuario, int $idLogin): bool
{
    if ($idUsuario < 1 || $idLogin < 1) {
        return false;
    }

    $consulta = conexion()->prepare(
        'SELECT p.id_profesor FROM profesor p '
        . 'JOIN usuario u ON u.id_usuario = p.usuario '
        . 'WHERE u.id_usuario = :id_usuario AND u.login = :id_login'
    );
    $consulta->execute([
        'id_usuario' => $idUsuario,
        'id_login' => $idLogin,
    ]);
    return count($consulta->fetchAll(\PDO::FETCH_COLUMN)) === 1;
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
$busqueda = isset ($_POST['busqueda']) ? $_POST['busqueda'] : '';
$inst = isset ($_POST['instTrab']) && is_scalar($_POST['instTrab']) ? (int) $_POST['instTrab'] : 0;
$nuevaInstitucion = isset($_POST['nueva_institucion']) && is_string($_POST['nueva_institucion'])
    ? trim(limpiar_datos($_POST['nueva_institucion']))
    : '';
$catAcad = isset ($_POST['catAcad']) ? (int) $_POST['catAcad'] : '';
$anioIng = isset ($_POST['anio_ing']) ? (int) $_POST['anio_ing'] : '';
$vinculo = isset ($_POST['vinculo']) ? (int) $_POST['vinculo'] : '';
$op = $operacionSolicitada;
$lineaInv = isset($_POST['lineaInv']) && is_array($_POST['lineaInv']) ? $_POST['lineaInv'] : [];
$prof = isset ($_SESSION['id_usuario']) ?$_SESSION['id_usuario'] : 0;
$id_prof=$prof!=0?$prof[0]['id_usuario']:0;
$actorLogin = loginActorDocente();
$excluirLoginListado = $actorLogin > 0
    && Authorization::hasAny(['admin', 'comite'])
        ? $actorLogin
        : 0;
//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
switch ($op) {
    case 'read_prof':
        $resp = $doc->buscarProf($busqueda, $excluirLoginListado);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_id':
        $resp = $doc->mostrarProfId($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_perfil':
        $resp = $doc->mostrarProfId($id_prof);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_prog':
        $resp = $doc->mostrarDatosProg($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-doc':
        $resp = $doc->mostrarProf($excluirLoginListado);// muestra todos los docentes
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-filtrada':// lista de los docentes segun vinculo con el programa
        $resp = $doc->mostrarProfTipo($tipo, $excluirLoginListado);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'insert-update':
        try {
            $login = (int) $doc->insertarLogin($correo, $pass);
            if ($login < 1) {
                throw new \RuntimeException('No fue posible crear el login Docente.');
            }

            if(isset($_SESSION['admin'])){
                $permiso = [4];
            }else if(isset($_SESSION['comite'])){
                $permiso = [4];
            }else{
                $permiso = [3];
            }
            foreach ($permiso as $per) {
                $resultadoPermiso = $doc->insertarPermisos($login, (int) $per);
                if (!($resultadoPermiso instanceof \PDOStatement) || $resultadoPermiso->rowCount() !== 1) {
                    throw new \RuntimeException('No fue posible asignar el permiso Docente.');
                }
            }

            $usuario = (int) $doc->insertarUsuario($pueblo, $pais_res, $pais_nac, $login, $fech_nac, $nombres, $ap_mat, $ap_pat, $documento, $nro_doc, $inst, $genero, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em);
            if ($usuario < 1) {
                throw new \RuntimeException('No fue posible crear el usuario Docente.');
            }

            $resultadoProfesor = $doc->insertar($catAcad, $anioIng, $vinculo, $usuario);
            if (!($resultadoProfesor instanceof \PDOStatement) || $resultadoProfesor->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible crear el Profesor.');
            }
            foreach ($lineaInv as $linea) {
                $resultadoLinea = $doc->insertarLineaInv($usuario, $linea);
                if (!($resultadoLinea instanceof \PDOStatement) || $resultadoLinea->rowCount() !== 1) {
                    throw new \RuntimeException('No fue posible guardar una línea de investigación.');
                }
            }

            responderDocente(200, [
                'ok' => true,
                'codigo' => 'DOCENTE_CREADO',
                'id_usuario' => $usuario,
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
        try {
            if (!identidadDocenteValida((int) $id_usu, (int) $id_login)) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                    'mensaje' => 'No fue posible validar la identidad del Profesor para editar sus datos.',
                ]);
            }

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
        try {
            if (!identidadDocenteValida((int) $id_usu, (int) $id_login)) {
                responderDocente(409, [
                    'ok' => false,
                    'error' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE',
                    'mensaje' => 'No fue posible validar la identidad del Profesor para editar sus datos de programa.',
                ]);
            }

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