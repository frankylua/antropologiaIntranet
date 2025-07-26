<?php
session_start();
require "usuario.php";
require ("../model/Estudiante.php");
$est = new Estudiante();
$promedio = isset($_POST['promedio']) ? floatval($_POST['promedio']) : '';
$trabaja = isset($_POST['trabaja']) ? (int) $_POST['trabaja'] : '';
$orientacion = isset($_POST['orientacion']) ? limpiar_datos($_POST['orientacion']) : "";
$sit_ocup = isset($_POST['sit_ocup']) ? limpiar_datos($_POST['sit_ocup']) : "";
$profesor = isset($_POST['profesor']) ? (int) $_POST['profesor'] : '';
$fech_ing = isset($_POST['fech_ing']) ? limpiar_datos($_POST['fech_ing']) : "";
$fech_grad = isset($_POST['fech_grad']) ? limpiar_datos($_POST['fech_grad']) : "";
$tipo_est = isset($_POST['tipo_est']) ? (int) $_POST['tipo_est'] : '';
$op = isset($_POST['op']) ? $_POST['op'] : '';
$lineaInv = isset($_POST['lineaInv']) ? $_POST['lineaInv'] : '';
$id_usu_prof = isset($_POST['id_usu_prof']) ? (int) $_POST['id_usu_prof'] : '';
$existe_est = isset ($_SESSION['id_usuario']) ?$_SESSION['id_usuario'] : 0;
$id_est=$existe_est!=0?$existe_est[0]['id_usuario']:0;

//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
switch ($op) {
    case 'read_tipo':
        $resp = $est->mostrarTipo();

        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof':
        $resp = $est->mostrarProf($busqueda);

        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_nom_prof':
        $resp = $est->mostrarNomProf($id_usu_prof);

        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_est_prog':
        $resp = $est->mostrarDatosProgEst($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_est_perfil':
         $resp = $est->mostrarEstId($id_est);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;

    case 'read-est':
        $resp = $est->mostrarEst();
        // header('Content-Type: application/json');
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_est_id':
        $resp = $est->mostrarEstId($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-filtrada':
        $resp = $est->mostrarEstTipo($tipo);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-est-filtro':
        $resp = $est->mostrarEstFiltro($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    // case 'read_est_prog':
    //     $resp = $est->mostrarDatosProgEst($id_usu);
    //     echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    //     break;

    case 'insert-update':
        $login = $est->insertarLogin($correo, $pass);
        foreach ($permiso as $per) {
            $per = (int) $per;
            $est->insertarPermisos($login, $per);
        }
        $usuario = $est->insertarUsuario($pueblo, $pais_res, $pais_nac, $login, $fech_nac, $nombres, $ap_mat, $ap_pat, $documento, $nro_doc, $inst, $genero, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em);
        $estudiante = $est->insertar($profesor, $tipo_est, $fech_ing, $fech_grad, $promedio, $trabaja, $orientacion, $sit_ocup, $usuario);
        foreach ($lineaInv as $linea) {
            $est->insertarLineaInv($usuario, $linea);
        }
        $estudiante ? $mensaje = "<p>Usuario guardado correctamente</p>" : $mensaje = "<p>Usuario no ha sido</p>";
        $arr_insert = [$mensaje, $usuario];
        //// validar los ingresos que no se puedan guardar si alguna tabla presenta error (quizas buscar alguna opcion desde sql)

        echo json_encode($arr_insert, JSON_UNESCAPED_UNICODE);
        break;
    case 'update-inf-pers':
        $resp_pers = $est->editarInfPers($nombres, $ap_mat, $ap_pat, $fech_nac, $documento, $nro_doc, $pais_nac, $genero, $pais_res, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em, $correo, $pass, $pueblo, $id_usu, $id_login);
        $resp_log = $est->editarLogin($id_login, $correo, $pass);
        $mensaje = $resp_pers && $resp_log ? 'Datos Editados Correctamente' : 'Los datos no han sido editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'update-inf-prog':
        //editar Permisos
        // if (isset($_SESSION['admin']) || isset($_SESSION['comite'])) {
        //     // editar permisos
        //     foreach ($permiso as $p) {
        //         // logica para buscar el permiso y editar en caso que sea comite o admin
        //         if ($p == 3) {
        //             $acep = $est->buscarPermiso(3, $id_login);
        //             if (!(array) $acep) {
        //                 $est->insertarPermisos($id_login, $p);    
        //             }
        //         }
        //         if ($p == 5) {
        //             // $acep = $est->buscarPermiso(5, $id_login);
        //             // if (!(array) $acep) {
        //                 $est->insertarPermisos($id_login, $p);    
        //             //}
        //         }
        //     }
        //     if (!in_array(3, $permiso)) {
        //         $elim = $est->eliminarPermiso(1, $id_login);
        //     }
        //     if (!in_array(5, $permiso)) {
        //         $est->eliminarPermiso(2, $id_login);
        //     }
        // }
        //editar Linea Inv
        //validar las lineas que no existen en la base de datoa
        foreach ($lineaInv as $linea) {
            //buscar si linea esta guardada
            $existLinea = (array) $est->buscarLinInv($linea, $id_usu);
            $existLinea = $existLinea ? true : false;
            if (!$existLinea) {
                $respuesta = $est->insertarLineaInv($id_usu, $linea);
            }
            //array_push($mensaje,$existLinea);
        }
        $lineasDB = $est->todasLinInv($id_usu);
        //validar linea que existen en la base de dato, pero deben ser eliminadas
        foreach ($lineasDB as $lin) {
            if (!in_array($lin['linea_inv'], $lineaInv)) {
                $est->borrarLineaInv($id_usu, $lin['linea_inv']);
                //array_push($mensaje,'no existe en el nuevo array');
            }
        }
        //editar datos programa
        $resp_est = $est->editarDatProg($id_usu, $profesor,$fech_ing, $fech_grad, $promedio,$trabaja, $orientacion, $sit_ocup );
        $resp_inst = $est->editarInstDoc($id_usu, $inst);
        $mensaje = $resp_est && $resp_inst ? 'Los Datos han sido Editados' : 'Los Datos no han sido Editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'val-mail':
        $resp = $est->validarCorreo($correo);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'delete':
        $respuesta = $est->eliminar($id_login);
        $respuesta ? $mensaje = "<p>Usuario Eliminado</p>" : $mensaje = "<p>Usuario no ha sido eliminado</p>";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
        
    case 'update-permiso-tipo-est':
        $resp;
        $resp=$est->eliminarAcceso($id_login);
        if($tipo_est==1 || $tipo_est==4){
            $resp= $est->agregarPermiso(5,$id_login);
        } 
        if($tipo_est==2 || $tipo_est==3){
            $resp= $est->agregarPermiso(3,$id_login);
            $resp= $est->agregarPermiso(5,$id_login);
        }
        $resp=$est->editarTipoEst($id_usu,$tipo_est);
        $resp? $mensaje = 'Los datos han sido  actualizados': $mensaje = 'Los datos no han podido ser acualizados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;

}
?>