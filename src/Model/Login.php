<?php
declare(strict_types=1);
namespace App\Model;

class Login 
 {
  
    
    public function __construct(){

    }
    public function insertarLogin($correo,$pass){
        $sql="INSERT INTO login (id_login,correo,pass) VALUES (NULL,'$correo','$pass')";
        return obtenerIdConsulta($sql);
    }

   
    public function editarLogin($id_login,$correo,$pass){
        $sql="UPDATE login SET correo = '$correo', pass = '$pass' WHERE id_login = '$id_login'";
        return ejecutarConsulta($sql);
    }
    
    public function eliminar($id_login){
        $sql="DELETE FROM login  WHERE id_login ='$id_login'";
        return ejecutarConsulta($sql);

    }

    public function validarPermiso($correo,$pass){
        $sql="SELECT * FROM login l JOIN permiso_login p WHERE l.id_login=p.id_login AND l.correo='$correo' AND l.pass='$pass'";
        return ejecutarConsultaResultados($sql);
    }

    public function retornarIdUsu($id_login){
        $sql= "SELECT id_usuario FROM login l JOIN usuario u WHERE l.id_login=u.login AND l.id_login='$id_login'";
        return ejecutarConsultaResultados($sql);
        
    }

    public function obtenerEstadosAcademicosPorLogin($id_login){
        $sql="SELECT e.tipo_est FROM usuario u JOIN estudiante e ON u.id_usuario=e.usuario WHERE u.login='$id_login'";
        return ejecutarConsultaResultados($sql);
    }
    public function obtenerEstadosProfesorPorLogin(int $idLogin): array{
        $consulta = conexion()->prepare(
            'SELECT p.estado_profesor FROM usuario u '
            . 'JOIN profesor p ON p.usuario = u.id_usuario '
            . 'WHERE u.login = :id_login'
        );
        $consulta->execute(['id_login' => $idLogin]);
        return $consulta->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
