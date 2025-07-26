<?php
require "validaciones.php";
require ("../model/Congreso.php");
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$usu=isset($_POST['usu'])?(int)$_POST['usu']:'';
$nombre=isset($_POST['nombre'])?$_POST['nombre']:'';
$ciudad=isset($_POST['ciudad'])?$_POST['ciudad']:'';
$anio=isset($_POST['anio'])?(int)$_POST['anio']:'';
$fech_in=isset($_POST['fech_in'])?$_POST['fech_in']:'';
$fech_ter=isset($_POST['fech_ter'])?$_POST['fech_ter']:'';
$tipo_part=isset($_POST['tipo_part'])?(int)$_POST['tipo_part']:'';
$rol=isset($_POST['rol'])?(int)$_POST['rol']:'';
$tipo_cong=isset($_POST['tipo_cong'])?(int)$_POST['tipo_cong']:'';
$autor=isset($_POST['nom_autor'])?$_POST['nom_autor']:'';
$id_coautor=isset($_POST['coautor'])?(int)$_POST['coautor']:'';
$id_autor=isset($_POST['autor'])?(int)$_POST['autor']:'';// prbar si esta bien el nombre de la variable // un caso de prueba
$coautores=isset($_POST['coautores'])?$_POST['coautores']:'';
$nom_mesa=isset($_POST['nom_mesa'])?$_POST['nom_mesa']:'';
$comen_pon=isset($_POST['comen_pon'])?$_POST['comen_pon']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$id_congreso=isset($_POST['cong'])?(int)$_POST['cong']:'';
$part=isset($_POST['part'])?(int)$_POST['part']:'';
$id_cong=0;
$id=isset($_POST['id'])?$_POST['id']:'';
$op=isset($_POST['op'])?$_POST['op']:'';
$cong= new Congreso();
switch($op){
    case'read_cong':
        $resp=$cong->cargarNomCong($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'read_part':
        $resp=$cong->cargarNomMesa($busqueda,$tipo_part,$id_congreso);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'carg_cong':
        $resp=$cong->cargarCong($id);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'carg_part':
        $resp=$cong->cargarPart($id);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;    
    //insertar publicaciones
    case 'insert-update':
        if(($id_cong == 0) ){        
            $congreso=$cong->insertarCong($nombre,$ciudad,$fech_in,$fech_ter);
            $respuesta=$cong->insertarPart($tipo_part,$tipo_cong,$coautores,$nom_mesa,$comen_pon,$congreso,$autor,$id_autor,$id_coautor);
            $respuesta ? $mensaje="Congreso registrado" : $mensaje="Congreso no ha sido registrado";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);           
        }
        //else{
        //     $respuesta=$institucion->editar($id_inst,$nombre);
        //     $respuesta ? $mensaje="Pueblo Editado" : $mensaje="Pueblo no ha sido editado";
        //      echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        // }
        break;
        case 'insert-part':
            $respuesta=$cong->insertarPart($tipo_part,$tipo_cong,$coautores,$nom_mesa,$comen_pon,$id_congreso,$autor,$id_autor,$id_coautor);            
            $respuesta ? $mensaje="Participación registrada" : $mensaje="Participación no ha sido registrado";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);                
            break;   
        //mostrar publicaciones
    case 'read':
        $respuesta=$cong->mostrar($usuario);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case 'read-usu':
    $respuesta=$cong->mostrarAutor($usu);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case 'update-rol':
        $respuesta=$rol==1?$cong->editarAutor($id_autor,$part):$cong->editarCoautor($id_coautor,$part);
        $respuesta ? $mensaje="Congreso Actualizado" : $mensaje="Congreso no ha sido actualizado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);        
        break;    
    // case'delete':
    //     $respuesta=$institucion->eliminar($id_inst);
    //     $respuesta ? $mensaje="Pueblo Eliminado" : $mensaje="Pueblo no ha sido eliminado";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    //     break;
        }

?>