<?php
require "validaciones.php";
require "../model/Publicacion.php";
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$tipo=isset($_POST['tipo'])?(int)$_POST['tipo']:'';
$anio=isset($_POST['anio'])?(int)$_POST['anio']:'';
$autores=isset($_POST['autores'])?$_POST['autores']:'';
$nombre=isset($_POST['nombre'])?$_POST['nombre']:'';
$estado=isset($_POST['estado'])?(int)$_POST['estado']:'';
$titulo=isset($_POST['titulo'])?$_POST['titulo']:'';
$indizacion=isset($_POST['indizacion'])?(int)$_POST['indizacion']:'';
$issn=isset($_POST['issn'])?$_POST['issn']:'';
$fac_imp=isset($_POST['fac_imp'])?floatval($_POST['fac_imp']):'';
$tipo_lib=isset($_POST['tipo_lib'])?(int)$_POST['tipo_lib']:'';
$rol=isset($_POST['rol_libro'])?(int)$_POST['rol_libro']:'';
$ref_ext=isset($_POST['ref_ext'])?(int)$_POST['ref_ext']:'';
$traduccion=isset($_POST['traduccion'])?(int)$_POST['traduccion']:'';
$lugar=isset($_POST['lugar'])?$_POST['lugar']:'';
$editorial=isset($_POST['editorial'])?$_POST['editorial']:'';
$tipo_pub=isset($_POST['tipo_pub'])?$_POST['tipo_pub']:'';
$desc_pub=isset($_POST['desc_pub'])?$_POST['desc_pub']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$libro=isset($_POST['libro'])?$_POST['libro']:'';
$id_pub=isset($_POST['id_pub'])?$_POST['id_pub']:0;
// $tipo_otra_pub=isset($_POST['tipo_otra_pub'])?$_POST['tipo_otra_pub']:'';
$pub= new Publicacion();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
    //insertar publicaciones
    case 'insert':
        if(($id_pub == 0) ){
            if($tipo==1||$tipo==2){
                $publicacion=$pub->insertar($usuario,$nombre,$autores,$anio,$estado);
                $respuesta=$pub->insertarArtRev($publicacion,$tipo,$titulo,$indizacion,$fac_imp,$issn);
                $respuesta ? $mensaje="Publicacion registrada" : $mensaje="Publicacion no ha sido registrada";
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

            }else if($tipo==3){
                $publicacion=$pub->insertar($usuario,$nombre,$autores,$anio,$estado);
                $id_libro=$pub->insertarLibro($publicacion,$tipo_lib,$rol,$ref_ext,$traduccion,$lugar);
                //$arreglo=[];
                foreach ($editorial as $ed) {                   
                    $id_editorial=$pub->insertarEditorial($ed);
                    $respuesta=$pub->insertarLibroEditorial($id_editorial,$id_libro);
                }
                $respuesta ? $mensaje="Publicacion registrada" : $mensaje="Publicacion no ha sido registrada";
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            }else if($tipo==4){
                $respuesta=$pub->insertarOtraPub($tipo_pub,$desc_pub,$usuario);
                $respuesta ? $mensaje="Publicacion registrada" : $mensaje="Publicacion no ha sido registrada";
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);

            }
         }
        break;
    case 'update':
        $respuesta=false;
        if($tipo==1||$tipo==2||$tipo==3){
            $respuesta=$pub->editar($nombre,$autores,$anio,$estado,$id_pub);
            if($tipo==1||$tipo==2){
                $respuesta=$pub->editarArtRev($id_pub,$titulo,$indizacion,$fac_imp,$issn);
            }else{
                $respuesta=$pub->editarLibro($id_pub,$tipo_lib,$rol,$ref_ext,$traduccion,$lugar);           
            }
        }else{
            $respuesta=$pub->editarOtraPub($tipo_pub,$desc_pub,$id_pub);
        }
        $mensaje=$respuesta ? 'Publicación editada' : 'Publicación no ha sido editada'; 
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);   
        break;
        //mostrar publicaciones
    case 'read_articulos':
        $respuesta=$pub->mostrarArtRev($usuario);
        
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
    case 'read_art_rev_id':
        $resp=$pub->mostrarArtRevId($id_pub);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_libro_id':
        $resp=$pub->mostrarLibroId($id_pub);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_libros':
        $respuesta=$pub->mostrarLibros($usuario);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_otro_art':
        $respuesta=$pub->mostrarOtrPub($usuario);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_otro_art_id':
        $respuesta=$pub->mostrarOtrPubId($id_pub);
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_edit':
        $resp=$pub->cargarEditorial($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_edit_libro':
        $resp=$pub->cargarEditLib($libro);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    
    case'delete':
        $respuesta=$pub->eliminar($id_pub);
        $respuesta ? $mensaje="Publicación Eliminada" : $mensaje="Publicación no ha sido eliminada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case'delete_otra_pub':
        $respuesta=$pub->eliminarOtraPub($id_pub);
        $respuesta ? $mensaje="Publicación Eliminada" : $mensaje="Publicación no ha sido eliminada";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        }

?>