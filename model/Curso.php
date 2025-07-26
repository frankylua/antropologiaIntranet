<?php
require "../config/conexion.php";
class Curso {
    public function __construct(){
    }
    public function insertar($creditos,$caracter,$periodo,$anio_curso,$carga_hor,$nom_curso,$programa,$docente){
       
        //ingesar curso
        $sql="INSERT INTO curso (id_curso,creditos,caracter,periodo,anio_curso,carga_hor,nom_curso,profesor,arch_prog) VALUES (NULL,'$creditos','$caracter','$periodo','$anio_curso','$carga_hor','$nom_curso','$docente','$programa')";
        return ejecutarConsulta($sql);
        //return true;      

    }

        
    public function editar($id_curso,$nom_curso,$creditos,$caracter,$periodo,$anio_curso,$programa,$car_hor,$docente){
        $sql="UPDATE curso SET creditos='$creditos',caracter='$caracter',periodo='$periodo',anio_curso='$anio_curso',carga_hor='$car_hor',nom_curso='$nom_curso', arch_prog='$programa', profesor='$docente' where id_curso='$id_curso'";
        return ejecutarConsulta($sql);
        
    }
    public function mostrarPorId($id_curso){
        $sql="SELECT * FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso JOIN usuario u ON c.profesor=u.id_usuario  WHERE c.id_curso='$id_curso'";
        return resultadoConsultaPorId($sql);
    }
    public function buscarArchivo($nom_arch){
        $sql="SELECT * FROM curso WHERE arch_prog='$nom_arch'";
        return ejecutarConsultaResultados($sql);
    }
    public function buscarArchivoId($id_curso){
        $sql="SELECT arch_prog FROM curso WHERE id_curso='$id_curso'";
        return ejecutarConsultaResultados($sql);
    }    
    public function mostrar(){
         
        $sql="SELECT c.id_curso,n.nom_curso,c.periodo,c.anio_curso FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  ORDER BY c.anio_curso DESC";
        return ejecutarConsultaResultados($sql);
        
    }

    public function mostrarCursos(){
         
        $sql="SELECT * FROM nombre_curso  ORDER BY nom_curso";
        return ejecutarConsultaResultados($sql);
        
    }

    public function eliminar($id_curso){
        $sql="DELETE FROM curso  WHERE id_curso ='$id_curso'";
        return ejecutarConsulta($sql);

    }
       
  
}
?>
