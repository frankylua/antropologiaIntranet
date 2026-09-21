<?php declare(strict_types=1);
require_once __DIR__.'/../src/bootstrap/session.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
require_once __DIR__.'/../src/bootstrap/app.php';
use App\Model\Tesis;
use App\Model\Usuario;
use App\Security\Authorization;
final class TE extends RuntimeException{
  function __construct(public int $h,public string $c,string $m){
    parent::__construct($m);
  }
}
function fail(int $h,string $c,string $m):never{
  throw new TE($h,$c,$m);
}
function out(int $h,string $c,string $m,mixed $d=null):never{
  http_response_code($h);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>$h<400,'codigo'=>$c,'mensaje'=>$m,'datos'=>$d],JSON_UNESCAPED_UNICODE);
  exit;
}
function id(mixed $v,string $n):int{
  if(!is_scalar($v)||preg_match('/^[1-9][0-9]*$/D',(string)$v)!==1)fail(400,'VALIDACION_INVALIDA',"$n inválido.");
  return(int)$v;
}
function txt(mixed $v,string $n,int $max):string{
  if(!is_string($v)||trim($v)===''||mb_strlen(trim($v))>$max)fail(400,'VALIDACION_INVALIDA',"$n inválido.");
  return trim($v);
}
function opt(mixed $v,string $n,int $max):?string{
  return $v===null||$v===''?null:txt($v,$n,$max);
}
function actor(Usuario $u):array{
  $login=$_SESSION['login']??null;
  if(!is_scalar($login)||preg_match('/^[1-9][0-9]*$/D',(string)$login)!==1)fail(401,'NO_AUTENTICADO','Debe iniciar sesión.');
  $login=(int)$login;
  if(Authorization::hasAny(['admin','comite'])&&((int)($_SESSION['admin']??0)===$login||(int)($_SESSION['comite']??0)===$login))return['r'=>'global','id'=>0];
  $ids=$_SESSION['id_usuario']??[];
  $uid=is_array($ids)&&count($ids)===1?(int)($ids[0]['id_usuario']??0):0;
  $d=(int)($_SESSION['docente']??0)===$login;
  $e=(int)($_SESSION['estudiante']??0)===$login;
  if($uid<1||$d===$e||!$u->usuarioAcademicoExiste($uid))fail(403,'NO_AUTORIZADO','Perfil académico inválido.');
  return['r'=>$d?'docente':'estudiante','id'=>$uid];
}
function csrf():void{
  $a=$_SESSION['csrf_tesis']??null;
  $b=$_SERVER['HTTP_X_CSRF_TOKEN']??null;
  if(!is_string($a)||!is_string($b)||!hash_equals($a,$b))fail(403,'CSRF_INVALIDO','Token CSRF inválido.');
}
function subject(array $a,Tesis $t):array{
  if($a['r']!=='global'){
    $key=$a['r']==='estudiante'?'subject_usuario_id':'subject_docente_id';
    if(isset($_POST[$key])&&id($_POST[$key],'subject')!==$a['id'])fail(403,'NO_AUTORIZADO','Contexto no autorizado.');
    return['id'=>$a['id'],'rol'=>$a['r']];
  }
  $hasStudent=isset($_POST['subject_usuario_id'])&&$_POST['subject_usuario_id']!=='';
  $hasDocente=isset($_POST['subject_docente_id'])&&$_POST['subject_docente_id']!=='';
  if($hasStudent===$hasDocente)fail(400,'VALIDACION_INVALIDA','Debe indicar un único subject.');
  if($hasStudent){
    $id=id($_POST['subject_usuario_id'],'subject_usuario_id');
    if(!$t->esEstudiante($id))fail(400,'VALIDACION_INVALIDA','Subject estudiante inválido.');
    return['id'=>$id,'rol'=>'estudiante'];
  }
  $id=id($_POST['subject_docente_id'],'subject_docente_id');
  if(!$t->esDocente($id))fail(400,'VALIDACION_INVALIDA','Subject docente inválido.');
  return['id'=>$id,'rol'=>'docente'];
}
function role(string $k,Tesis $t,bool $doc):array{
  $i=$_POST[$k.'_usuario_id']??null;
  $n=opt($_POST[$k.'_nombre_externo']??null,$k,60);
  $i=$i===null||$i===''?null:id($i,$k);
  if(($i===null)==($n===null))fail(400,'VALIDACION_INVALIDA',"$k debe ser interno o externo.");
  if($i!==null&&(!$t->existe('usuario','id_usuario',$i)||($doc&&!$t->esDocente($i))||($k==='estudiante'&&!$t->esEstudiante($i))))fail(400,'VALIDACION_INVALIDA',"$k interno inválido.");
  return['id'=>$i,'name'=>$n];
}
function institucion(Tesis $t,string $campo):array{
  $valor=$_POST[$campo]??null;
  if($valor==='nueva'){
    $nombre=$_POST['nueva_'.$campo]??null;
    if(!is_string($nombre)||trim($nombre)==='')fail(400,'VALIDACION_INVALIDA',"Nueva institución {$campo} inválida.");
    return['id'=>0,'nueva'=>trim($nombre)];
  }
  $id=id($valor,$campo);
  if(!$t->existe('institucion','id_inst',$id))fail(400,'VALIDACION_INVALIDA','Institución inexistente.');
  return['id'=>$id,'nueva'=>null];
}
function data(Tesis $t):array{
  $e=role('estudiante',$t,false);
  $g=role('guia',$t,true);
  $c=role('coguia',$t,true);
  if($g['id']!==null&&$g['id']===$c['id'])fail(400,'VALIDACION_INVALIDA','Guía y Coguía internos deben ser distintos.');
  $inst=institucion($t,'inst_tesis');
  $pais=id($_POST['pais_tesis']??null,'País Tesis');
  if(!$t->existe('pais','id_pais',$pais))fail(400,'VALIDACION_INVALIDA','País inexistente.');
  $cot=null;
  if(($_POST['cotutela_activa']??'false')==='true'){
    $cg=role('cot_guia',$t,true);
    $ci=institucion($t,'inst_cot');
    $cp=id($_POST['pais_cot']??null,'País Cotutela');
    if(!$t->existe('pais','id_pais',$cp))fail(400,'VALIDACION_INVALIDA','País de Cotutela inexistente.');
    $cot=['id_prof_guia'=>$cg['id'],'prof_guia'=>$cg['name'],'inst_cot'=>$ci['id'],'nueva_inst_cot'=>$ci['nueva'],'fondo'=>txt($_POST['fondo']??null,'Fondo',45),'ciudad'=>txt($_POST['ciudad_cot']??null,'Ciudad',45),'pais_cot'=>$cp];
  }
  return['lugar'=>txt($_POST['lugar']??null,'Lugar',80),'grado'=>id($_POST['grado']??null,'Grado'),'titulo'=>txt($_POST['titulo']??null,'Título',300),'anio'=>id($_POST['anio']??null,'Año'),'fecha_aprob'=>txt($_POST['fecha_aprob']??null,'Fecha aprobación',10),'fecha_def'=>txt($_POST['fecha_def']??null,'Fecha defensa',10),'panel'=>txt($_POST['panel']??null,'Panel',200),'id_est'=>$e['id'],'nom_est'=>$e['name'],'id_guia'=>$g['id'],'nom_guia'=>$g['name'],'id_coguia'=>$c['id'],'nom_coguia'=>$c['name'],'inst_tesis'=>$inst['id'],'nueva_inst_tesis'=>$inst['nueva'],'pais_tesis'=>$pais,'cotutela'=>$cot];
}
function capabilities(array $row,array $actor):array{
  $both=$row['id_prof_guia']!==null&&$row['id_prof_coguia']!==null;
  $manage=$actor['r']==='global'||($actor['r']==='docente'&&!$both);
  $row['canRead']=true;
  $row['canUpdate']=$manage;
  $row['canDelete']=$manage;
  return$row;
}
try{
  if(($_SERVER['REQUEST_METHOD']??'')!=='POST')fail(405,'METODO_NO_PERMITIDO','Utilice POST.');
  $t=new Tesis;
  $a=actor(new Usuario);
  $op=$_POST['op']??'';
  if(!in_array($op,['context','lookup','list','detail','create','update','delete'],true))fail(400,'OPERACION_INVALIDA','Operación inválida.');
  if($op==='lookup'){
    $tipo=$_POST['tipo']??'';
    $termino=$_POST['termino']??'';
    if(!is_string($tipo)||!in_array($tipo,['estudiante','profesor'],true)||!is_string($termino))fail(400,'VALIDACION_INVALIDA','Consulta de participante inválida.');
    out(200,'PARTICIPANTES_OBTENIDOS','Participantes obtenidos.',$t->buscarParticipantes($tipo,$termino));
  }
  $current=subject($a,$t);
  $s=$current['id'];
  $subjectRole=$current['rol'];
  if($op==='context'){
    $_SESSION['csrf_tesis']=$_SESSION['csrf_tesis']??bin2hex(random_bytes(32));
    $readOnly=$subjectRole==='estudiante';
    out(200,'CONTEXTO_OBTENIDO','Contexto obtenido.',['csrf_token'=>$_SESSION['csrf_tesis'],'rol'=>$subjectRole,'actorRol'=>$a['r'],'canCreate'=>!$readOnly,'canUpdate'=>!$readOnly,'canDelete'=>!$readOnly]);
  }
  if(in_array($op,['create','update','delete'],true))csrf();
  if($op==='list'){
    $rows=$subjectRole==='estudiante'?$t->listarEstudiante($s):$t->listarDocente($s);
    out(200,'LISTA_OBTENIDA','Tesis obtenidas.',array_map(fn(array $row):array=>capabilities($row,$a),$rows));
  }
  if($op==='create'){
    if($subjectRole==='estudiante')fail(403,'NO_AUTORIZADO','Estudiante sólo puede leer.');
    $d=data($t);
    if($d['id_guia']!==$s&&$d['id_coguia']!==$s)fail(403,'NO_AUTORIZADO','La Tesis debe relacionar al Docente del contexto.');
    $r=$t->crear($d);
    out(201,'TESIS_CREADA','Tesis creada.',['id_tesis'=>$r['idInsertado']]);
  }
  $i=id($_POST['id_tesis']??null,'id_tesis');
  $row=$t->detalle($i);
  if($row===null)fail(404,'TESIS_NO_ENCONTRADA','Tesis inexistente.');
  $rel=(int)$row['id_prof_guia']===$s||(int)$row['id_prof_coguia']===$s;
  if(($subjectRole==='estudiante'&&(int)$row['id_est_tes']!==$s)||($subjectRole!=='estudiante'&&!$rel))fail(403,'NO_AUTORIZADO','Tesis fuera de contexto.');
  if($op==='detail')out(200,'TESIS_OBTENIDA','Tesis obtenida.',$row);
  if($subjectRole==='estudiante'||($a['r']==='docente'&&$row['id_prof_guia']!==null&&$row['id_prof_coguia']!==null))fail(403,'NO_AUTORIZADO','Operación prohibida.');
  if($op==='update'){
    $d=data($t);
    if($d['id_guia']!==$s&&$d['id_coguia']!==$s)fail(403,'NO_AUTORIZADO','Debe conservar el Docente del contexto.');
    $t->actualizar($i,$d);
    out(200,'TESIS_ACTUALIZADA','Tesis actualizada.',['id_tesis'=>$i]);
  }
  $r=$t->eliminar($i);
  if((int)$r['filasAfectadas']!==1)fail(409,'CONFLICTO_PERSISTENCIA','Eliminación no confirmada.');
  out(200,'TESIS_ELIMINADA','Tesis eliminada.');
}
catch(TE $e){
  out($e->h,$e->c,$e->getMessage());
}
catch(PDOException $e){
  error_log('[TESIS] '.$e->getMessage());
  out(500,'ERROR_PERSISTENCIA','Error de persistencia.');
}
catch(Throwable $e){
  error_log('[TESIS] '.$e->getMessage());
  out(500,'ERROR_TECNICO','Error técnico.');
}
