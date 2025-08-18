<?php

require "validaciones.php";
require "../model/Grado.php";
$titulo=isset($_POST['titulo'])?(int)$_POST['titulo']:'';
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$fecha=isset($_POST['fecha'])?$_POST['fecha']:'';
$id_grado=isset($_POST['id_grado'])?(int)$_POST['id_grado']:0;
$grado= new Grado();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert-update':
        if(($id_grado == 0) ){
            $respuesta=$grado->insertar($usuario,$inst,$titulo,$fecha);
            $respuesta ? $mensaje="Grado Académico registrado" : $mensaje="Grado Académico no ha sido registrado";

            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
         }else{
            $respuesta=$grado->editar($id_grado,$inst,$titulo,$fecha);
            $respuesta ? $mensaje="Grado Académico Editado" : $mensaje="Grado Académico no ha sido editado";
             echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'read':
        $respuesta=$grado->mostrar($usuario);
        var_dump('respuesta de pueblo');
        var_dump($respuesta);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case 'read_grado_id':
    $respuesta=$grado->mostrarGrado($id_grado);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case'delete':
       
        $respuesta=$grado->eliminar($id_grado);
        $respuesta ? $mensaje="Grado Académico Eliminado" : $mensaje="Grado Académicos no ha sido eliminado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        }

?>