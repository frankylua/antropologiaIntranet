<?php
if (strlen(session_id()) < 1) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Identity\Cardinality;
use App\Identity\IdentityResolver;
use App\Model\Login;

//require('validaciones.php');
$login= new Login();
$correo=isset($_POST['correo'])?$_POST['correo']:"";
$pass=isset($_POST['pass'])?$_POST['pass']:"";
$mensaje='';
$_SESSION['capacidades'] = [];
if(isset($correo) && isset($pass)){
    $permisos=$login->validarPermiso($correo,$pass); //funcion para validar permisos desde la base de datos retorna areglos con permisos    
    $identityResolution = null;
    if (!empty($permisos)) {
        $authenticatedLoginId = isset($permisos[0]['id_login']) ? (int) $permisos[0]['id_login'] : 0;
        if ($authenticatedLoginId > 0) {
            try {
                $identityResolver = new IdentityResolver(conexion());
                $identityResolution = $identityResolver->resolveByLoginId($authenticatedLoginId);

                $legacyStudentSignal = false;
                $legacyProfessorSignal = false;
                foreach ($permisos as $permisoHistorico) {
                    $legacyStudentSignal = $legacyStudentSignal || (int) $permisoHistorico['id_permiso'] === 5;
                    $legacyProfessorSignal = $legacyProfessorSignal || (int) $permisoHistorico['id_permiso'] === 4;
                }

                $identityEventCodes = [
                    'LOGIN_NO_ENCONTRADO' => 'IDENTITY_LOGIN_NO_ENCONTRADO',
                    'LOGIN_CON_MULTIPLES_USUARIOS' => 'IDENTITY_LOGIN_MULTIPLE_USERS',
                    'USUARIO_CON_MULTIPLES_ESTUDIANTES' => 'IDENTITY_USER_MULTIPLE_STUDENTS',
                    'USUARIO_CON_MULTIPLES_PROFESORES' => 'IDENTITY_USER_MULTIPLE_PROFESSORS',
                    'TIPO_EST_INVALIDO' => 'IDENTITY_INVALID_STUDENT_STATUS',
                ];
                foreach ($identityResolution->inconsistencies() as $inconsistency) {
                    if (isset($identityEventCodes[$inconsistency])) {
                        error_log($identityEventCodes[$inconsistency]);
                    }
                }

                if ($identityResolution->studentCardinality() === Cardinality::SINGLE && !$legacyStudentSignal) {
                    error_log('IDENTITY_STUDENT_WITHOUT_LEGACY_ROLE');
                } elseif ($identityResolution->userCardinality() === Cardinality::SINGLE && $identityResolution->studentCardinality() === Cardinality::NONE && $legacyStudentSignal) {
                    error_log('IDENTITY_LEGACY_STUDENT_WITHOUT_RELATION');
                }

                if ($identityResolution->professorCardinality() === Cardinality::SINGLE && !$legacyProfessorSignal) {
                    error_log('IDENTITY_PROFESSOR_WITHOUT_LEGACY_ROLE');
                } elseif ($identityResolution->userCardinality() === Cardinality::SINGLE && $identityResolution->professorCardinality() === Cardinality::NONE && $legacyProfessorSignal) {
                    error_log('IDENTITY_LEGACY_PROFESSOR_WITHOUT_RELATION');
                }
            } catch (\PDOException | \InvalidArgumentException $exception) {
                error_log('IDENTITY_RESOLUTION_FAILED');
            }
        } else {
            error_log('IDENTITY_RESOLUTION_FAILED');
        }
    }
    foreach($permisos as $permiso){  
        $_SESSION['login']=$permiso['id_login'];    
       if($permiso['id_permiso'] == 1){
            $_SESSION['admin']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 2){
            $_SESSION['comite']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 3){// este permiso es aquel que se ha ingresado en el sistema con permiso de docente o estudiante aceptado
            $_SESSION['aceptado']=$permiso['id_login'];
        }
        if($permiso['id_permiso']== 4){
            $_SESSION['docente']=$permiso['id_login'];
            //creo la variable de sesion con el id usuario
            $id_login=(int)$permiso['id_login'];
            
        }
        
        if($permiso['id_permiso']== 5){
            $_SESSION['estudiante']=$permiso['id_login'];
            // $id_usuario=$login->retornarIdUsu($id_login);
            // $_SESSION['id_usuario']=$id_usuario;
        }
    }

    if (!empty($permisos) && isset($_SESSION['login'])) {
        $estadosAcademicos = $login->obtenerEstadosAcademicosPorLogin($_SESSION['login']);
        if (is_array($estadosAcademicos) && count($estadosAcademicos) === 1 && in_array((int) $estadosAcademicos[0]['tipo_est'], [1, 2, 3, 4, 5, 7], true)) {
            $_SESSION['capacidades'][] = 'reglamento.ver';
        } elseif (is_array($estadosAcademicos) && count($estadosAcademicos) > 1) {
            error_log('AUTHORIZATION_STATE_AMBIGUOUS');
        }
    }
}
    if(isset($_SESSION['docente'])||isset($_SESSION['estudiante'])){
            $id_usuario=$login->retornarIdUsu($_SESSION['login']);
            $_SESSION['id_usuario']=$id_usuario;
    }
    echo json_encode($permisos, JSON_UNESCAPED_UNICODE);
    
    
?>
