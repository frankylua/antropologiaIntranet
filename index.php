<?php
if (strlen(session_id()) < 1) {
    session_start();//Validamos si existe o no la sesión
}
// direccionamiento segun permisos de inicio de sesion
//direccionamiento segun permisos de inicio de sesion


?>
<!-- // if(isset($_SESSION['admin']) or isset($_SESSION['comite'])){
//     header('Location:admin/inicio.php');
// }else if(isset($_SESSION['aceptado'])){
//     header('Location:form-doc/calend.acad.php');
// }else if(isset($_SESSION['docente'])){
//     header('Location:form-doc/info.docente.php');
// }
// else if(isset($_SESSION['estudiante'])){
//     header('Location:form-doc/info.estudiante.php');
// }

// else{
//     header('Location:form-doc/login.php');
// } -->


