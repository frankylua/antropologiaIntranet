<?php
declare(strict_types=1);
namespace App\Model;
use PDO;
use RuntimeException;

final class Congreso {
    private function all(string $sql, array $params=[]): array { $s=conexion()->prepare($sql); $s->execute($params); return $s->fetchAll(PDO::FETCH_ASSOC); }
    private function one(string $sql, array $params=[]): ?array { return $this->all($sql,$params)[0]??null; }
    public function buscar(string $texto): array {
        $texto=trim($texto); if(mb_strlen($texto)<2)return [];
        $like='%'.str_replace(['\\','%','_'],['\\\\','\\%','\\_'],$texto).'%';
        return $this->all("SELECT id_congreso,nombre,ciudad,fecha_inicio,fecha_termino FROM congreso WHERE nombre LIKE :texto ESCAPE '\\\\' ORDER BY nombre,fecha_inicio LIMIT 20",[':texto'=>$like]);
    }
    public function existe(int $id): bool { return $this->one('SELECT id_congreso FROM congreso WHERE id_congreso=:id',[':id'=>$id])!==null; }
    public function listarContextual(int $usuario, bool $global=false): array {
        $where=$global?'':'WHERE p.id_aut=:usuario_aut OR p.id_coaut=:usuario_coaut';
        return $this->all("SELECT c.id_congreso,c.nombre,c.ciudad,c.fecha_inicio,c.fecha_termino,p.id_participacion,p.tipo_part,p.tipo_cong,p.otros_org,p.nombre_mesa,p.coment_ponenc,p.nom_aut,p.id_aut,TRIM(CONCAT_WS(' ',ua.nombres,ua.ap_pat,ua.ap_mat)) AS nombre_aut_interno,p.nom_coaut,p.id_coaut,TRIM(CONCAT_WS(' ',uc.nombres,uc.ap_pat,uc.ap_mat)) AS nombre_coaut_interno,p.congreso FROM congreso c JOIN participacion p ON p.congreso=c.id_congreso LEFT JOIN usuario ua ON ua.id_usuario=p.id_aut LEFT JOIN usuario uc ON uc.id_usuario=p.id_coaut $where ORDER BY c.nombre,c.fecha_inicio,p.id_participacion",$global?[]:[':usuario_aut'=>$usuario,':usuario_coaut'=>$usuario]);
    }
    public function detalle(int $id, ?int $usuario=null): ?array {
        $congreso=$this->one('SELECT id_congreso,nombre,ciudad,fecha_inicio,fecha_termino FROM congreso WHERE id_congreso=:id',[':id'=>$id]); if($congreso===null)return null;
        $where=$usuario===null?'p.congreso=:id':'p.congreso=:id AND (p.id_aut=:usuario_aut OR p.id_coaut=:usuario_coaut)';
        $congreso['participaciones']=$this->all("SELECT p.id_participacion,p.tipo_part,p.tipo_cong,p.otros_org,p.nombre_mesa,p.coment_ponenc,p.nom_aut,p.id_aut,TRIM(CONCAT_WS(' ',ua.nombres,ua.ap_pat,ua.ap_mat)) AS nombre_aut_interno,p.nom_coaut,p.id_coaut,TRIM(CONCAT_WS(' ',uc.nombres,uc.ap_pat,uc.ap_mat)) AS nombre_coaut_interno,p.congreso FROM participacion p LEFT JOIN usuario ua ON ua.id_usuario=p.id_aut LEFT JOIN usuario uc ON uc.id_usuario=p.id_coaut WHERE $where ORDER BY p.id_participacion",$usuario===null?[':id'=>$id]:[':id'=>$id,':usuario_aut'=>$usuario,':usuario_coaut'=>$usuario]); return $congreso;
    }
    public function participacion(int $id, ?int $usuario=null): ?array {
        $sql="SELECT p.id_participacion,p.congreso,p.tipo_part,p.tipo_cong,p.otros_org,p.nombre_mesa,p.coment_ponenc,p.nom_aut,p.id_aut,TRIM(CONCAT_WS(' ',ua.nombres,ua.ap_pat,ua.ap_mat)) AS nombre_aut_interno,p.nom_coaut,p.id_coaut,TRIM(CONCAT_WS(' ',uc.nombres,uc.ap_pat,uc.ap_mat)) AS nombre_coaut_interno FROM participacion p LEFT JOIN usuario ua ON ua.id_usuario=p.id_aut LEFT JOIN usuario uc ON uc.id_usuario=p.id_coaut WHERE p.id_participacion=:id";
        $params=[':id'=>$id];
        if ($usuario !== null) { $sql.=' AND (id_aut=:usuario_aut OR id_coaut=:usuario_coaut)'; $params[':usuario_aut']=$usuario; $params[':usuario_coaut']=$usuario; }
        return $this->one($sql,$params);
    }
    public function crearConParticipacion(array $congreso,array $participacion): int {
        $pdo=conexion(); $pdo->beginTransaction(); try { $s=$pdo->prepare('INSERT INTO congreso (nombre,ciudad,fecha_inicio,fecha_termino) VALUES (:nombre,:ciudad,:inicio,:termino)'); $s->execute([':nombre'=>$congreso['nombre'],':ciudad'=>$congreso['ciudad'],':inicio'=>$congreso['fecha_inicio'],':termino'=>$congreso['fecha_termino']]); $id=(int)$pdo->lastInsertId(); $this->insertarParticipacion($id,$participacion); $pdo->commit(); return $id; } catch(\Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); throw $e; }
    }
    public function crearParticipacion(int $congreso,array $datos): int { if(!$this->existe($congreso))throw new RuntimeException('Congreso no encontrado.'); return $this->insertarParticipacion($congreso,$datos); }
    private function insertarParticipacion(int $congreso,array $d): int {
        $s=conexion()->prepare('INSERT INTO participacion (tipo_part,tipo_cong,otros_org,nombre_mesa,coment_ponenc,nom_aut,id_aut,nom_coaut,id_coaut,congreso) VALUES (:tipo_part,:tipo_cong,:otros_org,:nombre_mesa,:coment_ponenc,:nom_aut,:id_aut,:nom_coaut,:id_coaut,:congreso)');
        $s->execute([':tipo_part'=>$d['tipo_part'],':tipo_cong'=>$d['tipo_cong'],':otros_org'=>$d['otros_org'],':nombre_mesa'=>$d['nombre_mesa'],':coment_ponenc'=>$d['coment_ponenc'],':nom_aut'=>$d['nom_aut'],':id_aut'=>$d['id_aut'],':nom_coaut'=>$d['nom_coaut'],':id_coaut'=>$d['id_coaut'],':congreso'=>$congreso]); return (int)conexion()->lastInsertId();
    }
    public function actualizarCongreso(int $id,array $d): bool { $s=conexion()->prepare('UPDATE congreso SET nombre=:nombre,ciudad=:ciudad,fecha_inicio=:inicio,fecha_termino=:termino WHERE id_congreso=:id'); $s->execute([':nombre'=>$d['nombre'],':ciudad'=>$d['ciudad'],':inicio'=>$d['fecha_inicio'],':termino'=>$d['fecha_termino'],':id'=>$id]); return $s->rowCount()===1; }
    public function actualizarParticipacion(int $id,array $d): bool { $s=conexion()->prepare('UPDATE participacion SET tipo_part=:tipo_part,tipo_cong=:tipo_cong,otros_org=:otros_org,nombre_mesa=:nombre_mesa,coment_ponenc=:coment_ponenc,nom_aut=:nom_aut,id_aut=:id_aut,nom_coaut=:nom_coaut,id_coaut=:id_coaut WHERE id_participacion=:id'); $s->execute([':tipo_part'=>$d['tipo_part'],':tipo_cong'=>$d['tipo_cong'],':otros_org'=>$d['otros_org'],':nombre_mesa'=>$d['nombre_mesa'],':coment_ponenc'=>$d['coment_ponenc'],':nom_aut'=>$d['nom_aut'],':id_aut'=>$d['id_aut'],':nom_coaut'=>$d['nom_coaut'],':id_coaut'=>$d['id_coaut'],':id'=>$id]); return $s->rowCount()===1; }
    public function eliminarCongreso(int $id): bool { $s=conexion()->prepare('DELETE FROM congreso WHERE id_congreso=:id');$s->execute([':id'=>$id]);return $s->rowCount()===1; }
    public function eliminarParticipacion(int $id): bool { $s=conexion()->prepare('DELETE FROM participacion WHERE id_participacion=:id');$s->execute([':id'=>$id]);return $s->rowCount()===1; }
}
