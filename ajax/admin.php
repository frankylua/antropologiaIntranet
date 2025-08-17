<?php
require "../model/Admin.php";
require "validaciones.php";
$admin=new Admin();
$nombre=isset($_POST['nombre'])?limpiar_datos($_POST['nombre']):'';
$correo=isset($_POST['correo'])?limpiar_datos($_POST['correo']):'';
$pass=isset($_POST['pass'])?limpiar_datos($_POST['pass']):'';
$permiso=isset($_POST['permiso'])?$_POST['permiso']:'';
$id_login=isset($_POST['id_login'])?(int)$_POST['id_login']:'';
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
        case 'insert-update':
                if($id_login == 0 ){
                        $login=$admin->insertarLogin($correo,$pass);
                        $respuesta=$admin->insertar($login,$nombre,$permiso);
                        $respuesta ? $mensaje="<p>Usuario guardado correctamente</p>" : $mensaje="<p>Usuario no ha sido</p>";
                        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                }
                else{
                        
                        $resp_log=$admin->editarLogin($id_login,$correo,$pass);
                        $resp_adm=$admin->editar($id_login,$nombre,$permiso);
                        $respuesta=$resp_adm&&$resp_log?true:false;
                        $resp_log&&$resp_adm ? $mensaje="<p>Usuario editado correctamente</p>" : $mensaje="<p>Usuario no ha sido editado</p>";
                        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                }
        break;
        case 'read':
                $respuesta=$admin->mostrar();
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        case 'read-perfil':
                $respuesta=$admin->mostrar();
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        case 'query_id':
                $respuesta=$admin->mostrarPorId($id_login);
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        case'delete':
                $respuesta=$admin->eliminar($id_login);
                $respuesta ? $mensaje="<p>Usuario Eliminado</p>" : $mensaje="<p>Usuario no ha sido eliminado</p>";
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
      }



?>