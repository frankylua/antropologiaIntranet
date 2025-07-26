<?php
require "validaciones.php";
require ("../model/Pasantia.php");
$prof=isset($_POST['prof'])?$_POST['prof']:'';
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$ciudad=isset($_POST['ciudad'])?$_POST['ciudad']:'';
$pais=isset($_POST['pais'])?$_POST['pais']:'';
$fondo=isset($_POST['fondo'])?$_POST['fondo']:'';
$fech_in=isset($_POST['fecha_in'])?$_POST['fecha_in']:'';
$fech_ter=isset($_POST['fecha_ter'])?$_POST['fecha_ter']:'';
$id_pasant=0;
$pasant= new Pasantia();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert-update':
        if(($id_pasant == 0) ){
            $respuesta=$pasant->insertar($usuario,$prof,$inst,$fech_in,$fech_ter,$ciudad,$fondo,$pais);
            $respuesta ? $mensaje="Pasantia registrada" : $mensaje="Pasantia no ha sido registrada";

            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
         }
        //else{
        //     $respuesta=$institucion->editar($id_inst,$nombre);
        //     $respuesta ? $mensaje="Pueblo Editado" : $mensaje="Pueblo no ha sido editado";
        //      echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        // }
        break;
    case 'read':
        $respuesta=$pasant->mostrar($usuario);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    // case'delete':
    //     $respuesta=$institucion->eliminar($id_inst);
    //     $respuesta ? $mensaje="Pueblo Eliminado" : $mensaje="Pueblo no ha sido eliminado";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    //     break;
        }

?>