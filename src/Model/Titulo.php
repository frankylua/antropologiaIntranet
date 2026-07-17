<?php
declare(strict_types=1);
namespace App\Model;

class Titulo {
    public function __construct(){
    }
    public function insertar($nombre,$tipo_grado){
        $sql="INSERT INTO titulo_grado (id_titulo,tit_grado,tipo_grado) VALUES (NULL,'$nombre','$tipo_grado')";
        return ejecutarConsulta($sql);
    }
    public function insertarObtenerId($nombre,$tipo_grado){
        $sql="INSERT INTO titulo_grado (id_titulo,tit_grado,tipo_grado) VALUES (NULL,'$nombre','$tipo_grado')";
        return obtenerIdConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE titulo_grado SET tit_grado='$nombre' where id_titulo='$id'";
        return ejecutarEscritura($sql);
    }
    public function mostrarPorGrado($tipo){
         //consultar pueblos
         $sql="SELECT * FROM titulo_grado WHERE tipo_grado = '$tipo' ORDER BY tit_grado";
         return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id){
        $pdo = conexion();
        $pdo->beginTransaction();

        try {
            $registro = $pdo->prepare('SELECT id_titulo FROM titulo_grado WHERE id_titulo = :id FOR UPDATE');
            $registro->execute(['id' => (int) $id]);

            if ($registro->fetch() === false) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => 'NO_ENCONTRADO', 'mensaje' => 'El título académico no existe'];
            }

            $dependencias = $pdo->prepare('SELECT 1 FROM grado_academico WHERE tit_grado = :id LIMIT 1');
            $dependencias->execute(['id' => (int) $id]);

            if ($dependencias->fetchColumn() !== false) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => 'TIENE_DEPENDENCIAS', 'mensaje' => 'No se puede eliminar porque existen registros asociados'];
            }

            $eliminar = $pdo->prepare('DELETE FROM titulo_grado WHERE id_titulo = :id');
            $eliminar->execute(['id' => (int) $id]);
            $pdo->commit();

            return ['ok' => true, 'codigo' => 'ELIMINADO', 'mensaje' => 'Registro eliminado correctamente'];
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($e->getCode() === '23000') {
                return ['ok' => false, 'codigo' => 'TIENE_DEPENDENCIAS', 'mensaje' => 'No se puede eliminar porque existen registros asociados'];
            }

            error_log('[Titulo::eliminar] ' . $e->getMessage());
            return ['ok' => false, 'codigo' => 'ERROR_ELIMINACION', 'mensaje' => 'No fue posible eliminar el registro'];
        }
    }
}
