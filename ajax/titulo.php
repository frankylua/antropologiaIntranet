<?php
require_once "../model/Titulo.php";
require "validaciones.php";
$titulo=new Titulo();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_titulo=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$tipo=isset($_POST['tipo'])?limpiar_datos($_POST['tipo']):"";
$tipo_grado=isset($_POST['tipo_grado'])?(int)$_POST['tipo_grado']:'';
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
        $respuesta=$titulo->insertarObtenerId($nombre,$tipo_grado);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
   
    break;
    case 'insert-update':
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
        $respuesta=$titulo->eliminar($id_titulo);
        $respuesta ? $pueblor="Titulo Eliminado" : $pueblor="Titulo no ha sido eliminado";
        echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);

        break;
         }
?>