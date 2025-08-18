<?php
require "validaciones.php";
require "../model/Beca.php";
$nombre=isset($_POST['nombre'])?$_POST['nombre']:'';
$nueva_beca=isset($_POST['nueva_beca'])?$_POST['nueva_beca']:'';
$tipo_beca=isset($_POST['tipo'])?$_POST['tipo']:'';
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$alumno=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$fecha_in=isset($_POST['fecha_in'])?$_POST['fecha_in']:'';
$fecha_ter=isset($_POST['fecha_ter'])?$_POST['fecha_ter']:'';
$id_beca=isset($_POST['id'])?(int)$_POST['id']:0;
$beca= new Beca();
$op=isset($_POST['op'])?$_POST['op']:'';
$tipo_int=0;
        if($tipo_beca=='bec_int'){
            $tipo_int=1;

        }
        if($tipo_beca=='bec_ext'){
            $tipo_int=2;

        }
switch($op){
    case 'insert':
        $respuesta=$beca->insertarObtenerId($nueva_beca,$tipo_int);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
   
    break;
    case 'insert-beca':
        if(($id_beca == 0) ){
            $respuesta=$beca->insertar($nombre,$tipo_beca,$inst,$fecha_in,$fecha_ter,$alumno);
            $respuesta ? $mensaje="Beca registrada" : $mensaje="Error: Beca no ha sido registrada";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
         }
        //else{
        //     $respuesta=$institucion->editar($id_inst,$nombre);
        //     $respuesta ? $mensaje="Pueblo Editado" : $mensaje="Pueblo no ha sido editado";
        //      echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        // }
        break;
        case 'insert-update':
            if(($id_beca == 0) ){
                $respuesta=$beca->insertarList($nombre,$tipo_int);
                $respuesta ? $mensaje="Beca registrada" : $mensaje="Error: Beca no ha sido registrada";
                echo json_encode($nombre, JSON_UNESCAPED_UNICODE);
             }
            else{
                $respuesta=$beca->editar($id_inst,$nombre);
                $respuesta ? $mensaje="Beca ha sido editada" : $mensaje="Beca no ha sido editada";
                 echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            }
            break;
    case 'read':
        $respuesta=$beca->mostrar($alumno);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case 'read-nombre':
        $respuesta=$beca->mostrarNomBeca($tipo_beca);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    break;
    case 'read_lista':      
        $respuesta=$beca->mostrarLista($tipo_int);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    // case'delete':
    //     $respuesta=$institucion->eliminar($id_inst);
    //     $respuesta ? $mensaje="Pueblo Eliminado" : $mensaje="Pueblo no ha sido eliminado";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    //     break;
        }

?>