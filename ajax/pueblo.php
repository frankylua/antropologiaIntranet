<?php
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Pueblo;
use App\Security\Authorization;
require 'validaciones.php';
$pueblo=new Pueblo();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_pueblo=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$id_pueblo=(int)$id_pueblo;
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert-update':
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!Authorization::hasAny(['admin', 'comite'])) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(403);
            $mensaje = $id_pueblo == 0 ? 'Usuario sin permisos para crear' : 'Usuario sin permisos para editar';
            echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => $mensaje], JSON_UNESCAPED_UNICODE);
            break;
        }
        if(($id_pueblo == 0) ){
            $respuesta=$pueblo->insertar($nombre);
             $respuesta ? $pueblor="Pueblo Registrado" : $pueblor="Pueblo no ha sido registrado";
            echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);
        }else{
            $respuesta=$pueblo->editar($id_pueblo,$nombre);
            $respuesta ? $pueblor="Pueblo Editado" : $pueblor="Pueblo no ha sido editado";
             echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'read':
        $respuesta=$pueblo->mostrar();
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case'delete':
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');
        if (!Authorization::hasAny(['admin', 'comite'])) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'codigo' => 'NO_AUTORIZADO', 'mensaje' => 'Usuario sin permisos para eliminar'], JSON_UNESCAPED_UNICODE);
            break;
        }
        if ($id_pueblo <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'codigo' => 'ID_INVALIDO', 'mensaje' => 'El identificador del pueblo no es válido'], JSON_UNESCAPED_UNICODE);
            break;
        }
        $resultado = $pueblo->eliminar($id_pueblo);
        if ($resultado['ok'] === false && $resultado['codigo'] === 'ERROR_ELIMINACION') {
            http_response_code(500);
        }
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
        }
?>
