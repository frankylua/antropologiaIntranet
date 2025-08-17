<?php
function limpiar_datos($datos){
    $datos= trim($datos);
    $datos= stripslashes($datos);
    $datos= htmlspecialchars($datos);
    $datos= filter_var($datos,FILTER_SANITIZE_SPECIAL_CHARS);
    $datos= strtolower($datos);
    return $datos;
}