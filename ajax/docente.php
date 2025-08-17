<?php
require "usuario.php";
require "../model/Docente.php";
$doc = new Docente();
$busqueda = isset ($_POST['busqueda']) ? $_POST['busqueda'] : '';
$inst = isset ($_POST['instTrab']) ? (int) $_POST['instTrab'] : '';
$catAcad = isset ($_POST['catAcad']) ? (int) $_POST['catAcad'] : '';
$anioIng = isset ($_POST['anio_ing']) ? (int) $_POST['anio_ing'] : '';
$vinculo = isset ($_POST['vinculo']) ? (int) $_POST['vinculo'] : '';
$op = isset ($_POST['op']) ? $_POST['op'] : '';
$lineaInv = isset ($_POST['lineaInv']) ? $_POST['lineaInv'] : '';
$prof = isset ($_SESSION['id_usuario']) ?$_SESSION['id_usuario'] : 0;
$id_prof=$prof!=0?$prof[0]['id_usuario']:0;
//echo json_encode($grados, JSON_UNESCAPED_UNICODE);
// *******para ingresar los datos de formularios dinamicos hacer un for con un contador 
// para validar si existe el id del formulario dinamico******
switch ($op) {
    case 'read_prof':
        $resp = $doc->buscarProf($busqueda);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_id':
        $resp = $doc->mostrarProfId($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_perfil':
        $resp = $doc->mostrarProfId($id_prof);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read_prof_prog':
        $resp = $doc->mostrarDatosProg($id_usu);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-doc':
        $resp = $doc->mostrarProf();// muestra todos los docentes 
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'read-filtrada':// lista de los docentes segun vinculo con el programa
        $resp = $doc->mostrarProfTipo($tipo);
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        break;
    case 'insert-update':
        $login = $doc->insertarLogin($correo, $pass);
        
            foreach ($permiso as $per) {
                $per = (int) $per;
                $doc->insertarPermisos($login, $per);
            }
           
        $usuario = $doc->insertarUsuario($pueblo, $pais_res, $pais_nac, $login, $fech_nac, $nombres, $ap_mat, $ap_pat, $documento, $nro_doc, $inst, $genero, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em);
        $docente = $doc->insertar($catAcad, $anioIng, $vinculo, $usuario);
        foreach ($lineaInv as $linea) {
            $doc->insertarLineaInv($usuario, $linea);
        }
        $docente ? $mensaje = "<p>Usuario guardado correctamente</p>" : $mensaje = "<p>Usuario no ha sido guardado</p>";
        $arr_insert = [$mensaje, $usuario];
        echo json_encode($arr_insert, JSON_UNESCAPED_UNICODE);
        break;
    case 'delete':
        $respuesta = $doc->eliminar($id_login);
        $respuesta ? $mensaje = "<p>Usuario Eliminado</p>" : $mensaje = "<p>Usuario no ha sido eliminado</p>";
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'update-inf-pers':
        $resp_pers = $doc->editarInfPers($nombres, $ap_mat, $ap_pat, $fech_nac, $documento, $nro_doc, $pais_nac, $genero, $pais_res, $region, $comuna, $telefono, $direccion, $cont_em, $tel_em, $correo, $pass, $pueblo, $id_usu, $id_login);
        $resp_log = $doc->editarLogin($id_login, $correo, $pass);
        $mensaje = $resp_pers && $resp_log ? 'Datos Editados Correctamente' : 'Los datos no han sido editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;
    case 'update-inf-prog':
        //editar Permisos
        if(isset($_SESSION['admin'])){   
            
            // editar permisos
            foreach ($permiso as $p) {
                // logica para buscar el permiso y editar en caso que sea comite o admin
                if ($p == 1) {
                    $ad = $doc->buscarPermiso(1, $id_login);
                    $co = $doc->buscarPermiso(2, $id_login);
                    $existe = (array) $ad || (array) $co ? true : false;
                    if ($existe) {
                        $doc->editarPermiso($p, $id_login);
                        $editado = 'edita permiso admin';
                    } else {
                        $doc->insertarPermisos($id_login, $p);
                        $insertado = 'inserta permiso admin';
                    }
                }
                if ($p == 2) {
                    $co = $doc->buscarPermiso(2, $id_login);
                    $ad = $doc->buscarPermiso(1, $id_login);
                    $existe = (array) $ad || $co ? true : false;
                    if ($existe) {
                        $doc->editarPermiso($p, $id_login);
                    } else {
                        $doc->insertarPermisos($id_login, $p);
                    }
                }
                if ($p == 3) {
                    $aceptado = $doc->buscarPermiso(3, $id_login);
                    $existe = (array) $aceptado ? true : false;
                    if (!$existe) {
                        $doc->insertarPermisos($id_login, $p);
                    }
                }
                if ($p == 4) {
                    $aceptado = $doc->buscarPermiso(4, $id_login);
                    $existe = (array) $aceptado ? true : false;
                    if (!$existe) {
                        $doc->insertarPermisos($id_login, $p);
                    }
                }
                
            }
            if (!in_array(1, $permiso)) {
                $elim = $doc->eliminarPermiso(1, $id_login);
    
            }
            if (!in_array(2, $permiso)) {
                $doc->eliminarPermiso(2, $id_login);
            }
        }
        //editar Linea Inv
        //validar las lineas que no existen en la base de datoa
        foreach ($lineaInv as $linea) {
            //buscar si linea esta guardada
            $existLinea = (array) $doc->buscarLinInv($linea, $id_usu);
            $existLinea = $existLinea ? true : false;
            if (!$existLinea) {
                $respuesta = $doc->insertarLineaInv($id_usu, $linea);
            }
            //array_push($mensaje,$existLinea);
        }
        $lineasDB = $doc->todasLinInv($id_usu);
        //validar linea que existen en la base de dato, pero deben ser eliminadas
        foreach ($lineasDB as $lin) {
            if (!in_array($lin['linea_inv'], $lineaInv)) {
                $doc->borrarLineaInv($id_usu, $lin['linea_inv']);
                //array_push($mensaje,'no existe en el nuevo array');
            }
        }
        $mensaje = 'lineas_editadas';
        //editar datos programa
        $resp_prof = $doc->editarDatProg($id_usu, $vinculo, $catAcad, $anioIng);
        $resp_inst = $doc->editarInstDoc($id_usu, $inst);
        $mensaje = $resp_prof && $resp_inst ? 'Los Datos han sido Editados' : 'Los Datos no han sido Editados';
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        break;


}