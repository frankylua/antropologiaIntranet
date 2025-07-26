<?php
require "../config/conexion.php";
Class Congreso {
    public function __construct(){
    }
    //insert
    public function insertarCong($nombre,$ciudad,$fech_in,$fech_ter){
        $sql="INSERT INTO congreso (id_congreso,nombre,ciudad,fecha_inicio,fecha_termino) VALUES (NULL,'$nombre','$ciudad','$fech_in','$fech_ter')";
        return obtenerIdConsulta($sql);
    }

    public function insertarPart($tipo_part,$tipo_cong,$coautores,$nom_mesa,$comen_pon,$congreso,$autor,$id_autor,$id_coautor){
      $sql="INSERT INTO participacion (id_participacion,tipo_part,tipo_cong,otros_org,nombre_mesa,coment_ponenc,nom_aut,id_aut,id_coaut,congreso) VALUES (NULL,'$tipo_part','$tipo_cong'," . (empty($coautores)? "NULL":"'$coautores'"). ",'$nom_mesa','$comen_pon'," . (empty($autor)? "NULL":"'$autor'"). "," . (empty($id_autor)? "NULL":"'$id_autor'"). "," . (empty($id_coautor)? "NULL":"'$id_coautor'"). ",'$congreso')";
        return ejecutarConsulta($sql);
    }

    //buscar cogresos registrados
    public function cargarNomCong($busqueda){        
        $sql="SELECT id_congreso, nombre FROM congreso WHERE nombre LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }
    public function cargarCong($id){        
        $sql="SELECT * FROM congreso WHERE id_congreso='$id'";
        return ejecutarConsultaResultados($sql);        
    }
    //buscar participaciones registradas
    public function cargarNomMesa($busqueda,$tipo_part,$id_congreso){        
        $sql="SELECT id_participacion, nombre_mesa FROM participacion WHERE  tipo_part='$tipo_part' AND congreso='$id_congreso' AND (id_coaut IS NULL OR id_aut IS NULL)  AND nombre_mesa LIKE '".$busqueda."%'";
        return ejecutarConsultaResultados($sql);        
    }

    public function cargarPart($id){        
        $sql="SELECT * FROM participacion  WHERE id_participacion='$id'";
        return ejecutarConsultaResultados($sql);        
    }
    //update
    public function editarAutor($id_autor,$part){
        $sql="UPDATE participacion SET nom_aut=NULL, id_aut='$id_autor' WHERE id_participacion='$part' ";
        return ejecutarConsulta($sql);
    }
    public function editarCoautor($id_coautor,$part){
        $sql="UPDATE participacion SET id_coaut='$id_coautor' WHERE id_participacion='$part' ";
        return ejecutarConsulta($sql);
    }
    // public function editar($id,$nombre){
    //     $sql="UPDATE pueblo SET nombre='$nombre' where id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
    public function mostrar($usuario){
         $sql="SELECT * FROM congreso c INNER JOIN participacion p ON c.id_congreso=p.congreso  WHERE  p.id_aut='$usuario' OR p.id_coaut='$usuario' order by c.nombre";
         return ejecutarConsultaResultados($sql);
    }
    public function mostrarAutor($usuario){
        $sql="SELECT nombres,ap_pat,ap_mat FROM usuario WHERE id_usuario='$usuario'";
        return ejecutarConsultaResultados($sql);
   }

    // public function eliminar($id){
    //     $sql="DELETE FROM pueblo  WHERE id_pueblo='$id'";
    //     return ejecutarConsulta($sql);
    // }
}
?>