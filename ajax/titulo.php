<?php
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Grado;
use App\Model\Titulo;
use App\Security\Authorization;
require 'validaciones.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$titulo=new Titulo();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_titulo=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$id_grado=isset($_POST['id_grado'])?(int)$_POST['id_grado']:0;
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:0;
$tipo=isset($_POST['tipo'])?limpiar_datos($_POST['tipo']):"";
$tipo_grado=isset($_POST['tipo_grado'])?(int)$_POST['tipo_grado']:'';
$tipog=null;
// $tipog=$tipo=='lic'?$tipo=='un'?$tipo=='mag'?3:2:1:4;
if($tipo=='lic'){
    $tipog=1;
}
if($tipo=='un'){
    $tipog=2;
}
if($tipo=='mag'){
    $tipog=3;
}
if($tipo=='doc'){
    $tipog=4;
}
$id_titulo=(int)$id_titulo;
$op=isset($_POST['op'])?$_POST['op']:'';

switch($op){
    case 'insert':
        if (!Authorization::hasAny(['admin', 'comite'])) {
            $actorContextual = Authorization::hasAny(['estudiante', 'docente']);
            $usuarioSesion = isset($_SESSION['id_usuario'][0]['id_usuario'])
                ? (int) $_SESSION['id_usuario'][0]['id_usuario']
                : 0;

            if (!$actorContextual || $usuarioSesion <= 0 || $usuario !== $usuarioSesion) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => 'Usuario sin permisos para crear el titulo academico'], JSON_UNESCAPED_UNICODE);
                break;
            }

            if ($id_grado > 0) {
                $grado = new Grado();
                $gradosUsuario = $grado->mostrar($usuarioSesion);
                $gradoPropio = false;

                if (is_array($gradosUsuario)) {
                    foreach ($gradosUsuario as $gradoUsuario) {
                        if (isset($gradoUsuario['id_grado']) && (int) $gradoUsuario['id_grado'] === $id_grado) {
                            $gradoPropio = true;
                            break;
                        }
                    }
                }

                if (!$gradoPropio) {
                    http_response_code(403);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => 'No esta autorizado para modificar estos antecedentes academicos'], JSON_UNESCAPED_UNICODE);
                    break;
                }
            }
        }
        $respuesta=$titulo->insertarObtenerId($nombre,$tipo_grado);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
   
    break;
    case 'insert-update':
        if (!Authorization::hasAny(['admin', 'comite'])) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => 'Usuario sin permisos para crear o editar titulos academicos'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if(($id_titulo == 0) ){
            $respuesta=$titulo->insertar($nombre,$tipog);
             $respuesta ? $pueblor="Titulo Registrado" : $pueblor="Titulo no ha sido registrado";
            echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);
        }else{
            $respuesta=$titulo->editar($id_titulo,$nombre);
            $respuesta ? $pueblor="Titulo Editado" : $pueblor="Titulo no ha sido editado";
             echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'read':
        $respuesta=$titulo->mostrarPorGrado($tipog);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case'delete':
        header('Content-Type: application/json; charset=utf-8');
        if (!Authorization::hasAny(['admin', 'comite'])) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => 'Usuario sin permisos para eliminar'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if ($id_titulo <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'codigo' => 'ID_INVALIDO', 'mensaje' => 'El identificador del título no es válido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $resultado = $titulo->eliminar($id_titulo);
        if ($resultado['ok'] === false && $resultado['codigo'] === 'ERROR_ELIMINACION') {
            http_response_code(500);
        }
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
         }
?>
