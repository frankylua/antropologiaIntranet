<?php
require "validaciones.php";
$nombres=isset($_POST['nombres'])?limpiar_datos($_POST['nombres']):"";
$ap_mat=isset($_POST['ap_mat'])?limpiar_datos($_POST['ap_mat']):"";
$ap_pat=isset($_POST['ap_pat'])?limpiar_datos($_POST['ap_pat']):"";
$fech_nac=isset($_POST['fech_nac'])?$_POST['fech_nac']:'';
$documento=isset($_POST['documento'])?(int)$_POST['documento']:'';
$nro_doc=isset($_POST['nro_doc'])?limpiar_datos($_POST['nro_doc']):"";
$pais_nac=isset($_POST['pais_nac'])?(int)$_POST['pais_nac']:'';
$genero=isset($_POST['genero'])?(int)$_POST['genero']:'';
$pais_res=isset($_POST['pais_res'])?(int)$_POST['pais_res']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$region=isset($_POST['region'])?limpiar_datos($_POST['region']):"";
$comuna=isset($_POST['comuna'])?limpiar_datos($_POST['comuna']):"";
$telefono=isset($_POST['telefono'])?limpiar_datos($_POST['telefono']):"";
$direccion=isset($_POST['direccion'])?limpiar_datos($_POST['direccion']):"";
$cont_em=isset($_POST['cont_em'])?limpiar_datos($_POST['cont_em']):"";
$tel_em=isset($_POST['tel_em'])?limpiar_datos($_POST['tel_em']):"";
$correo=isset($_POST['correo'])?limpiar_datos($_POST['correo']):"";
$pass=isset($_POST['pass'])?limpiar_datos($_POST['pass']):"";
$inst=isset($_POST['inst'])?(int)$_POST['inst']:'';
$pueblo=isset($_POST['pueblo'])?(int)$_POST['pueblo']:'';
$permiso=isset($_POST['permisos'])?$_POST['permisos']:'';
$id_usu=isset($_POST['id_usu'])?(int)$_POST['id_usu']:'';
$id_login=isset($_POST['id_login'])?(int)$_POST['id_login']:'';
$tipo = isset ($_POST['tipo']) ? (int) $_POST['tipo'] : '';





