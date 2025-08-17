<?php
function limpiar_datos($datos){
    $datos= trim($datos);
    $datos= stripslashes($datos);
    $datos= htmlspecialchars($datos);
    $datos= strtolower($datos);
    return $datos;
}