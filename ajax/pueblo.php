<?php

header('Content-Type: application/json');
require "../model/Pueblo.php";
require "validaciones.php";
$pueblo=new Pueblo();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):"";
$id_pueblo=isset($_POST['id'])?limpiar_datos($_POST['id']):"";
$id_pueblo=(int)$id_pueblo;
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case 'insert-update':
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
        $respuesta=$pueblo->eliminar($id_pueblo);
        $respuesta ? $pueblor="Pueblo Eliminado" : $pueblor="Pueblo no ha sido eliminado";
        echo json_encode($pueblor, JSON_UNESCAPED_UNICODE);

        break;
        }

?>



      
        
        
    


                
            