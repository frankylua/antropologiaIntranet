<?php
require "../config/conexion.php";
Class Publicacion {
    public function __construct(){
    }
    public function insertar($usuario,$nombre,$autores,$anio,$estado){
        $sql="INSERT INTO publicacion (id_publicacion,nombre,otros_autores,anio,estado,usuario) VALUES (NULL,'$nombre'," . (empty($autores)? "NULL":"'$autores'"). ",'$anio','$estado','$usuario')";
        return obtenerIdConsulta($sql);
    }
    public function insertarArtRev($publicacion,$tipo,$titulo,$indizacion,$fac_imp,$issn){
        $sql="INSERT INTO articulo_revista (id_art_rev,tipo,titulo,issn,indizacion,factor_impacto,publicacion) VALUES (NULL,'$tipo','$titulo','$issn','$indizacion','$fac_imp','$publicacion')";
        return ejecutarConsulta($sql);
    }
    public function insertarLibro($publicacion,$tipo,$rol,$ref_ext,$traduccion,$lugar){
        $sql="INSERT INTO libro (id_libro,tipo,rol,ref_ext,traduccion,lugar,publicacion) VALUES (NULL,'$tipo','$rol','$ref_ext','$traduccion','$lugar','$publicacion')";
        return obtenerIdConsulta($sql);
    }
    public function insertarEditorial($editorial){
        if(is_numeric($editorial)){
            return (int)$editorial;
        }else{
            $sql="INSERT INTO editorial (id_editorial,nombre) VALUES (NULL,'$editorial')";
            return obtenerIdConsulta($sql);
        }
    }   
    public function insertarLibroEditorial($id_editorial,$id_libro){
        $sql="INSERT INTO libro_editorial (id_lib_ed,editorial,libro) VALUES (NULL,'$id_editorial','$id_libro')";
        return ejecutarConsulta($sql);
    }
    public function insertarOtraPub($tipo_pub,$desc_pub,$usuario){
        $sql="INSERT INTO otra_publicacion (id_otra_pub,tipo,descripcion,usuario) VALUES (NULL,'$tipo_pub','$desc_pub','$usuario')";
        return ejecutarConsulta($sql);
    }
    public function editar($nombre,$autores,$anio,$estado,$id_pub){
        $sql="UPDATE publicacion SET nombre='$nombre',otros_autores=" . (empty($autores)? "NULL":"'$autores'"). ",anio='$anio', estado='$estado' where id_publicacion='$id_pub'";
        return ejecutarConsulta($sql);
    }
    public function editarArtRev($id_pub,$titulo,$indizacion,$fac_imp,$issn){
        $sql="UPDATE articulo_revista SET titulo='$titulo', issn='$issn', indizacion='$indizacion', factor_impacto='$fac_imp' where publicacion='$id_pub'";
        return ejecutarConsulta($sql);
    }
    public function editarLibro($id_pub,$tipo_lib,$rol,$ref_ext,$traduccion,$lugar){
        $sql="UPDATE libro SET tipo='$tipo_lib', rol='$rol', ref_ext='$ref_ext', traduccion='$traduccion', lugar='$lugar'  where publicacion='$id_pub'";
        return ejecutarConsulta($sql);
    }
    public function editarOtraPub($tipo_pub,$desc_pub,$id_pub){
        $sql="UPDATE otra_publicacion SET tipo='$tipo_pub', descripcion='$desc_pub' where id_otra_pub='$id_pub'";
        return ejecutarConsulta($sql);
    }
    //buscar editoriales
    public function cargarEditorial($busqueda){        
        $sql="SELECT id_editorial, nombre FROM editorial WHERE nombre LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    public function cargarEditLib($libro){
        $sql="SELECT * FROM editorial e INNER JOIN libro_editorial l ON l.editorial=e.id_editorial WHERE l.libro='$libro'";
        return ejecutarConsultaResultados($sql);
    }
    public function mostrarArtRev($usuario){
         //consultar pueblos
         $sql="SELECT p.nombre,p.otros_autores,p.anio,e.nombre,a.tipo,a.titulo,a.issn,i.nombre,a.factor_impacto,p.id_publicacion FROM publicacion p JOIN articulo_revista a ON p.id_publicacion=a.publicacion JOIN usuario u ON p.usuario=u.id_usuario JOIN indizacion i ON a.indizacion=i.id_ind JOIN estado_pub e ON p.estado=e.id_est_pub WHERE  p.usuario='$usuario' ";
         return ejecutarConsultaResultados($sql);
    }
    // mostrar articulos por id
    public function mostrarArtRevId($id_pub){
        
        $sql="SELECT p.nombre,p.otros_autores,p.anio,p.estado,a.tipo,a.titulo,a.issn,a.indizacion,a.factor_impacto,p.id_publicacion FROM publicacion p JOIN articulo_revista a ON p.id_publicacion=a.publicacion   WHERE  p.id_publicacion='$id_pub' ";
        return ejecutarConsultaResultados($sql);
   }
    public function mostrarLibros($usuario){
        
        $sql="SELECT p.nombre AS nom_pub,p.otros_autores,p.anio,e.nombre AS nom_est,l.id_libro,l.tipo,l.rol,l.ref_ext,l.traduccion,l.lugar,p.id_publicacion FROM publicacion p JOIN libro l ON p.id_publicacion=l.publicacion JOIN usuario u ON p.usuario=u.id_usuario JOIN estado_pub e ON p.estado=e.id_est_pub WHERE p.usuario='$usuario' ";
        return ejecutarConsultaResultados($sql);
    }
    public function mostrarLibroId($id_pub){
        
        $sql="SELECT p.nombre AS nom_pub,p.otros_autores,p.estado,p.anio,l.id_libro,l.tipo,l.rol,l.ref_ext,l.traduccion,l.lugar,p.id_publicacion,e.nombre as nom_ed ,e.id_editorial FROM publicacion p JOIN libro l ON p.id_publicacion=l.publicacion JOIN libro_editorial le ON le.libro=l.id_libro JOIN editorial e ON le.editorial=e.id_editorial  WHERE p.id_publicacion='$id_pub' ";
        return ejecutarConsultaResultados($sql);
   }
    public function mostrarOtrPub($usuario){
        //consultar pueblos
        $sql="SELECT * FROM otra_publicacion  WHERE usuario='$usuario' ";
        return ejecutarConsultaResultados($sql);
    }
    public function mostrarOtrPubId($id_pub){
        //consultar pueblos
        $sql="SELECT * FROM otra_publicacion  WHERE id_otra_pub='$id_pub' ";
        return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id_pub){
        $sql="DELETE FROM publicacion  WHERE id_publicacion='$id_pub'";
        return ejecutarConsulta($sql);
    }
    public function eliminarOtraPub($id_pub){
        $sql="DELETE FROM otra_publicacion  WHERE id_otra_pub='$id_pub'";
        return ejecutarConsulta($sql);
    }
}
?>