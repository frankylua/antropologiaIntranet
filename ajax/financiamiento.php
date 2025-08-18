<?php
require_once "../model/Financiamiento.php";
require "validaciones.php";
$financ=new Financiamiento();
$nom_financ=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_financ=isset($_POST['id'])?limpiar_datos($_POST['id']):0;
$id_financ=(int)$id_financ;
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert':
        $respuesta=$financ->insertarObtenerId($nom_financ);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
   
    break;
    case 'insert-update':
        if(($id_financ == 0) ){
            $respuesta=$financ->insertar($nom_financ);
             $respuesta ? $mensaje="Fuente de Financiamiento Registrada" : $mensaje="Fuente de Financiamiento no ha sido registrada";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        }else{
            $respuesta=$financ->editar($id_financ,$financ);
            $respuesta ? $mensaje="Fuente de Financiamiento Editada" : $mwnsaje="Fuente de Financiamiento no ha sido editad";
             echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'read':
        $respuesta=$financ->mostrar();
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case'delete':
        $respuesta=$financ->eliminar($id_pueblo);
        $respuesta ? $mensaje="Fuente de Financiamiento Eliminada" : $pueblor="Fuente de Financiamiento no ha sido eliminada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

        break;
        }
?>