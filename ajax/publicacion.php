<?php
require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';
use App\Model\Publicacion;
use App\Model\Usuario;
use App\Security\Authorization;
require 'validaciones.php';
$csrf = static function (): void {
    $recibido = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? null);
    $sesion = $_SESSION['csrf_publicacion'] ?? null;
    if (!is_string($recibido) || !is_string($sesion) || !hash_equals($sesion, $recibido)) {
        throw new RuntimeException('CSRF inválido.');
    }
};
$actor = static function (): int {
    $usuarios = $_SESSION['id_usuario'] ?? null;
    $id = is_array($usuarios) ? ($usuarios[0]['id_usuario'] ?? null) : null;
    if (!is_scalar($id) || (int)$id < 1) throw new RuntimeException('Autenticación requerida.');
    return (int)$id;
};
$autenticado = static function (): void {
    $login = $_SESSION['login'] ?? null;
    if (!is_scalar($login) || (int)$login < 1) throw new RuntimeException('Autenticación requerida.');
};
$global = static fn(): bool => Authorization::hasAny(['admin', 'comite']);
$responder = static function (int $status, array $payload): never { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($payload, JSON_UNESCAPED_UNICODE); exit; };
$inicializarCsrf = static function (): void {
    $token = $_SESSION['csrf_publicacion'] ?? null;
    if (!is_string($token) || preg_match('/^[a-f0-9]{64}$/D', $token) !== 1) {
        $_SESSION['csrf_publicacion'] = bin2hex(random_bytes(32));
    }
};
$usuario=isset($_POST['usuario'])?(int)$_POST['usuario']:'';
$tipo=isset($_POST['tipo'])?(int)$_POST['tipo']:'';
$anio=isset($_POST['anio'])?(int)$_POST['anio']:'';
$autores=isset($_POST['autores'])?$_POST['autores']:'';
$nombre=isset($_POST['nombre'])?$_POST['nombre']:'';
$estado=isset($_POST['estado'])?(int)$_POST['estado']:'';
$titulo=isset($_POST['titulo'])?$_POST['titulo']:'';
$indizacion=isset($_POST['indizacion'])?(int)$_POST['indizacion']:'';
$issn=isset($_POST['issn'])?$_POST['issn']:'';
$fac_imp=isset($_POST['fac_imp'])?floatval($_POST['fac_imp']):'';
$tipo_lib=isset($_POST['tipo_lib'])?(int)$_POST['tipo_lib']:'';
$rol=isset($_POST['rol_libro'])?(int)$_POST['rol_libro']:'';
$ref_ext=isset($_POST['ref_ext'])?(int)$_POST['ref_ext']:'';
$traduccion=isset($_POST['traduccion'])?(int)$_POST['traduccion']:'';
$lugar=isset($_POST['lugar'])?$_POST['lugar']:'';
$editorial=isset($_POST['editorial'])?$_POST['editorial']:'';
$tipo_pub=isset($_POST['tipo_pub'])?$_POST['tipo_pub']:'';
$desc_pub=isset($_POST['desc_pub'])?$_POST['desc_pub']:'';
$busqueda=isset($_POST['busqueda'])?$_POST['busqueda']:'';
$libro=isset($_POST['libro'])?$_POST['libro']:'';
$id_pub=isset($_POST['id_pub'])?$_POST['id_pub']:0;
// $tipo_otra_pub=isset($_POST['tipo_otra_pub'])?$_POST['tipo_otra_pub']:'';
$op=isset($_POST['op'])?$_POST['op']:'';
if ($op === 'context') {
    try {
        $autenticado();
        if (!$global()) $actor();
    } catch (RuntimeException $e) {
        $responder(401, ['status'=>'ERROR','message'=>'Operación no autorizada.']);
    }

    $inicializarCsrf();
    $responder(200, ['status'=>'OK','data'=>[
        'csrf_token'=>$_SESSION['csrf_publicacion'],
        'can_manage_global'=>$global(),
    ]]);
}
$pub= new Publicacion();
$usuariosModelo = new Usuario();
if ($op === 'academic_user_search') {
    try {
        $autenticado();
        $canManageGlobal = $global();
        if (!$canManageGlobal) {
            $actorId = $actor();
            $actorAcademico = $usuariosModelo->usuarioAcademicoExiste($actorId);
            if (!$actorAcademico) {
                $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
            }
        }
        $q = $_POST['q'] ?? null;
        if (!is_string($q) || mb_strlen(trim($q)) < 2) {
            $responder(400, ['status'=>'ERROR','message'=>'Ingrese al menos 2 caracteres.']);
        }
        $responder(200, ['status'=>'OK','data'=>$usuariosModelo->buscarUsuariosAcademicos($q)]);
    } catch (RuntimeException $e) {
        $responder(401, ['status'=>'ERROR','message'=>'Operación no autorizada.']);
    } catch (Throwable $e) {
        $responder(500, ['status'=>'ERROR','message'=>'No fue posible buscar usuarios académicos.']);
    }
}
$participantesSolicitud = static function (): array {
    $resultado = [];
    foreach (['autor' => 'AUTOR', 'coautor' => 'COAUTOR'] as $prefijo => $rolPrincipal) {
        $usuario = filter_var($_POST[$prefijo . '_usuario'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        $nombre = $_POST[$prefijo . '_nombre_externo'] ?? null;
        if ($usuario === false) $usuario = null;
        if ($usuario !== null && $nombre !== null && $nombre !== '') throw new InvalidArgumentException('Autoría ambigua.');
        if ($usuario !== null) { $resultado[$rolPrincipal] = ['usuario'=>(int)$usuario, 'nombre_externo'=>null]; continue; }
        if (!is_string($nombre) || trim($nombre) === '' || strlen(trim($nombre)) > 120) throw new InvalidArgumentException('Autoría externa inválida.');
        $resultado[$rolPrincipal] = ['usuario'=>null, 'nombre_externo'=>trim($nombre)];
    }
    return $resultado;
};
try {
    if (in_array($op, ['create','update','delete','participation_create','participation_update','participation_delete'], true)) {
        try { $csrf(); } catch (RuntimeException $e) { $responder(403, ['status'=>'ERROR','message'=>'CSRF inválido.']); }
    }
    if (in_array($op, ['participation_create','participation_update','participation_delete'], true)) {
        $responder(410, ['status'=>'ERROR','message'=>'La autoría principal sólo se modifica junto con la publicación.']);
    }
    if ($op === 'create') {
        $autenticado(); $actorId=$actor(); $canManageGlobal=$global(); $subjectUsuarioId=$actorId;
        if ($canManageGlobal) { $subjectUsuarioId=filter_var($_POST['subject_usuario_id'] ?? ($_POST['usuario'] ?? null),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]); if($subjectUsuarioId===false||!$usuariosModelo->usuarioAcademicoExiste((int)$subjectUsuarioId))$responder(400,['status'=>'ERROR','message'=>'Sujeto académico inválido.']); }
        elseif (!$usuariosModelo->usuarioAcademicoExiste($actorId)) $responder(403,['status'=>'ERROR','message'=>'Actor académico inválido.']);
        $participantes=$participantesSolicitud();
        if (($participantes['AUTOR']['usuario'] ?? null) !== $subjectUsuarioId && ($participantes['COAUTOR']['usuario'] ?? null) !== $subjectUsuarioId) $responder(403,['status'=>'ERROR','message'=>'El sujeto académico debe ser Autor o Coautor interno.']);
        $datos=['nombre'=>trim((string)($_POST['nombre']??'')),'autores'=>trim((string)($_POST['autores']??'')),'anio'=>(int)($_POST['anio']??0),'estado'=>(int)($_POST['estado']??0)];
        if($datos['nombre']===''||strlen($datos['nombre'])>60||strlen($datos['autores'])>300)$responder(400,['status'=>'ERROR','message'=>'Datos de Publicación inválidos.']);
        $tipoNuevo=(int)($_POST['tipo']??0);
        if(in_array($tipoNuevo,[1,2],true)) {
            $datos += ['tipo'=>$tipoNuevo,'titulo'=>trim((string)($_POST['titulo']??'')),'issn'=>trim((string)($_POST['issn']??'')),'indizacion'=>(int)($_POST['indizacion']??0),'fac_imp'=>!empty($_POST['no_fac']) ? 0 : (float)($_POST['fac_imp']??0)];
            $id=$pub->crearArticuloPrincipal($datos,$participantes);
        } elseif($tipoNuevo===3) {
            $existentes=$_POST['editoriales_existentes']??[]; $nuevas=$_POST['editoriales_nuevas']??[];
            if(!is_array($existentes)||!is_array($nuevas))$responder(400,['status'=>'ERROR','message'=>'Contrato editorial inválido.']);
            $existentes=array_values(array_unique(array_map(static fn($v)=>filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]),$existentes)));
            if(in_array(false,$existentes,true))$responder(400,['status'=>'ERROR','message'=>'Editorial existente inválida.']);
            $nuevas=array_values(array_unique(array_map(static fn($v)=>trim(is_string($v)?$v:''),$nuevas)));
            if(array_filter($nuevas,static fn($v)=>$v===''||strlen($v)>60)!==[])$responder(400,['status'=>'ERROR','message'=>'Editorial nueva inválida.']);
            $datos += ['tipo'=>(int)($_POST['tipo_lib']??0),'rol'=>(int)($_POST['rol_libro']??0),'ref_ext'=>(int)($_POST['ref_ext']??0),'traduccion'=>(int)($_POST['traduccion']??0),'lugar'=>trim((string)($_POST['lugar']??'')),'editoriales_existentes'=>$existentes,'editoriales_nuevas'=>$nuevas];
            $id=$pub->crearLibroPrincipal($datos,$participantes);
        } else $responder(400,['status'=>'ERROR','message'=>'Tipo inválido.']);
        $responder(201,['status'=>'OK','data'=>['id_publicacion'=>$id]]);
        $datos=['nombre'=>trim((string)($_POST['nombre']??'')),'autores'=>trim((string)($_POST['autores']??'')),'anio'=>(int)($_POST['anio']??0),'estado'=>(int)($_POST['estado']??0)]; if($datos['nombre']===''||strlen($datos['nombre'])>60||strlen($datos['autores'])>300)$responder(400,['status'=>'ERROR','message'=>'Datos de Publicación inválidos.']);
        $tipoNuevo=(int)($_POST['tipo']??0); if(in_array($tipoNuevo,[1,2],true)){$datos+=['tipo'=>$tipoNuevo,'titulo'=>trim((string)($_POST['titulo']??'')),'issn'=>trim((string)($_POST['issn']??'')),'indizacion'=>(int)($_POST['indizacion']??0),'fac_imp'=>(float)($_POST['fac_imp']??0)];$id=$pub->crearArticuloConParticipacion($datos,(int)$participante,$rolParticipacion);} elseif($tipoNuevo===3){$existentes=$_POST['editoriales_existentes']??[];$nuevas=$_POST['editoriales_nuevas']??[];if(!is_array($existentes)||!is_array($nuevas))$responder(400,['status'=>'ERROR','message'=>'Contrato editorial inválido.']);$existentes=array_values(array_unique(array_map(static fn($v)=>filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]),$existentes)));if(in_array(false,$existentes,true))$responder(400,['status'=>'ERROR','message'=>'Editorial existente inválida.']);$nuevas=array_values(array_unique(array_map(static fn($v)=>trim(is_string($v)?$v:''),$nuevas)));if(array_filter($nuevas,static fn($v)=>$v===''||strlen($v)>60)!==[])$responder(400,['status'=>'ERROR','message'=>'Editorial nueva inválida.']);$datos+=['tipo'=>(int)($_POST['tipo_lib']??0),'rol'=>(int)($_POST['rol_libro']??0),'ref_ext'=>(int)($_POST['ref_ext']??0),'traduccion'=>(int)($_POST['traduccion']??0),'lugar'=>trim((string)($_POST['lugar']??'')),'editoriales_existentes'=>$existentes,'editoriales_nuevas'=>$nuevas];$id=$pub->crearLibroConParticipacion($datos,(int)$participante,$rolParticipacion);}else $responder(400,['status'=>'ERROR','message'=>'Tipo inválido.']); $responder(201,['status'=>'OK','data'=>['id_publicacion'=>$id]]);
    }
    if ($op === 'list') {
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $usuarioObjetivo = $actorId;
            if ($global() && array_key_exists('usuario', $_POST)) {
                $usuarioObjetivo = filter_var($_POST['usuario'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
                if ($usuarioObjetivo === false || !$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario inválido.']);
            }
            if ($usuarioObjetivo === null) $responder(400, ['status'=>'ERROR','message'=>'Usuario contextual requerido.']);
            $tipoListado = $_POST['tipo'] ?? null;
            if ($tipoListado !== null && $tipoListado !== '' && !in_array(filter_var($tipoListado, FILTER_VALIDATE_INT), [1, 2, 3], true)) $responder(400, ['status'=>'ERROR','message'=>'Tipo inválido.']);
            $tipoListado = $tipoListado === null || $tipoListado === '' ? null : (int)$tipoListado;
            $articulos = $tipoListado === 3 ? [] : $pub->mostrarArtRev((int)$usuarioObjetivo);
            $libros = in_array($tipoListado, [1, 2], true) ? [] : $pub->mostrarLibros((int)$usuarioObjetivo);
            $responder(200, ['status'=>'OK','data'=>['articulos'=>$articulos, 'libros'=>$libros]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible listar las publicaciones.']);
        }
    }
    if ($op === 'update' && (int)($_POST['tipo'] ?? 0) !== 4) {
        $autenticado();
        if (!$global()) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
        $subjectUsuarioId=filter_var($_POST['subject_usuario_id'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        if($subjectUsuarioId===false||!$usuariosModelo->usuarioAcademicoExiste((int)$subjectUsuarioId))$responder(400,['status'=>'ERROR','message'=>'Sujeto académico inválido.']);
        $participantes=$participantesSolicitud();
        if (($participantes['AUTOR']['usuario'] ?? null) !== $subjectUsuarioId && ($participantes['COAUTOR']['usuario'] ?? null) !== $subjectUsuarioId) $responder(403,['status'=>'ERROR','message'=>'El sujeto académico debe ser Autor o Coautor interno.']);
        try {
            $id=filter_var($_POST['id_publicacion']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]); if($id===false)$responder(400,['status'=>'ERROR','message'=>'ID inválido.']);
            $datos=['nombre'=>trim((string)($_POST['nombre']??'')),'autores'=>trim((string)($_POST['autores']??'')),'anio'=>(int)($_POST['anio']??0),'estado'=>(int)($_POST['estado']??0)]; if($datos['nombre']===''||strlen($datos['nombre'])>60||strlen($datos['autores'])>300)$responder(400,['status'=>'ERROR','message'=>'Datos de Publicación inválidos.']);
            $tipoUpdate=(int)($_POST['tipo']??0);
            if(in_array($tipoUpdate,[1,2],true)) { $datos+=['tipo'=>$tipoUpdate,'titulo'=>trim((string)($_POST['titulo']??'')),'issn'=>trim((string)($_POST['issn']??'')),'indizacion'=>(int)($_POST['indizacion']??0),'fac_imp'=>!empty($_POST['no_fac']) ? 0 : (float)($_POST['fac_imp']??0)]; $pub->actualizarArticuloPrincipal((int)$id,$datos,$participantes); }
            elseif($tipoUpdate===3) { $existentes=$_POST['editoriales_existentes']??[];$nuevas=$_POST['editoriales_nuevas']??[];if(!is_array($existentes)||!is_array($nuevas))$responder(400,['status'=>'ERROR','message'=>'Contrato editorial inválido.']);$existentes=array_values(array_unique(array_map(static fn($v)=>filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]),$existentes)));if(in_array(false,$existentes,true))$responder(400,['status'=>'ERROR','message'=>'Editorial existente inválida.']);$nuevas=array_values(array_unique(array_map(static fn($v)=>trim(is_string($v)?$v:''),$nuevas)));if(array_filter($nuevas,static fn($v)=>$v===''||strlen($v)>60)!==[])$responder(400,['status'=>'ERROR','message'=>'Editorial nueva inválida.']);$datos+=['tipo'=>(int)($_POST['tipo_lib']??0),'rol'=>(int)($_POST['rol_libro']??0),'ref_ext'=>(int)($_POST['ref_ext']??0),'traduccion'=>(int)($_POST['traduccion']??0),'lugar'=>trim((string)($_POST['lugar']??''))];$pub->actualizarLibroPrincipal((int)$id,$datos,$existentes,$nuevas,$participantes); }
            else $responder(400,['status'=>'ERROR','message'=>'Tipo inválido.']);
            $responder(200,['status'=>'OK','data'=>['id_publicacion'=>(int)$id]]);
        } catch (Throwable $e) { $responder(500,['status'=>'ERROR','message'=>'No fue posible actualizar la publicación.']); }
        try {
        $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
        $datos = ['nombre'=>trim((string)($_POST['nombre']??'')), 'autores'=>trim((string)($_POST['autores']??'')), 'anio'=>(int)($_POST['anio']??0), 'estado'=>(int)($_POST['estado']??0)];
        if ($datos['nombre']==='' || strlen($datos['nombre'])>60 || strlen($datos['autores'])>300) $responder(400, ['status'=>'ERROR','message'=>'Datos de Publicación inválidos.']);
        $tipoUpdate = filter_var($_POST['tipo'] ?? null, FILTER_VALIDATE_INT);
        if (in_array($tipoUpdate, [1, 2], true)) {
            if ($pub->detalleArticulo((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            $datos += ['tipo'=>$tipoUpdate, 'titulo'=>trim((string)($_POST['titulo']??'')), 'issn'=>trim((string)($_POST['issn']??'')), 'indizacion'=>(int)($_POST['indizacion']??0), 'fac_imp'=>(float)($_POST['fac_imp']??0)];
            $pub->actualizarArticulo((int)$id, $datos);
        } elseif ($tipoUpdate === 3) {
            if ($pub->detalleLibro((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            $existentes = $_POST['editoriales_existentes'] ?? [];
            $nuevas = $_POST['editoriales_nuevas'] ?? [];
            if (!is_array($existentes) || !is_array($nuevas)) $responder(400, ['status'=>'ERROR','message'=>'Contrato editorial inválido.']);
            $existentes = array_map(static fn($valor) => filter_var($valor, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]), $existentes);
            if (in_array(false, $existentes, true)) $responder(400, ['status'=>'ERROR','message'=>'Editorial existente inválida.']);
            $existentes = array_values(array_unique($existentes));
            foreach ($nuevas as $editorialNueva) if (!is_string($editorialNueva)) $responder(400, ['status'=>'ERROR','message'=>'Editorial nueva inválida.']);
            $nuevas = array_values(array_unique(array_map(static fn(string $valor): string => trim($valor), $nuevas)));
            if (array_filter($nuevas, static fn(string $valor): bool => $valor==='' || strlen($valor)>60) !== []) $responder(400, ['status'=>'ERROR','message'=>'Editorial nueva inválida.']);
            $datos += ['tipo'=>(int)($_POST['tipo_lib']??0), 'rol'=>(int)($_POST['rol_libro']??0), 'ref_ext'=>(int)($_POST['ref_ext']??0), 'traduccion'=>(int)($_POST['traduccion']??0), 'lugar'=>trim((string)($_POST['lugar']??''))];
            $pub->actualizarLibro((int)$id, $datos, $existentes, $nuevas);
        } else {
            $responder(400, ['status'=>'ERROR','message'=>'Tipo inválido.']);
        }
        $responder(200, ['status'=>'OK','data'=>['id_publicacion'=>(int)$id]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible actualizar la publicación.']);
        }
    }
    if ($op === 'delete') {
        $autenticado();
        if (!$global()) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
        try {
            $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
            if ($pub->detalleArticulo((int)$id) === null && $pub->detalleLibro((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            $resultado = $pub->eliminar((int)$id);
            if (($resultado['filasAfectadas'] ?? 0) !== 1) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            $responder(200, ['status'=>'OK','data'=>['id_publicacion'=>(int)$id]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible eliminar la publicación.']);
        }
    }
    if ($op === 'participation_create') {
        $autenticado();
        if (!$global()) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
        try {
            $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $usuarioObjetivo = filter_var($_POST['usuario'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $rolParticipacion = (string)($_POST['rol'] ?? '');
            if ($id === false || $usuarioObjetivo === false) $responder(400, ['status'=>'ERROR','message'=>'Datos de participación inválidos.']);
            if (!$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario participante inválido.']);
            if (!in_array($rolParticipacion, ['AUTOR', 'COAUTOR'], true)) $responder(400, ['status'=>'ERROR','message'=>'Rol inválido.']);
            if ($pub->detalleArticulo((int)$id) === null && $pub->detalleLibro((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if ($pub->usuarioParticipa((int)$id, (int)$usuarioObjetivo)) $responder(409, ['status'=>'ERROR','message'=>'La participación ya existe.']);
            $resultado = $pub->crearParticipacion((int)$id, (int)$usuarioObjetivo, $rolParticipacion);
            if (($resultado['filasAfectadas'] ?? 0) !== 1) $responder(500, ['status'=>'ERROR','message'=>'No fue posible crear la participación.']);
            $responder(201, ['status'=>'OK','data'=>['id_publicacion'=>(int)$id, 'usuario'=>(int)$usuarioObjetivo, 'rol'=>$rolParticipacion]]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $responder(409, ['status'=>'ERROR','message'=>'No fue posible crear la participación por conflicto de integridad.']);
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible crear la participación.']);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible crear la participación.']);
        }
    }
    if ($op === 'participation_update') {
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
            $usuarioObjetivo = $actorId;
            if ($global()) {
                $usuarioObjetivo = filter_var($_POST['usuario'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
                if ($usuarioObjetivo === false || !$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario participante inválido.']);
            }
            $rolParticipacion = (string)($_POST['rol'] ?? '');
            if (!in_array($rolParticipacion, ['AUTOR', 'COAUTOR'], true)) $responder(400, ['status'=>'ERROR','message'=>'Rol inválido.']);
            if ($pub->detalleArticulo((int)$id) === null && $pub->detalleLibro((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if (!$pub->usuarioParticipa((int)$id, (int)$usuarioObjetivo)) $responder(404, ['status'=>'ERROR','message'=>'Participación no encontrada.']);
            $participaciones = $pub->listarParticipaciones((int)$id);
            $participacion = array_values(array_filter($participaciones, static fn(array $fila): bool => (int)$fila['usuario'] === (int)$usuarioObjetivo));
            if ($participacion === []) $responder(404, ['status'=>'ERROR','message'=>'Participación no encontrada.']);
            $pub->actualizarParticipacion((int)$participacion[0]['id_publicacion_participacion'], $rolParticipacion);
            $responder(200, ['status'=>'OK','data'=>['id_publicacion'=>(int)$id, 'usuario'=>(int)$usuarioObjetivo, 'rol'=>$rolParticipacion]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible actualizar la participación.']);
        }
    }
    if ($op === 'participation_delete') {
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
            $usuarioObjetivo = $actorId;
            if ($global()) {
                $usuarioObjetivo = filter_var($_POST['usuario'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
                if ($usuarioObjetivo === false || !$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario participante inválido.']);
            }
            if ($pub->detalleArticulo((int)$id) === null && $pub->detalleLibro((int)$id) === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if (!$pub->usuarioParticipa((int)$id, (int)$usuarioObjetivo)) $responder(404, ['status'=>'ERROR','message'=>'Participación no encontrada.']);
            $participaciones = $pub->listarParticipaciones((int)$id);
            $participacion = array_values(array_filter($participaciones, static fn(array $fila): bool => (int)$fila['usuario'] === (int)$usuarioObjetivo));
            if ($participacion === []) $responder(404, ['status'=>'ERROR','message'=>'Participación no encontrada.']);
            $resultado = $pub->eliminarParticipacion((int)$participacion[0]['id_publicacion_participacion']);
            if (($resultado['filasAfectadas'] ?? 0) !== 1) $responder(404, ['status'=>'ERROR','message'=>'Participación no encontrada.']);
            $responder(200, ['status'=>'OK','data'=>['id_publicacion'=>(int)$id, 'usuario'=>(int)$usuarioObjetivo]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible eliminar la participación.']);
        }
    }
    if ($op === 'otra_list') {
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $usuarioObjetivo = $actorId;
            if ($global() && array_key_exists('usuario', $_POST)) {
                $usuarioObjetivo = filter_var($_POST['usuario'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
                if ($usuarioObjetivo === false || !$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario inválido.']);
            }
            if ($usuarioObjetivo === null) $responder(400, ['status'=>'ERROR','message'=>'Usuario contextual requerido.']);
            $responder(200, ['status'=>'OK','data'=>$pub->mostrarOtrPub((int)$usuarioObjetivo)]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible consultar las publicaciones.']);
        }
    }
    if ($op === 'otra_detail') {
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $id = filter_var($_POST['id_otra_pub'] ?? ($_POST['id_pub'] ?? null), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
            $detalle = $pub->mostrarOtrPubId((int)$id);
            if ($detalle === []) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if (!$global() && (int)$detalle[0]['usuario'] !== $actorId) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
            $responder(200, ['status'=>'OK','data'=>$detalle[0]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible consultar la publicación.']);
        }
    }
    if ($op === 'otra_create') {
        try { $csrf(); } catch (RuntimeException $e) { $responder(403, ['status'=>'ERROR','message'=>'CSRF inválido.']); }
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $tipoOtra = $_POST['tipo_otra_pub'] ?? ($_POST['tipo_pub'] ?? null);
            $descripcion = $_POST['descripcion'] ?? ($_POST['desc_pub'] ?? null);
            if (!is_string($tipoOtra) || !is_string($descripcion)) $responder(400, ['status'=>'ERROR','message'=>'Datos de publicación inválidos.']);
            $tipoOtra = trim($tipoOtra); $descripcion = trim($descripcion);
            if ($tipoOtra === '' || strlen($tipoOtra) > 50 || $descripcion === '' || strlen($descripcion) > 300) $responder(400, ['status'=>'ERROR','message'=>'Datos de publicación inválidos.']);
            $usuarioObjetivo = $actorId;
            if ($global() && array_key_exists('usuario', $_POST)) {
                $usuarioObjetivo = filter_var($_POST['usuario'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
                if ($usuarioObjetivo === false || !$usuariosModelo->usuarioInternoExiste((int)$usuarioObjetivo)) $responder(400, ['status'=>'ERROR','message'=>'Usuario inválido.']);
            }
            if ($usuarioObjetivo === null) $responder(400, ['status'=>'ERROR','message'=>'Usuario contextual requerido.']);
            $resultado = $pub->insertarOtraPub($tipoOtra, $descripcion, (int)$usuarioObjetivo);
            $id = (int)($resultado['idInsertado'] ?? 0);
            if (($resultado['filasAfectadas'] ?? 0) !== 1 || $id < 1) $responder(500, ['status'=>'ERROR','message'=>'No fue posible crear la publicación.']);
            $responder(201, ['status'=>'OK','data'=>['id_otra_pub'=>$id]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible crear la publicación.']);
        }
    }
    if ($op === 'otra_update') {
        try { $csrf(); } catch (RuntimeException $e) { $responder(403, ['status'=>'ERROR','message'=>'CSRF inválido.']); }
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $id = filter_var($_POST['id_otra_pub'] ?? ($_POST['id_pub'] ?? null), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            $tipoOtra = $_POST['tipo_otra_pub'] ?? ($_POST['tipo_pub'] ?? null);
            $descripcion = $_POST['descripcion'] ?? ($_POST['desc_pub'] ?? null);
            if ($id === false || !is_string($tipoOtra) || !is_string($descripcion)) $responder(400, ['status'=>'ERROR','message'=>'Datos de publicación inválidos.']);
            $tipoOtra = trim($tipoOtra); $descripcion = trim($descripcion);
            if ($tipoOtra === '' || strlen($tipoOtra) > 50 || $descripcion === '' || strlen($descripcion) > 300) $responder(400, ['status'=>'ERROR','message'=>'Datos de publicación inválidos.']);
            $detalle = $pub->mostrarOtrPubId((int)$id);
            if ($detalle === []) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if (!$global() && (int)$detalle[0]['usuario'] !== $actorId) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
            $pub->editarOtraPub($tipoOtra, $descripcion, (int)$id);
            $responder(200, ['status'=>'OK','data'=>['id_otra_pub'=>(int)$id]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible actualizar la publicación.']);
        }
    }
    if ($op === 'otra_delete') {
        try { $csrf(); } catch (RuntimeException $e) { $responder(403, ['status'=>'ERROR','message'=>'CSRF inválido.']); }
        $autenticado(); $actorId = $global() ? null : $actor();
        try {
            $id = filter_var($_POST['id_otra_pub'] ?? ($_POST['id_pub'] ?? null), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
            if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
            $detalle = $pub->mostrarOtrPubId((int)$id);
            if ($detalle === []) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            if (!$global() && (int)$detalle[0]['usuario'] !== $actorId) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
            $resultado = $pub->eliminarOtraPub((int)$id);
            if (($resultado['filasAfectadas'] ?? 0) !== 1) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
            $responder(200, ['status'=>'OK','data'=>['id_otra_pub'=>(int)$id]]);
        } catch (Throwable $e) {
            $responder(500, ['status'=>'ERROR','message'=>'No fue posible eliminar la publicación.']);
        }
    }
    if ($op === 'detail') {
        $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
        $autenticado(); $usuarioSesion = $global() ? null : $actor();
        if (!$global() && !$pub->usuarioParticipa((int)$id, $usuarioSesion)) $responder(403, ['status'=>'ERROR','message'=>'Acceso denegado.']);
        $tipoDetalle = $_POST['tipo'] ?? null;
        $detalle = in_array((int)$tipoDetalle, [1,2], true) ? $pub->detalleArticulo((int)$id) : $pub->detalleLibro((int)$id);
        if ($detalle === null) $responder(404, ['status'=>'ERROR','message'=>'Publicación no encontrada.']);
        $responder(200, ['status'=>'OK','data'=>$detalle]);
    }
    if ($op === 'participation_list') {
        $id = filter_var($_POST['id_publicacion'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
        if ($id === false) $responder(400, ['status'=>'ERROR','message'=>'ID inválido.']);
        $autenticado(); $usuarioSesion=$global() ? null : $actor(); if (!$global() && !$pub->usuarioParticipa((int)$id,$usuarioSesion)) $responder(403,['status'=>'ERROR','message'=>'Acceso denegado.']);
        $datos=$pub->listarParticipaciones((int)$id); if(!$global()) $datos=array_values(array_filter($datos, static fn($p)=>(int)$p['usuario']===$usuarioSesion));
        $responder(200,['status'=>'OK','data'=>$datos]);
    }
    if ($op === 'editorial_search') { $autenticado(); if (!$global()) $actor(); $q=trim((string)($_POST['busqueda']??'')); $responder(200,['status'=>'OK','data'=>$pub->cargarEditorial($q)]); }
} catch (RuntimeException $e) { $responder(401, ['status'=>'ERROR','message'=>'Operación no autorizada.']); }
