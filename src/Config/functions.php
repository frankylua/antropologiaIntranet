<?php

function limpiar_datos($datos){
    $datos= trim($datos);
    $datos= stripslashes($datos);
    $datos= htmlspecialchars($datos);
    $datos= filter_var($datos,FILTER_SANITIZE_STRING);
    $datos= strtolower($datos);
    return $datos;
}
//  function comprobar_session(){
//     if(isset($_SESSION['admin'])){
//         header('Location: index.php');
//     }
//  }
//  function comprobar_session_admin($location){
//     if(isset($_SESSION['admin'])){
//         require $location;
//     }else{
//         header('Location:../index.php');
//     }
// }
// function comprobar_session_comite($location){
//     if(isset($_SESSION['comite']) || isset($_SESSION['admin'])){
//         require $location;
//     }else{
//         header('Location: '. RUTA);
//     }
// }

// function mostrar_perfil_admin($conexion,$id_login){
//     $statement=$conexion->prepare('SELECT * FROM login l INNER JOIN admin a ON l.id_login = a.id_login AND a.id_login = :id_login');
//     $statement ->execute(array(':id_login' => $id_login));
//     $resultado= $statement->fetch();
//     return $resultado;
// }

// function id($id){
//     return(int)$id;
// }

// function validar_correo($correo, $conexion){
//     $statement=$conexion->prepare('SELECT * FROM login WHERE correo = :correo LIMIT 1');
//     $statement ->execute(array(':correo' => $correo));
//     $resultado= $statement->fetch();
//     return $resultado;
// }
//  function rol_id(){
//      if(isset($_SESSION['admin'])){
//         $id_login = $_SESSION['admin'];
//         return $id_login;
//      }
//      if(isset($_SESSION['comite'])){
//         $id_login = $_SESSION['comite'];
//         return $id_login;
//      }
//  }

?>
    
    


        

       



