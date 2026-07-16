<?php
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Pais;
require 'validaciones.php';
$pais=new Pais();
$op=isset($_POST['op'])?$_POST['op']:'';
if($op){
     $respuesta=$pais->mostrarConsultaOrdenada();
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

}


 
