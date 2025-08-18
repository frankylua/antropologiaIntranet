<?php
require_once "../model/Pais.php";
require "validaciones.php";
$pais=new Pais();
$op=isset($_POST['op'])?$_POST['op']:'';
if($op){
     $respuesta=$pais->mostrarConsultaOrdenada();
     var_dump('respuesta de pueblo');
        var_dump($respuesta);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

}


 
