<?php
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Titulo;
require 'validaciones.php';
$titulo=new Titulo();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_titulo=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$tipo=isset($_POST['tipo'])?limpiar_datos($_POST['tipo']):"";
$tipo_grado=isset($_POST['tipo_grado'])?(int)$_POST['tipo_grado']:0;
// // $tipog=$tipo=='lic'?$tipo=='un'?$tipo=='mag'?3:2:1:4;
// if($tipo=='lic'){
//     $tipog=1;
// }
// if($tipo=='un'){
//     $tipog=2;
// }
// if($tipo=='mag'){
//     $tipog=3;
// }
// if($tipo=='doc'){
//     $tipog=4;
// }
$id_titulo=(int)$id_titulo;
$op=isset($_POST['op'])?$_POST['op']:'';

switch($op){
    case 'insert':
    case 'insert-update':
    case 'delete':
        http_response_code(410);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'codigo' => 'OPERACION_NO_DISPONIBLE', 'mensaje' => 'La ruta heredada no permite modificar titulos academicos'], JSON_UNESCAPED_UNICODE);
        break;
    case 'read':
        $respuesta=$titulo->mostrarPorGrado($tipog);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
         }
?>