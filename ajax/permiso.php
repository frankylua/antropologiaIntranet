<?php
require_once "../model/Permiso.php";
require "validaciones.php";
$permiso= new Permiso();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
   
    case 'read':
        $respuesta=$permiso->listar();
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
  
        }

?>