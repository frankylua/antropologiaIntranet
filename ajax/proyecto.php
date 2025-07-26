<?php
require "validaciones.php";
require ("../model/Proyecto.php");
$usuario=isset($_POST['usuario'])?$_POST['usuario']:'';
$folio=isset($_POST['folio'])?$_POST['folio']:'';
$anio=isset($_POST['anio'])?(int)$_POST['anio']:'';
$duracion=isset($_POST['duracion'])?(int)$_POST['duracion']:'';
$titulo=isset($_POST['titulo'])?$_POST['titulo']:'';
$nom_inv=isset($_POST['nom_inv'])?$_POST['nom_inv']:'';
$nom_coinv=isset($_POST['nom_coinv'])?$_POST['nom_coinv']:'';
$inv=isset($_POST['inv'])?(int)$_POST['inv']:'';
$coinv=isset($_POST['coinv'])?(int)$_POST['coinv']:'';
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$financ=isset($_POST['financ'])?(int)$_POST['financ']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$id_proy=isset($_POST['id_proy'])?$_POST['id_proy']:0;
$id=isset($_POST['id'])?$_POST['id']:'';
$id_inst=isset($_POST['id_inst'])?$_POST['id_inst']:'';
$usu=isset($_POST['usu'])?$_POST['usu']:'';
$proy= new Proyecto();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case'read_proy':
        $resp=$proy->cargarFolio($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'carg_proy':
        $resp=$proy->cargarProyecto($id);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;   
    //insertar publicaciones
    case 'insert-update':
        
        if(($id_proy == 0) ){        
            $respuesta=$proy->insertar($folio,$anio,$duracion,$titulo,$nom_inv,$nom_coinv,$inv,$coinv,$inst,$financ);
            
            $respuesta ? $mensaje="Proyecto de Investigación registrado" : $mensaje="Proyecto de Investigación no ha sido registrado";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);           
        }
        //else{
        //     $respuesta=$institucion->editar($id_inst,$nombre);
        //     $respuesta ? $mensaje="Pueblo Editado" : $mensaje="Pueblo no ha sido editado";
        //      echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        // }
        break;
        // case 'insert-part':
        //     $respuesta=$cong->insertarPart($tipo_part,$tipo_cong,$coautores,$nom_mesa,$comen_pon,$id_congreso,$autor,$id_autor,$id_coautor);            
        //     $respuesta ? $mensaje="Participación registrada" : $mensaje="Participación no ha sido registrado";
        //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);                
        //     break;   
        //mostrar publicaciones
    case 'read':
        $respuesta=$proy->mostrar($usuario);
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case 'read-inst':
        $respuesta=$proy->mostrarInst($id_inst);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
       


    case 'read-usu':
        $respuesta=$proy->mostrarInvCoinv($usu);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        // case 'read-inst':
        // //$respuesta=$proy->mostrarInvCoinv($usu);
        // //echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        // break;
    case 'update-inv':
        $respuesta=$proy->editarInv($inv,$id_proy);
        $respuesta ? $mensaje="Proyecto de Investigación Actualizado" : $mensaje="Proyecto de Investigación no ha sido actualizado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);        
        break;
    case 'update-coinv':
        $respuesta=$proy->editarCoinv($coinv,$id_proy,$inst);
        $respuesta ? $mensaje="Proyecto de Investigación Actualizado" : $mensaje="Proyecto de Investigación no ha sido actualizado";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);        
        break;    
    // case'delete':
    //     $respuesta=$institucion->eliminar($id_inst);
    //     $respuesta ? $mensaje="Pueblo Eliminado" : $mensaje="Pueblo no ha sido eliminado";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    //     break;
        }

?>