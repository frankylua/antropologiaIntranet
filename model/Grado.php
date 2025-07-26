<?php
require "../config/conexion.php";
Class Grado {
    public function __construct(){
    }
    public function insertar($usuario,$instituto,$titulo,$fecha){
        $sql="INSERT INTO grado_academico (id_grado,usuario,inst_grado,tit_grado,fech_graduacion) VALUES (NULL,'$usuario','$instituto','$titulo','$fecha')";
        return ejecutarConsulta($sql);
    }
    public function editar($id_grado,$inst,$titulo,$fecha){
        $sql="UPDATE grado_academico SET inst_grado='$inst', tit_grado='$titulo',fech_graduacion='$fecha' where id_grado='$id_grado'";
        return ejecutarConsulta($sql);
    }
    public function mostrar($usuario){
         //consultar pueblos
         $sql="SELECT g.id_grado,i.inst,t.tit_grado,t.tipo_grado,g.fech_graduacion FROM grado_academico g JOIN titulo_grado t ON g.tit_grado=t.id_titulo JOIN usuario u ON g.usuario=u.id_usuario JOIN institucion i  ON g.inst_grado=i.id_inst WHERE  g.usuario='$usuario' ORDER BY t.tipo_grado";
         return ejecutarConsultaResultados($sql);
    }
    public function mostrarGrado($id_grado){
        //consultar pueblos
        $sql="SELECT g.id_grado,i.inst,t.tit_grado,t.tipo_grado,g.fech_graduacion,g.inst_grado,g.tit_grado FROM grado_academico g JOIN titulo_grado t ON g.tit_grado=t.id_titulo JOIN usuario u ON g.usuario=u.id_usuario JOIN institucion i  ON g.inst_grado=i.id_inst WHERE  g.id_grado='$id_grado' ORDER BY t.tipo_grado";
        return ejecutarConsultaResultados($sql);
   }
    public function eliminar($id){
        $sql="DELETE FROM grado_academico  WHERE id_grado='$id'";
        return ejecutarConsulta($sql);
    }
}
?>