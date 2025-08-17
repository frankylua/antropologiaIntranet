<?php
require "validaciones.php";
require "../model/Tesis.php";
$lugar=isset($_POST['lugar'])?(int)$_POST['lugar']:'';
$grado=isset($_POST['grado'])?(int)$_POST['grado']:'';
$anio=isset($_POST['anio'])?(int)$_POST['anio']:'';
$rol=isset($_POST['rol'])?(int)$_POST['rol']:'';
$titulo=isset($_POST['titulo'])?$_POST['titulo']:'';
$nom_guia=isset($_POST['nom_guia'])?$_POST['nom_guia']:'';
$nom_coguia=isset($_POST['nom_coguia'])?$_POST['nom_coguia']:'';
$nom_est=isset($_POST['nom_est'])?$_POST['nom_est']:'';
$guia=isset($_POST['guia'])?(int)$_POST['guia']:'';
$coguia=isset($_POST['coguia'])?(int)$_POST['coguia']:'';
$est=isset($_POST['est'])?(int)$_POST['est']:'';
$inst_tesis=isset($_POST['inst_tesis'])?(int)$_POST['inst_tesis']:'';
$inst_cot=isset($_POST['inst_cot'])?(int)$_POST['inst_cot']:'';
$pais_tesis=isset($_POST['pais_tesis'])?(int)$_POST['pais_tesis']:'';
$pais_cot=isset($_POST['pais_cot'])?(int)$_POST['pais_cot']:'';
$fecha_aprob=isset($_POST['fecha_aprob'])?$_POST['fecha_aprob']:'';
$fecha_def=isset($_POST['fecha_def'])?$_POST['fecha_def']:'';
$panel=isset($_POST['panel'])?$_POST['panel']:'';
$cotutela=isset($_POST['cotutela'])?$_POST['cotutela']:'';
$prof_cot=isset($_POST['prof_cot'])?$_POST['prof_cot']:'';
$fondo=isset($_POST['fondo'])?$_POST['fondo']:'';
$ciudad_cot=isset($_POST['ciudad_cot'])?$_POST['ciudad_cot']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$id_tesis=isset($_POST['id_tesis'])?$_POST['id_tesis']:0;
$id=isset($_POST['id'])?$_POST['id']:'';
$usu=isset($_POST['usu'])?$_POST['usu']:'';
$tesis= new Tesis();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    case'read_tesis':
        $resp=$tesis->cargarTitulos($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'read_prof':
        $resp=$tesis->mostrarProf($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'read_est':
        $resp=$tesis->mostrarEst($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;

    case'carg_tesis':
        $resp=$tesis->cargarTesisCotutela($id);
        $sinCot=(array)$resp;
        $resp=count($sinCot)==0?$tesis->cargarTesis($id):$resp;
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case'usu_tesis':
        $resp=$tesis->cargarTesisCotutela($usuario);
        $sinCot=(array)$resp;
        $resp=count($sinCot)==0?$tesis->cargarTesis($id):$resp;
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;    
    //insertar publicaciones
    case 'insert-update':
        
        if(($id_tesis == 0) ){        
            $regtesis=$tesis->insertarTesis($lugar,$grado,$anio,$titulo,$nom_guia,$nom_est,$nom_coguia,$guia,$est,$coguia,$inst_tesis,$pais_tesis,$fecha_aprob,$fecha_def,$panel);
            if($cotutela=='true'){
                $respuesta=$tesis->insertarCotutela($prof_cot,$inst_cot,$fondo,$ciudad_cot,$pais_cot,$regtesis);
            }else{
                $respuesta=$regtesis>0?true:false;
            }           
            $respuesta ? $mensaje="Tesis registrada" : $mensaje="Tesis no ha sido registrada";
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);           
        }
        //else{
        //     $respuesta=$institucion->editar($id_inst,$nombre);
        //     $respuesta ? $mensaje="Pueblo Editado" : $mensaje="Pueblo no ha sido editado";
        //      echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        // }
        break;

    case 'read':
        $resp=$tesis->mostrarTesis($usu);
         echo json_encode($resp, JSON_UNESCAPED_UNICODE);
         break;
    case 'read-cotutela':
    $resp=$tesis->mostrarCotutela($id_tesis);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-usu':
        $respuesta=$tesis->cargarProf($usu);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;

        //  case 'read-usu':
        //     $respuesta=$proy->mostrarProf($usu);
        //         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        //         break;
    case 'update-guia':
        $respuesta=$tesis->editarGuia($guia,$id_tesis);
        $respuesta ? $mensaje="Tesis Actualizada" : $mensaje="Tesis no ha sido actualizada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);        
        break;
    case 'update-coguia':
        $respuesta=$tesis->editarCoguia($coguia,$id_tesis);
        $respuesta ? $mensaje="Tesis Actualizada" : $mensaje="Tesis no ha sido actualizada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);        
        break;    
    // case'delete':
    //     $respuesta=$institucion->eliminar($id_inst);
    //     $respuesta ? $mensaje="Pueblo Eliminado" : $mensaje="Pueblo no ha sido eliminado";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    //     break;
        }

