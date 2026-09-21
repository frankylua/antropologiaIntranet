<?php declare(strict_types=1);
namespace App\Model;
use PDO;
final class Tesis {
  public function detalle(int $id): ?array {
    $q=conexion()->prepare($this->base().' WHERE t.id_tesis=:id');
    $q->execute(['id'=>$id]);
    $r=$q->fetch(PDO::FETCH_ASSOC);
    return $r===false?null:$r;
  }
  public function listarDocente(int $id): array {
    $q=conexion()->prepare($this->base().' WHERE t.id_prof_guia=:g OR t.id_prof_coguia=:c ORDER BY t.anio DESC,t.id_tesis DESC');
    $q->execute(['g'=>$id,'c'=>$id]);
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }
  public function listarEstudiante(int $id): array {
    $q=conexion()->prepare($this->base().' WHERE t.id_est_tes=:id ORDER BY t.anio DESC,t.id_tesis DESC');
    $q->execute(['id'=>$id]);
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }
  public function existe(string $table,string $column,int $id): bool {
    $ok=['usuario'=>'id_usuario','institucion'=>'id_inst','pais'=>'id_pais'];
    if(($ok[$table]??null)!==$column)return false;
    $q=conexion()->prepare("SELECT 1 FROM {$table} WHERE {$column}=:id LIMIT 1");
    $q->execute(['id'=>$id]);
    return $q->fetchColumn()!==false;
  }
  public function esDocente(int $id):bool{
    return $this->perfil('profesor',$id);
  }
  public function esEstudiante(int $id):bool{
    return $this->perfil('estudiante',$id);
  }
  public function buscarParticipantes(string $perfil,string $termino):array{
    if(!in_array($perfil,['estudiante','profesor'],true))return [];
    $termino=trim($termino);
    if(mb_strlen($termino)<2)return [];
    $q=conexion()->prepare("SELECT u.id_usuario,TRIM(CONCAT_WS(' ',u.nombres,u.ap_pat,u.ap_mat)) AS nombre FROM usuario u INNER JOIN {$perfil} p ON p.usuario=u.id_usuario WHERE CONCAT_WS(' ',u.nombres,u.ap_pat,u.ap_mat) LIKE :termino ESCAPE '\\\\' ORDER BY u.nombres,u.ap_pat,u.ap_mat LIMIT 20");
    $q->execute(['termino'=>'%'.str_replace(['\\','%','_'],['\\\\','\\%','\\_'],$termino).'%']);
    return $q->fetchAll(PDO::FETCH_ASSOC);
  }
  public function crear(array $d):array{
    $pdo=conexion();
    try{
      $pdo->beginTransaction();
      $this->resolverInstitucionesNuevas($d);
      $cot=$d['cotutela'];
      unset($d['cotutela']);
      $r=ejecutarEscritura($this->insert(),$d,true);
      $id=(int)($r['idInsertado']??0);
      if((int)($r['filasAfectadas']??0)!==1||$id<1)throw new \RuntimeException('Inserción no confirmada.');
      $this->cotutela($id,$cot);
      $pdo->commit();
      return ['filasAfectadas'=>1,'idInsertado'=>$id];
    }
    catch(\Throwable $e){
      if($pdo->inTransaction())$pdo->rollBack();
      throw $e;
    }
  }
  public function actualizar(int $id,array $d):array{
    $pdo=conexion();
    try{
      $pdo->beginTransaction();
      $this->resolverInstitucionesNuevas($d);
      $cot=$d['cotutela'];
      unset($d['cotutela']);
      $d['id_tesis']=$id;
      $r=ejecutarEscritura('UPDATE tesis SET lugar=:lugar,grado=:grado,tit_tesis=:titulo,anio=:anio,fecha_aprob=:fecha_aprob,fecha_def=:fecha_def,panel_eval=:panel,id_est_tes=:id_est,nom_est_tes=:nom_est,id_prof_guia=:id_guia,id_prof_coguia=:id_coguia,nom_prof_guia=:nom_guia,nom_prof_coguia=:nom_coguia,inst_tesis=:inst_tesis,pais_tesis=:pais_tesis WHERE id_tesis=:id_tesis',$d);
      $this->cotutela($id,$cot);
      $pdo->commit();
      return $r;
    }
    catch(\Throwable $e){
      if($pdo->inTransaction())$pdo->rollBack();
      throw $e;
    }
  }
  public function eliminar(int $id):array{
    return ejecutarEscritura('DELETE FROM tesis WHERE id_tesis=:id',['id'=>$id]);
  }
  private function resolverInstitucionesNuevas(array &$d):void{
    $nuevaTesis=$d['nueva_inst_tesis']??null;
    unset($d['nueva_inst_tesis']);
    $d['inst_tesis']=$this->crearInstitucionContextual($nuevaTesis,$d['inst_tesis']);
    if($d['cotutela']!==null){
      $nuevaCot=$d['cotutela']['nueva_inst_cot']??null;
      unset($d['cotutela']['nueva_inst_cot']);
      $d['cotutela']['inst_cot']=$this->crearInstitucionContextual($nuevaCot,$d['cotutela']['inst_cot']);
    }
  }
  private function crearInstitucionContextual(?string $nombre,int $idActual):int{
    if($nombre===null)return $idActual;
    $r=ejecutarEscritura('INSERT INTO institucion (inst) VALUES (:inst)',['inst'=>$nombre],true);
    $id=(int)($r['idInsertado']??0);
    if((int)($r['filasAfectadas']??0)!==1||$id<1)throw new \RuntimeException('No fue posible crear la institución.');
    return $id;
  }
  private function cotutela(int $tesis,?array $d):void{
    if($d===null){
      ejecutarEscritura('DELETE FROM cotutela WHERE tesis=:tesis',['tesis'=>$tesis]);
      return;
    }
    $d['tesis']=$tesis;
    $q=conexion()->prepare('SELECT 1 FROM cotutela WHERE tesis=:tesis');
    $q->execute(['tesis'=>$tesis]);
    $new=$q->fetchColumn()===false;
    $sql=$new?'INSERT INTO cotutela (prof_guia,id_prof_guia,inst_cot,fondo,ciudad,pais_cot,tesis) VALUES (:prof_guia,:id_prof_guia,:inst_cot,:fondo,:ciudad,:pais_cot,:tesis)':'UPDATE cotutela SET prof_guia=:prof_guia,id_prof_guia=:id_prof_guia,inst_cot=:inst_cot,fondo=:fondo,ciudad=:ciudad,pais_cot=:pais_cot WHERE tesis=:tesis';
    $r=ejecutarEscritura($sql,$d);
    if($new&&(int)($r['filasAfectadas']??0)!==1)throw new \RuntimeException('Cotutela no confirmada.');
  }
  private function perfil(string $table,int $id):bool{
    $q=conexion()->prepare("SELECT 1 FROM {$table} WHERE usuario=:id LIMIT 1");
    $q->execute(['id'=>$id]);
    return $q->fetchColumn()!==false;
  }
  private function insert():string{
    return 'INSERT INTO tesis (lugar,grado,tit_tesis,anio,fecha_aprob,fecha_def,panel_eval,id_est_tes,nom_est_tes,id_prof_guia,id_prof_coguia,nom_prof_guia,nom_prof_coguia,inst_tesis,pais_tesis) VALUES (:lugar,:grado,:titulo,:anio,:fecha_aprob,:fecha_def,:panel,:id_est,:nom_est,:id_guia,:id_coguia,:nom_guia,:nom_coguia,:inst_tesis,:pais_tesis)';
  }
  private function base():string{
    return "SELECT t.*,c.id_cotutela,c.prof_guia AS cot_guia_externo,c.id_prof_guia AS cot_guia_interno,c.inst_cot,c.fondo,c.ciudad,c.pais_cot,it.inst AS inst_tesis_nombre,pt.pais AS pais_tesis_nombre,ic.inst AS inst_cot_nombre,pc.pais AS pais_cot_nombre,TRIM(CONCAT_WS(' ',eg.nombres,eg.ap_pat,eg.ap_mat)) estudiante_interno,TRIM(CONCAT_WS(' ',gg.nombres,gg.ap_pat,gg.ap_mat)) guia_interno,TRIM(CONCAT_WS(' ',cg.nombres,cg.ap_pat,cg.ap_mat)) coguia_interno,TRIM(CONCAT_WS(' ',ct.nombres,ct.ap_pat,ct.ap_mat)) cot_guia_nombre FROM tesis t LEFT JOIN cotutela c ON c.tesis=t.id_tesis LEFT JOIN institucion it ON it.id_inst=t.inst_tesis LEFT JOIN pais pt ON pt.id_pais=t.pais_tesis LEFT JOIN institucion ic ON ic.id_inst=c.inst_cot LEFT JOIN pais pc ON pc.id_pais=c.pais_cot LEFT JOIN usuario eg ON eg.id_usuario=t.id_est_tes LEFT JOIN usuario gg ON gg.id_usuario=t.id_prof_guia LEFT JOIN usuario cg ON cg.id_usuario=t.id_prof_coguia LEFT JOIN usuario ct ON ct.id_usuario=c.id_prof_guia";
  }
}
