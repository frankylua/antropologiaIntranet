<?php
declare(strict_types=1);
namespace App\Model;

class Institucion {
    public function __construct(){
    }
    public function insertar($nombre){
        $sql="INSERT INTO institucion (id_inst,inst) VALUES (NULL,'$nombre')";
        return ejecutarEscritura($sql);
    }
    public function insertarObtenerId($nombre){
        $sql="INSERT INTO institucion (id_inst,inst) VALUES (NULL,'$nombre')";
        return obtenerIdConsulta($sql);
    }
    public function editar($id,$nombre){
        $sql="UPDATE institucion SET inst='$nombre' where id_inst='$id'";
        return ejecutarEscritura($sql);
    }
    public function mostrarConsultaOrdenada(){
         //consultar pueblos
         $sql1="SELECT * FROM institucion WHERE inst = 'universidad catolica del norte' OR inst='universidad de tarapaca'";
         $sql2="SELECT * FROM institucion WHERE inst LIKE 'universidad%' AND inst!='universidad de tarapaca' AND inst != 'universidad catolica del norte' ORDER BY inst";
         $sql3="SELECT * FROM institucion WHERE inst NOT LIKE 'universidad%' AND inst!='universidad de tarapaca' AND inst != 'universidad catolica del norte' ORDER BY inst";
         $respuesta1=ejecutarConsultaResultados($sql1);
         $respuesta2=ejecutarConsultaResultados($sql2);
         $respuesta3=ejecutarConsultaResultados($sql3);
         $institutos = array_merge_recursive($respuesta1,$respuesta2,$respuesta3);
         return $institutos;

    }
    public function eliminar($id){
        $pdo = conexion();
        $pdo->beginTransaction();

        try {
            $registro = $pdo->prepare('SELECT id_inst FROM institucion WHERE id_inst = :id FOR UPDATE');
            $registro->execute(['id' => (int) $id]);

            if ($registro->fetch() === false) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => 'NO_ENCONTRADO', 'mensaje' => 'La institución no existe'];
            }

            // Las FK vigentes resuelven sus dependencias mediante ON DELETE CASCADE.
            $eliminar = $pdo->prepare('DELETE FROM institucion WHERE id_inst = :id');
            $eliminar->execute(['id' => (int) $id]);
            $pdo->commit();

            return ['ok' => true, 'codigo' => 'ELIMINADO', 'mensaje' => 'Registro eliminado correctamente'];
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log('[Institucion::eliminar] ' . $e->getMessage());
            return ['ok' => false, 'codigo' => 'ERROR_ELIMINACION', 'mensaje' => 'No fue posible eliminar el registro'];
        }
    }
}
?>
