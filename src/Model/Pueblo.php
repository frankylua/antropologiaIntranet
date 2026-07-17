<?php
declare(strict_types=1);
namespace App\Model;

class Pueblo {
    public function __construct(){
    }
    public function insertar($nombre){
        $sql="INSERT INTO pueblo (id_pueblo,pueblo) VALUES (NULL,'$nombre')";
        return ejecutarEscritura($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE pueblo SET pueblo='$nombre' where id_pueblo='$id'";
        return ejecutarEscritura($sql);
    }
    public function mostrar(){
         //consultar pueblos
         $sql="SELECT * FROM pueblo ORDER BY pueblo";
         return ejecutarConsultaResultados($sql);
    }
    public function eliminar($id){
        $pdo = conexion();
        $pdo->beginTransaction();

        try {
            $registro = $pdo->prepare('SELECT id_pueblo FROM pueblo WHERE id_pueblo = :id FOR UPDATE');
            $registro->execute(['id' => (int) $id]);

            if ($registro->fetch() === false) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => 'NO_ENCONTRADO', 'mensaje' => 'El pueblo no existe'];
            }

            $dependencias = $pdo->prepare('SELECT 1 FROM usuario WHERE pueblo = :id LIMIT 1');
            $dependencias->execute(['id' => (int) $id]);

            if ($dependencias->fetchColumn() !== false) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => 'TIENE_DEPENDENCIAS', 'mensaje' => 'No se puede eliminar porque existen registros asociados'];
            }

            $eliminar = $pdo->prepare('DELETE FROM pueblo WHERE id_pueblo = :id');
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

            error_log('[Pueblo::eliminar] ' . $e->getMessage());
            return ['ok' => false, 'codigo' => 'ERROR_ELIMINACION', 'mensaje' => 'No fue posible eliminar el registro'];
        }
    }
}
