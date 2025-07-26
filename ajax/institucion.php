<?php
require_once "../model/Institucion.php";
require "validaciones.php";
$institucion=new Institucion();
$nombre=isset($_POST['nombre'])?$_POST['nombre']:"";
$id_inst=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$id_inst=(int)$id_inst;
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert':
            $respuesta=$institucion->insertarObtenerId($nombre);
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
       
        break;
    case 'insert-update':
        if(($id_inst == 0) ){
            $respuesta=$institucion->insertar($nombre);
             $respuesta ? $mensaje="Institución Registrado" : $mensaje="Institución no ha sido registrado";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        }else{
            $respuesta=$institucion->editar($id_inst,$nombre);
            $respuesta ? $mensaje="Institución Editado" : $mensaje="Institución no ha sido editado";
             echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'read':
        $respuesta=$institucion->mostrarConsultaOrdenada();
        $prueba=['prueba'=>'hola'];
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case'delete':
        $respuesta=$institucion->eliminar($id_inst);
        $respuesta ? $mensaje="Institución eliminada" : $mensaje="Institución no ha sido eliminado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        }

?>