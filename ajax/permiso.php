<?php
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Permiso;
require 'validaciones.php';
$permiso= new Permiso();
$op=isset($_POST['op'])?$_POST['op']:'';
switch($op){
   
    case 'read':
        $respuesta=$permiso->listar();
         echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
         break;
  
        }

?>