<?php
require "../model/Curso.php";
require "validaciones.php";
$curso=new Curso();

$nom_curso=isset($_POST['nombre'])?(int)$_POST['nombre']:'';
$creditos=isset($_POST['creditos'])?limpiar_datos($_POST['creditos']):"";
$docente=isset($_POST['docente'])?limpiar_datos($_POST['docente']):null;
$car_hor=isset($_POST['carga_hor'])?(int)$_POST['carga_hor']:'';
$caracter=isset($_POST['caracter'])?(int)$_POST['caracter']:'';
$periodo=isset($_POST['periodo'])?(int)$_POST['periodo']:'';
$anio_curso=isset($_POST['anio_curso'])?(int)$_POST['anio_curso']:'';
$id_curso=isset($_POST['id_curso'])?(int)$_POST['id_curso']:'';
$arch_actual=isset($_POST['arch_actual'])?$_POST['arch_actual']:'';
$op=isset($_POST['op'])?$_POST['op']:'';

// guardar archivo programa curso
// if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
// 		{
// 			$imagen=$_POST["imagenactual"];
// 		}
// 		else 
// 		{
			// $ext = explode(".", $_FILES["prog_curso"]["name"]);
			// if ($_FILES['prog_curso']['type'] == "image/jpg" || $_FILES['prog_curso']['type'] == "image/jpeg" || $_FILES['prog_curso']['type'] == "image/png")
			// {
			// 	$programa = round(microtime(true)) . '.' . end($ext);
			// 	move_uploaded_file($_FILES["prog_curso"]["tmp_name"], "../files/programa_curso/" . $programa);
			// }
		//}

// $lista=array('creditos'=>$creditos,'caracter'=>$caracter,'periodo'=>$periodo,'año'=>$anio_curso,'carga'=>$carga_hor,'nombre'=>$nombre,'programa'=>$programa);
switch($op){
        case 'insert-update':
                $dir= '../files/prog_curso';//ruta para guardar archivo programa curso
                $nom_prog=basename($_FILES['prog_curso']['name']);
                $archivos=$curso->buscarArchivo($nom_prog);                
                $nom_prog=count($archivos)==0?$nom_prog:rand(00,99).'-'.$nom_prog; //modifico nombre en caso de existir el nombre de archivo en la base de datos 
                $ruta_carga=$dir.'/'.$nom_prog;
                $arch_tmp=$_FILES['prog_curso']['tmp_name'];
                if($id_curso == 0 ){
                        if (!file_exists($dir)) {
                                mkdir($dir, 0777);
                            }
                        if(move_uploaded_file($arch_tmp,$ruta_carga)){                                
                                $respuesta=$curso->insertar($creditos,$caracter,$periodo,$anio_curso,$car_hor,$nom_curso,$nom_prog,$docente);
                        }else{
                                $respuesta=false;
                        }
                        $respuesta ? $mensaje="<p>Curso guardado correctamente</p>" : $mensaje="<p>Curso no ha sido guardado</p>";
                        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                }
                else{
                        if (file_exists($_FILES['prog_curso']['tmp_name']) || is_uploaded_file($_FILES['prog_curso']['tmp_name'])){
                                if(move_uploaded_file($arch_tmp,$ruta_carga)){
                                        unlink($dir.'/'.$_POST['arch_actual']);
                                        $respuesta=$curso->editar($id_curso,$nom_curso,$creditos,$caracter,$periodo,$anio_curso,$nom_prog,$car_hor,$docente);
                                }else{
                                        $respuesta=false;
                                }
                         }
                         else{
                                $programa=$_POST['arch_actual'];
                                $respuesta=$curso->editar($id_curso,$nom_curso,$creditos,$caracter,$periodo,$anio_curso,$programa,$car_hor,$docente);
                        }                       
                        $respuesta ? $mensaje="<p>Curso editado correctamente</p>" : $mensaje="<p>Curso no ha sido editado</p>";
                        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
                }
        break;
        case 'read':
                $respuesta=$curso->mostrar();
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        case 'read_cursos':
             $respuesta=$curso->mostrarCursos();
            echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    break;
        case 'query_id':
                $respuesta=$curso->mostrarPorId($id_curso);
                echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        break;
        case'delete':
                $nom_prog=$curso->buscarArchivoId($id_curso);
                $respuesta=$curso->eliminar($id_curso);
                if($respuesta){
                        unlink('../files/prog_curso/'.$nom_prog[0][0]);
                }
                $respuesta ? $mensaje="<p>Curso Eliminado</p>" : $mensaje="<p>Curso no ha sido eliminado</p>";
                echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
      }



?>