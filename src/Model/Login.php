<?php
declare(strict_types=1);
namespace App\Model;

class Login 
 {
  
    
    public function __construct(){

    }
    public function insertarLogin($correo,$pass){
        $pdo = conexion();
        $q = $pdo->prepare('INSERT INTO login (correo,pass) VALUES (:correo,:pass)');
        $q->execute(['correo'=>$correo,'pass'=>\App\Security\PasswordCredentials::hash($pass)]);
        return $pdo->lastInsertId();
    }

   
    public function editarLogin($id_login,$correo,$pass=null){
        // null conserva la contraseña sin leerla ni reenviarla.
        $parametros = ['correo' => $correo, 'id_login' => $id_login];
        $sql = 'UPDATE login SET correo=:correo';
        if ($pass !== null) {
            $sql .= ',pass=:pass';
            $parametros['pass'] = \App\Security\PasswordCredentials::hash($pass);
        }
        $consulta = conexion()->prepare($sql . ' WHERE id_login=:id_login');
        $consulta->execute($parametros);
        return $consulta;
    }
    
    public function eliminar($id_login){
        $sql="DELETE FROM login  WHERE id_login ='$id_login'";
        return ejecutarConsulta($sql);

    }

    public function validarPermiso($correo,$pass){
        $pdo = conexion();
        $id = \App\Security\PasswordCredentials::find($pdo, $correo, $pass);
        if ($id === null || !\App\Security\PasswordCredentials::finalize($pdo, $id, $pass)) return [];
        $q = $pdo->prepare('SELECT id_login,id_permiso FROM permiso_login WHERE id_login=:id_login');
        $q->execute(['id_login'=>$id]);
        return $q->fetchAll(\PDO::FETCH_ASSOC);
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
    public function obtenerActividadPorLogin(int $idLogin): array {
        $pdo=conexion();
        $queries=[
            'usuarios'=>'SELECT id_usuario FROM usuario WHERE login=:login',
            'estudiantes'=>'SELECT e.tipo_est FROM usuario u JOIN estudiante e ON e.usuario=u.id_usuario WHERE u.login=:login',
            'profesores'=>'SELECT p.estado_profesor FROM usuario u JOIN profesor p ON p.usuario=u.id_usuario WHERE u.login=:login',
        ];
        $result=[];
        foreach ($queries as $key=>$sql) {
            $q=$pdo->prepare($sql); $q->execute(['login'=>$idLogin]);
            $result[$key]=$q->fetchAll(\PDO::FETCH_COLUMN);
        }
        return $result;
    }
}
?>
