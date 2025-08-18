<?php
require "validaciones.php";
require "../model/Postdoctorado.php";
$prof=isset($_POST['prof'])?limpiar_datos($_POST['prof']):'';
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$fech_in=isset($_POST['fech_in'])?$_POST['fech_in']:'';
$fech_ter=isset($_POST['fech_ter'])?$_POST['fech_ter']:'';
$id_postdoc=isset($_POST['id_postdoc'])?(int)$_POST['id_postdoc']:0;
$postdoc= new Postdoctorado();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert':
            $respuesta=$postdoc->insertar($usuario,$prof,$inst,$fech_in,$fech_ter);
            $respuesta ? $mensaje="Postdoctorado registrado" : $mensaje="Postdoctorado no ha sido registrado";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'update':
        $respuesta=$postdoc->editarPostdoc($id_postdoc,$prof,$inst,$fech_in,$fech_ter);
        $respuesta ? $mensaje="Postdoctorado actualizado" : $mensaje="Postdoctorado no ha sido actualizado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_postdoc_id':
        $respuesta=$postdoc->mostrarPostdoc($id_postdoc);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case 'read':
        $respuesta=$postdoc->mostrar($usuario);
        var_dump('respuesta de pueblo');
        var_dump($respuesta);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case'delete':
        $respuesta=$postdoc->eliminar($id_postdoc);
        $respuesta ? $mensaje="Postdoctorado Eliminado" : $mensaje="Postdoctorado no ha sido eliminado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        }

?>