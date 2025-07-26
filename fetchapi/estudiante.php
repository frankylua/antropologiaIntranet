<?php
// require "usuario.php";
// require ("../model/Estudiante.php");
// $est=new Estudiante();
// $promedio=floatval($_POST['promedio']);
// $trabaja=(int)$_POST['trabaja'];
// $orientacion=(int)$_POST['orientacion'];
// $sit_ocup=isset($_POST['sit_ocup'])?limpiar_datos($_POST['sit_ocup']):"";
// $profesor=(int)$_POST['profesor'];
// $fech_ing=isset($_POST['fech_ing'])?limpiar_datos($_POST['fech_ing']):"";
// $fech_grad=isset($_POST['fech_grad'])?limpiar_datos($_POST['fech_grad']):"";
// $tipo_est=(int)$_POST['tipo'];
// $op=$_POST['op'];
// $lineaInv=$_POST['lineaInv'];

// $resp=$est->mostrarTipo();
$data = array(
    "nombre" => "Juan",
    "edad" => 30,
    "ciudad" => "Madrid"
);
echo json_encode($data, JSON_UNESCAPED_UNICODE);
//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
// switch($op){
//     case'read_tipo':
        // break;
    // case'read_prof':
    //     $resp=$est->mostrarProf($busqueda);
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;
    // case'read-est':
    //     $resp=$est->mostrarEst();
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;
    // case'read_est_id':
    //     $resp=$est->mostrarEstId($id_usu);
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;
    // case'insert-update':
    //     $login=$est->insertarLogin($correo,$pass);
    //     foreach ($permiso as $per) {
    //         $per=(int)$per;
    //         $est->insertarPermisos($login,$per);
    //     }
    //     $usuario=$est->insertarUsuario($pueblo,$pais_res,$pais_nac,$login,$fech_nac,$nombres,$ap_mat,$ap_pat,$documento,$nro_doc,$inst,$genero,$region,$comuna,$telefono,$direccion,$cont_em,$tel_em);
    //     $estudiante=$est->insertar($profesor,$tipo_est,$fech_ing,$fech_grad,$promedio,$trabaja,$orientacion,$sit_ocup,$usuario);
    //     foreach ($lineaInv as $linea) {
    //         $est->insertarLineaInv($usuario,$linea);
    //     }
    //     $estudiante ? $mensaje="<p>Usuario guardado correctamente</p>" : $mensaje="<p>Usuario no ha sido</p>";
    //     $arr_insert=[$mensaje,$usuario];
    //     echo json_encode($arr_insert, JSON_UNESCAPED_UNICODE);
    //     break;
    // case'val-mail':
    //     $resp=$est->validarCorreo($correo);
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;
    // case'delete':
    //     $respuesta=$est->eliminar($id_login);
    //     $respuesta ? $mensaje="<p>Usuario Eliminado</p>" : $mensaje="<p>Usuario no ha sido eliminado</p>";
    //     echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
    // break;


// }
?>