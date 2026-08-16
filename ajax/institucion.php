<?php
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Institucion;
use App\Security\Authorization;

require 'validaciones.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** @param array<string, mixed> $payload */
function responderInstitucion(int $estadoHttp, array $payload): void
{
    http_response_code($estadoHttp);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
}

function usuarioContextualInstitucion(): int
{
    $login = isset($_SESSION['login']) && is_scalar($_SESSION['login'])
        ? (int) $_SESSION['login']
        : 0;
    $actorAutenticado = $login > 0 && (
        (isset($_SESSION['estudiante']) && (int) $_SESSION['estudiante'] === $login)
        || (isset($_SESSION['docente']) && (int) $_SESSION['docente'] === $login)
    );

    if (!$actorAutenticado || !Authorization::hasAny(['estudiante', 'docente'])) {
        return 0;
    }

    $usuarios = $_SESSION['id_usuario'] ?? null;
    if (
        !is_array($usuarios)
        || count($usuarios) !== 1
        || !isset($usuarios[0]['id_usuario'])
        || !is_scalar($usuarios[0]['id_usuario'])
    ) {
        return 0;
    }

    $usuario = (int) $usuarios[0]['id_usuario'];
    return $usuario > 0 ? $usuario : 0;
}

$institucion = new Institucion();
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$idInst = isset($_POST['id']) ? (int) limpiar_datos($_POST['id']) : 0;
$usuarioSolicitado = isset($_POST['usuario']) ? (int) $_POST['usuario'] : 0;
$contexto = isset($_POST['contexto']) && is_string($_POST['contexto']) ? $_POST['contexto'] : '';
$op = isset($_POST['op']) ? $_POST['op'] : '';

switch ($op) {
    case 'insert':
        if (!Authorization::hasAny(['admin', 'comite'])) {
            $contextosPermitidos = [
                'beca',
                'grado',
                'pasantia',
                'postdoctorado',
                'proyecto',
                'tesis',
            ];
            $usuarioSesion = usuarioContextualInstitucion();

            if (
                $usuarioSesion <= 0
                || $usuarioSolicitado !== $usuarioSesion
                || !in_array($contexto, $contextosPermitidos, true)
            ) {
                responderInstitucion(403, [
                    'ok' => false,
                    'codigo' => 'NO_AUTORIZADO',
                    'mensaje' => 'Usuario sin permisos para crear la institución',
                ]);
                break;
            }
        }

        $respuesta = $institucion->insertarObtenerId($nombre);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;

    case 'insert-update':
        if (!Authorization::hasAny(['admin', 'comite'])) {
            responderInstitucion(403, [
                'ok' => false,
                'codigo' => 'NO_AUTORIZADO',
                'mensaje' => $idInst === 0
                    ? 'Usuario sin permisos para crear instituciones'
                    : 'Usuario sin permisos para editar instituciones',
            ]);
            break;
        }

        if ($idInst === 0) {
            $respuesta = $institucion->insertar($nombre);
            $mensaje = $respuesta ? 'Institución registrada' : 'Institución no ha sido registrada';
        } else {
            $respuesta = $institucion->editar($idInst, $nombre);
            $mensaje = $respuesta ? 'Institución editada' : 'Institución no ha sido editada';
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;

    case 'read':
        $respuesta = $institucion->mostrarConsultaOrdenada();
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;

    case 'delete':
        if (!Authorization::hasAny(['admin', 'comite'])) {
            responderInstitucion(403, [
                'ok' => false,
                'codigo' => 'NO_AUTORIZADO',
                'mensaje' => 'Usuario sin permisos para eliminar',
            ]);
            break;
        }
        if ($idInst <= 0) {
            responderInstitucion(400, [
                'ok' => false,
                'codigo' => 'ID_INVALIDO',
                'mensaje' => 'El identificador de la institución no es válido',
            ]);
            break;
        }

        $resultado = $institucion->eliminar($idInst);
        if ($resultado['ok'] === false && $resultado['codigo'] === 'ERROR_ELIMINACION') {
            http_response_code(500);
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
}
