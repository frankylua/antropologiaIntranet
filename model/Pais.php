<?php
require "../config/conexion.php";
Class Pais {
    public function __construct(){
    }
    public function mostrarConsultaOrdenada(){
    //consultar paises Argentina | Brasil | Bolivia | Chile | Colombia | Ecuador | Paraguay | Perú | Uruguay | Venezuela
    $sql1="SELECT * FROM pais WHERE id_pais=13 OR id_pais=76 OR id_pais=46 OR id_pais=52 OR id_pais=66 OR id_pais=172 OR id_pais=173 OR id_pais=229 OR id_pais=33";
    $sql2="SELECT * FROM pais WHERE id_pais!=13 OR id_pais!=76 OR id_pais!=46 OR id_pais!=52 OR id_pais!=66 OR id_pais!=172 OR id_pais!=173 OR id_pais!=229 OR id_pais!=33";
    $respuesta1= ejecutarConsultaResultados($sql1);
    $respuesta2= ejecutarConsultaResultados($sql2);
    $paises = array_merge($respuesta1,$respuesta2);
    return $paises;
}

}
?>
    

   
    

