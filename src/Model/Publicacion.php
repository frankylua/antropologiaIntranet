<?php
declare(strict_types=1);
namespace App\Model;

class Publicacion {
    public function __construct(){
    }
    private function validarRolParticipacion(string $rol): void {
        if (!in_array($rol, ['AUTOR', 'COAUTOR'], true)) {
            throw new \InvalidArgumentException('Rol de participación inválido.');
        }
    }
    private function consulta(string $sql, array $parametros = []): array {
        $stmt = conexion()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function listarParticipaciones(int $publicacion): array {
        return $this->consulta('SELECT id_publicacion_participacion, usuario, nombre_externo, rol FROM publicacion_participacion WHERE publicacion = :publicacion', [':publicacion' => $publicacion]);
    }
    public function usuarioParticipa(int $publicacion, int $usuario): bool {
        return $this->consulta('SELECT id_publicacion_participacion FROM publicacion_participacion WHERE publicacion = :publicacion AND usuario = :usuario', [':publicacion' => $publicacion, ':usuario' => $usuario]) !== [];
    }
    private function normalizarParticipantes(array $participantes): array {
        if (array_keys($participantes) !== ['AUTOR', 'COAUTOR']) throw new \InvalidArgumentException('Se requiere exactamente Autor y Coautor.');
        $usuarios = new Usuario();
        foreach ($participantes as $rol => $participante) {
            $this->validarRolParticipacion($rol);
            if (!is_array($participante)) throw new \InvalidArgumentException('Participante invalido.');
            $usuario = $participante['usuario'] ?? null; $nombre = $participante['nombre_externo'] ?? null;
            $interno = is_int($usuario) && $usuario > 0 && $nombre === null;
            $externo = $usuario === null && is_string($nombre) && trim($nombre) !== '' && strlen(trim($nombre)) <= 120;
            if (!$interno && !$externo) throw new \InvalidArgumentException('Autoría interna o externa invalida.');
            if ($interno && !$usuarios->usuarioAcademicoExiste($usuario)) throw new \InvalidArgumentException('Usuario academico invalido.');
            $participantes[$rol] = ['usuario'=>$interno ? $usuario : null, 'nombre_externo'=>$externo ? trim($nombre) : null];
        }
        if ($participantes['AUTOR']['usuario'] !== null && $participantes['AUTOR']['usuario'] === $participantes['COAUTOR']['usuario']) throw new \InvalidArgumentException('El mismo usuario no puede ocupar ambos roles.');
        return $participantes;
    }
    private function guardarParticipantes(int $publicacion, array $participantes): void {
        foreach ($this->normalizarParticipantes($participantes) as $rol => $participante) {
            $r = ejecutarEscritura('INSERT INTO publicacion_participacion (publicacion, usuario, nombre_externo, rol) VALUES (:publicacion, :usuario, :nombre, :rol)', [':publicacion'=>$publicacion, ':usuario'=>$participante['usuario'], ':nombre'=>$participante['nombre_externo'], ':rol'=>$rol]);
            if ($r['filasAfectadas'] !== 1) throw new \RuntimeException('No se creo la autoria principal.');
        }
    }
    public function reemplazarParticipantes(int $publicacion, array $participantes): void {
        $participantes = $this->normalizarParticipantes($participantes);
        ejecutarEscritura('DELETE FROM publicacion_participacion WHERE publicacion=:publicacion', [':publicacion'=>$publicacion]);
        $this->guardarParticipantes($publicacion, $participantes);
    }
    public function insertar($usuario,$nombre,$autores,$anio,$estado){
        $resultado = ejecutarEscritura('INSERT INTO publicacion (nombre, otros_autores, anio, estado, usuario) VALUES (:nombre, :autores, :anio, :estado, NULL)', [':nombre'=>$nombre, ':autores'=>$autores === '' ? null : $autores, ':anio'=>(int)$anio, ':estado'=>(int)$estado], true);
        if ($resultado['filasAfectadas'] !== 1 || (int)$resultado['idInsertado'] < 1) throw new \RuntimeException('No se creó la publicación.');
        return (int)$resultado['idInsertado'];
    }
    public function crearArticuloConParticipacion(array $datos, int $usuario, string $rol): int {
        $this->validarRolParticipacion($rol);
        $pdo = conexion();
        try {
            $pdo->beginTransaction();
            $id = $this->insertar(0, $datos['nombre'], $datos['autores'] ?? '', $datos['anio'], $datos['estado']);
            $articulo = ejecutarEscritura('INSERT INTO articulo_revista (tipo,titulo,issn,indizacion,factor_impacto,publicacion) VALUES (:tipo,:titulo,:issn,:indizacion,:factor,:publicacion)', [':tipo'=>$datos['tipo'],':titulo'=>$datos['titulo'],':issn'=>$datos['issn'],':indizacion'=>$datos['indizacion'],':factor'=>$datos['fac_imp'],':publicacion'=>$id]);
            if ($articulo['filasAfectadas'] !== 1) throw new \RuntimeException('No se creó el artículo.');
            $participacion = $this->crearParticipacion($id, $usuario, $rol);
            if ($participacion['filasAfectadas'] !== 1) throw new \RuntimeException('No se creó la participación.');
            $pdo->commit();
            return $id;
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $error;
        }
    }
    public function crearArticuloPrincipal(array $datos, array $participantes): int {
        $pdo = conexion();
        try {
            $pdo->beginTransaction();
            $id = $this->insertar(0, $datos['nombre'], $datos['autores'] ?? '', $datos['anio'], $datos['estado']);
            $r = ejecutarEscritura('INSERT INTO articulo_revista (tipo,titulo,issn,indizacion,factor_impacto,publicacion) VALUES (:tipo,:titulo,:issn,:indizacion,:factor,:publicacion)', [':tipo'=>$datos['tipo'],':titulo'=>$datos['titulo'],':issn'=>$datos['issn'],':indizacion'=>$datos['indizacion'],':factor'=>$datos['fac_imp'],':publicacion'=>$id]);
            if ($r['filasAfectadas'] !== 1) throw new \RuntimeException('No se creo el articulo.');
            $this->guardarParticipantes($id, $participantes);
            $pdo->commit(); return $id;
        } catch (\Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    public function insertarLibro($publicacion,$tipo,$rol,$ref_ext,$traduccion,$lugar){
        $resultado = ejecutarEscritura('INSERT INTO libro (tipo, rol, ref_ext, traduccion, lugar, publicacion) VALUES (:tipo,:rol,:referato,:traduccion,:lugar,:publicacion)', [':tipo'=>(int)$tipo, ':rol'=>(int)$rol, ':referato'=>(int)$ref_ext, ':traduccion'=>(int)$traduccion, ':lugar'=>$lugar, ':publicacion'=>(int)$publicacion], true);
        if ($resultado['filasAfectadas'] !== 1 || (int)$resultado['idInsertado'] < 1) throw new \RuntimeException('No se creó el libro.');
        return (int)$resultado['idInsertado'];
    }
    public function crearLibroConParticipacion(array $datos, int $usuario, string $rolParticipacion): int {
        $this->validarRolParticipacion($rolParticipacion); $pdo=conexion();
        $existentes=$datos['editoriales_existentes']??[]; $nuevas=$datos['editoriales_nuevas']??[];
        if(!is_array($existentes)||!is_array($nuevas)) throw new \InvalidArgumentException('Contrato editorial inválido.');
        try { $pdo->beginTransaction(); $publicacion=$this->insertar(0,$datos['nombre'],$datos['autores']??'',$datos['anio'],$datos['estado']); $libro=$this->insertarLibro($publicacion,$datos['tipo'],$datos['rol'],$datos['ref_ext'],$datos['traduccion'],$datos['lugar']); $ids=[]; foreach($existentes as $id){if(!is_int($id)||$id<1||$this->consulta('SELECT id_editorial FROM editorial WHERE id_editorial=:id',[':id'=>$id])===[])throw new \RuntimeException('Editorial inexistente.');$ids[$id]=true;} foreach($nuevas as $nombre){if(!is_string($nombre)||($nombre=trim($nombre))===''||strlen($nombre)>60)throw new \RuntimeException('Editorial nueva inválida.');$ids[$this->insertarEditorial($nombre)]=true;} foreach(array_keys($ids) as $editorial){$r=$this->insertarLibroEditorial($editorial,$libro);if($r['filasAfectadas']!==1)throw new \RuntimeException('No se asoció editorial.');} $participacion=$this->crearParticipacion($publicacion,$usuario,$rolParticipacion); if($participacion['filasAfectadas']!==1) throw new \RuntimeException('No se creó la participación.'); $pdo->commit(); return $publicacion; } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    public function crearLibroPrincipal(array $datos, array $participantes): int {
        $this->normalizarParticipantes($participantes);
        $pdo=conexion(); $existentes=$datos['editoriales_existentes']??[]; $nuevas=$datos['editoriales_nuevas']??[];
        if(!is_array($existentes)||!is_array($nuevas)) throw new \InvalidArgumentException('Contrato editorial invalido.');
        try { $pdo->beginTransaction(); $publicacion=$this->insertar(0,$datos['nombre'],$datos['autores']??'',$datos['anio'],$datos['estado']); $libro=$this->insertarLibro($publicacion,$datos['tipo'],$datos['rol'],$datos['ref_ext'],$datos['traduccion'],$datos['lugar']); $ids=[]; foreach($existentes as $id){if(!is_int($id)||$id<1||$this->consulta('SELECT id_editorial FROM editorial WHERE id_editorial=:id',[':id'=>$id])===[])throw new \RuntimeException('Editorial inexistente.');$ids[$id]=true;} foreach($nuevas as $nombre){if(!is_string($nombre)||($nombre=trim($nombre))===''||strlen($nombre)>60)throw new \RuntimeException('Editorial nueva invalida.');$ids[$this->insertarEditorial($nombre)]=true;} foreach(array_keys($ids) as $editorial){$r=$this->insertarLibroEditorial($editorial,$libro);if($r['filasAfectadas']!==1)throw new \RuntimeException('No se asocio editorial.');} $this->guardarParticipantes($publicacion,$participantes); $pdo->commit(); return $publicacion; } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    public function insertarEditorial($editorial){
        if (ctype_digit((string)$editorial)) { if ($this->consulta('SELECT id_editorial,nombre FROM editorial WHERE id_editorial=:id',[':id'=>(int)$editorial])===[]) throw new \RuntimeException('Editorial inexistente.'); return (int)$editorial; }
        $nombre=trim((string)$editorial); if($nombre===''||strlen($nombre)>60) throw new \RuntimeException('Editorial inválida.'); $existente=$this->consulta('SELECT id_editorial,nombre FROM editorial WHERE nombre=:nombre',[':nombre'=>$nombre]); if($existente!==[]) return (int)$existente[0]['id_editorial']; $r=ejecutarEscritura('INSERT INTO editorial (nombre) VALUES (:nombre)',[':nombre'=>$nombre],true); if($r['filasAfectadas']!==1||(int)$r['idInsertado']<1)throw new \RuntimeException('No se creó editorial.'); return (int)$r['idInsertado'];
    }   
    public function insertarLibroEditorial($id_editorial,$id_libro){
        return ejecutarEscritura('INSERT INTO libro_editorial (editorial,libro) VALUES (:editorial,:libro)', [':editorial'=>(int)$id_editorial,':libro'=>(int)$id_libro]);
    }
    public function insertarOtraPub($tipo_pub,$desc_pub,$usuario){
        return ejecutarEscritura('INSERT INTO otra_publicacion (tipo,descripcion,usuario) VALUES (:tipo,:descripcion,:usuario)', [':tipo'=>$tipo_pub,':descripcion'=>$desc_pub,':usuario'=>(int)$usuario], true);
    }
    public function editarArtRev($id_pub,$titulo,$indizacion,$fac_imp,$issn){
        return ejecutarEscritura('UPDATE articulo_revista SET titulo=:titulo, issn=:issn, indizacion=:indizacion, factor_impacto=:factor WHERE publicacion=:publicacion', [':titulo'=>$titulo, ':issn'=>$issn, ':indizacion'=>(int)$indizacion, ':factor'=>$fac_imp, ':publicacion'=>(int)$id_pub]);
    }
    public function actualizarArticulo(int $id, array $datos): void {
        if ($this->consulta('SELECT id_publicacion FROM publicacion WHERE id_publicacion=:id', [':id'=>$id]) === [] || $this->consulta('SELECT id_art_rev FROM articulo_revista WHERE publicacion=:id', [':id'=>$id]) === []) throw new \RuntimeException('Publicación o artículo inexistente.');
        $pdo=conexion(); try { $pdo->beginTransaction(); ejecutarEscritura('UPDATE publicacion SET nombre=:nombre, otros_autores=:autores, anio=:anio, estado=:estado WHERE id_publicacion=:id', [':nombre'=>$datos['nombre'], ':autores'=>$datos['autores'] ?: null, ':anio'=>$datos['anio'], ':estado'=>$datos['estado'], ':id'=>$id]); $this->editarArtRev($id,$datos['titulo'],$datos['indizacion'],$datos['fac_imp'],$datos['issn']); $pdo->commit(); } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    public function actualizarArticuloPrincipal(int $id, array $datos, array $participantes): void {
        if ($this->consulta('SELECT id_publicacion FROM publicacion WHERE id_publicacion=:id', [':id'=>$id]) === [] || $this->consulta('SELECT id_art_rev FROM articulo_revista WHERE publicacion=:id', [':id'=>$id]) === []) throw new \RuntimeException('Publicacion o articulo inexistente.');
        $participantes = $this->normalizarParticipantes($participantes); $pdo=conexion();
        try { $pdo->beginTransaction(); ejecutarEscritura('UPDATE publicacion SET nombre=:nombre, otros_autores=:autores, anio=:anio, estado=:estado WHERE id_publicacion=:id', [':nombre'=>$datos['nombre'], ':autores'=>$datos['autores'] ?: null, ':anio'=>$datos['anio'], ':estado'=>$datos['estado'], ':id'=>$id]); $this->editarArtRev($id,$datos['titulo'],$datos['indizacion'],$datos['fac_imp'],$datos['issn']); $this->reemplazarParticipantes($id,$participantes); $pdo->commit(); } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }
    public function editarLibro($id_pub,$tipo_lib,$rol,$ref_ext,$traduccion,$lugar){
        return ejecutarEscritura('UPDATE libro SET tipo=:tipo, rol=:rol, ref_ext=:ref, traduccion=:traduccion, lugar=:lugar WHERE publicacion=:publicacion', [':tipo'=>(int)$tipo_lib,':rol'=>(int)$rol,':ref'=>(int)$ref_ext,':traduccion'=>(int)$traduccion,':lugar'=>$lugar,':publicacion'=>(int)$id_pub]);
    }
    public function actualizarLibro(int $id, array $datos, array $existentes, array $nuevas): void {
        $pdo=conexion(); $pub=$this->consulta('SELECT id_publicacion FROM publicacion WHERE id_publicacion=:id',[':id'=>$id]); $libro=$this->consulta('SELECT id_libro FROM libro WHERE publicacion=:id',[':id'=>$id]); if($pub===[]||$libro===[]) throw new \RuntimeException('Publicación o libro inexistente.');
        try { $pdo->beginTransaction(); ejecutarEscritura('UPDATE publicacion SET nombre=:nombre, otros_autores=:autores, anio=:anio, estado=:estado WHERE id_publicacion=:id',[':nombre'=>$datos['nombre'],':autores'=>$datos['autores']?:null,':anio'=>$datos['anio'],':estado'=>$datos['estado'],':id'=>$id]); $this->editarLibro($id,$datos['tipo'],$datos['rol'],$datos['ref_ext'],$datos['traduccion'],$datos['lugar']); $ids=[]; foreach($existentes as $eid){$eid=(int)$eid;if($eid<1||$this->consulta('SELECT id_editorial FROM editorial WHERE id_editorial=:id',[':id'=>$eid])===[])throw new \RuntimeException('Editorial inexistente.');$ids[$eid]=true;} foreach($nuevas as $nombre){$nombre=trim((string)$nombre);if($nombre===''||strlen($nombre)>60)throw new \RuntimeException('Editorial nueva inválida.');$encontrada=$this->consulta('SELECT id_editorial FROM editorial WHERE nombre=:nombre',[':nombre'=>$nombre]);if($encontrada!==[])$eid=(int)$encontrada[0]['id_editorial'];else{$r=ejecutarEscritura('INSERT INTO editorial (nombre) VALUES (:nombre)',[':nombre'=>$nombre],true);$eid=(int)$r['idInsertado'];if($r['filasAfectadas']!==1||$eid<1)throw new \RuntimeException('No se creó editorial.');}$ids[$eid]=true;} $lid=(int)$libro[0]['id_libro'];$actual=$this->consulta('SELECT editorial FROM libro_editorial WHERE libro=:libro',[':libro'=>$lid]);$actuales=[];foreach($actual as $f)$actuales[(int)$f['editorial']]=true;foreach(array_diff_key($ids,$actuales) as $eid=>$_){$r=ejecutarEscritura('INSERT INTO libro_editorial (libro,editorial) VALUES (:libro,:editorial)',[':libro'=>$lid,':editorial'=>$eid]);if($r['filasAfectadas']!==1)throw new \RuntimeException('No se asoció editorial.');}foreach(array_diff_key($actuales,$ids) as $eid=>$_){ejecutarEscritura('DELETE FROM libro_editorial WHERE libro=:libro AND editorial=:editorial',[':libro'=>$lid,':editorial'=>$eid]);}$pdo->commit(); }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public function actualizarLibroPrincipal(int $id, array $datos, array $existentes, array $nuevas, array $participantes): void {
        $participantes=$this->normalizarParticipantes($participantes); $pdo=conexion(); $pub=$this->consulta('SELECT id_publicacion FROM publicacion WHERE id_publicacion=:id',[':id'=>$id]); $libro=$this->consulta('SELECT id_libro FROM libro WHERE publicacion=:id',[':id'=>$id]); if($pub===[]||$libro===[]) throw new \RuntimeException('Publicacion o libro inexistente.');
        try { $pdo->beginTransaction(); ejecutarEscritura('UPDATE publicacion SET nombre=:nombre, otros_autores=:autores, anio=:anio, estado=:estado WHERE id_publicacion=:id',[':nombre'=>$datos['nombre'],':autores'=>$datos['autores']?:null,':anio'=>$datos['anio'],':estado'=>$datos['estado'],':id'=>$id]); $this->editarLibro($id,$datos['tipo'],$datos['rol'],$datos['ref_ext'],$datos['traduccion'],$datos['lugar']); $ids=[]; foreach($existentes as $eid){$eid=(int)$eid;if($eid<1||$this->consulta('SELECT id_editorial FROM editorial WHERE id_editorial=:id',[':id'=>$eid])===[])throw new \RuntimeException('Editorial inexistente.');$ids[$eid]=true;} foreach($nuevas as $nombre){$nombre=trim((string)$nombre);if($nombre===''||strlen($nombre)>60)throw new \RuntimeException('Editorial nueva invalida.');$encontrada=$this->consulta('SELECT id_editorial FROM editorial WHERE nombre=:nombre',[':nombre'=>$nombre]);if($encontrada!==[])$eid=(int)$encontrada[0]['id_editorial'];else{$r=ejecutarEscritura('INSERT INTO editorial (nombre) VALUES (:nombre)',[':nombre'=>$nombre],true);$eid=(int)$r['idInsertado'];if($r['filasAfectadas']!==1||$eid<1)throw new \RuntimeException('No se creo editorial.');}$ids[$eid]=true;} $lid=(int)$libro[0]['id_libro'];$actual=$this->consulta('SELECT editorial FROM libro_editorial WHERE libro=:libro',[':libro'=>$lid]);$actuales=[];foreach($actual as $f)$actuales[(int)$f['editorial']]=true;foreach(array_diff_key($ids,$actuales) as $eid=>$_){$r=ejecutarEscritura('INSERT INTO libro_editorial (libro,editorial) VALUES (:libro,:editorial)',[':libro'=>$lid,':editorial'=>$eid]);if($r['filasAfectadas']!==1)throw new \RuntimeException('No se asocio editorial.');}foreach(array_diff_key($actuales,$ids) as $eid=>$_){ejecutarEscritura('DELETE FROM libro_editorial WHERE libro=:libro AND editorial=:editorial',[':libro'=>$lid,':editorial'=>$eid]);}$this->reemplazarParticipantes($id,$participantes); $pdo->commit(); }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
    }
    public function editarOtraPub($tipo_pub,$desc_pub,$id_pub){
        return ejecutarEscritura('UPDATE otra_publicacion SET tipo=:tipo, descripcion=:descripcion WHERE id_otra_pub=:id', [':tipo'=>$tipo_pub,':descripcion'=>$desc_pub,':id'=>(int)$id_pub]);
    }
    //buscar editoriales
    public function cargarEditorial($busqueda){        
        return $this->consulta('SELECT id_editorial,nombre FROM editorial WHERE nombre LIKE :busqueda ORDER BY nombre', [':busqueda'=>(string)$busqueda.'%']);
    }
    public function cargarEditLib($libro){
        return $this->consulta('SELECT e.id_editorial,e.nombre FROM editorial e INNER JOIN libro_editorial l ON l.editorial=e.id_editorial WHERE l.libro=:libro', [':libro'=>(int)$libro]);
    }
    public function mostrarArtRev($usuario){
         return $this->consulta("SELECT DISTINCT p.nombre AS nombre_revista,p.otros_autores,p.anio,e.nombre AS estado_publicacion,a.tipo,a.titulo,a.issn,i.nombre AS indizacion,a.factor_impacto,p.id_publicacion,COALESCE(NULLIF(TRIM(CONCAT_WS(' ', ua.nombres, ua.ap_pat, ua.ap_mat)), ''), pa.nombre_externo) AS autor_nombre,COALESCE(NULLIF(TRIM(CONCAT_WS(' ', uc.nombres, uc.ap_pat, uc.ap_mat)), ''), pc.nombre_externo) AS coautor_nombre FROM publicacion p JOIN articulo_revista a ON p.id_publicacion=a.publicacion JOIN publicacion_participacion pp ON pp.publicacion=p.id_publicacion LEFT JOIN publicacion_participacion pa ON pa.publicacion=p.id_publicacion AND pa.rol='AUTOR' LEFT JOIN usuario ua ON ua.id_usuario=pa.usuario LEFT JOIN publicacion_participacion pc ON pc.publicacion=p.id_publicacion AND pc.rol='COAUTOR' LEFT JOIN usuario uc ON uc.id_usuario=pc.usuario JOIN indizacion i ON a.indizacion=i.id_ind JOIN estado_pub e ON p.estado=e.id_est_pub WHERE pp.usuario=:usuario", [':usuario'=>(int)$usuario]);
    }
    public function detalleArticulo(int $idPublicacion): ?array {
        $filas = $this->consulta('SELECT p.nombre,p.otros_autores,p.anio,p.estado,a.tipo,a.titulo,a.issn,a.indizacion,a.factor_impacto,p.id_publicacion FROM publicacion p INNER JOIN articulo_revista a ON a.publicacion=p.id_publicacion WHERE p.id_publicacion=:id', [':id'=>$idPublicacion]);
        if ($filas === []) return null;
        $detalle = $filas[0]; $this->agregarParticipantesDetalle($detalle, $idPublicacion); return $detalle;
    }
    public function mostrarLibros($usuario){
        $libros = $this->consulta("SELECT DISTINCT p.nombre AS nom_pub,p.otros_autores,p.anio,e.nombre AS nom_est,l.id_libro,l.tipo,l.rol,l.ref_ext,l.traduccion,l.lugar,p.id_publicacion,COALESCE(NULLIF(TRIM(CONCAT_WS(' ', ua.nombres, ua.ap_pat, ua.ap_mat)), ''), pa.nombre_externo) AS autor_nombre,COALESCE(NULLIF(TRIM(CONCAT_WS(' ', uc.nombres, uc.ap_pat, uc.ap_mat)), ''), pc.nombre_externo) AS coautor_nombre FROM publicacion p JOIN libro l ON p.id_publicacion=l.publicacion JOIN publicacion_participacion pp ON pp.publicacion=p.id_publicacion LEFT JOIN publicacion_participacion pa ON pa.publicacion=p.id_publicacion AND pa.rol='AUTOR' LEFT JOIN usuario ua ON ua.id_usuario=pa.usuario LEFT JOIN publicacion_participacion pc ON pc.publicacion=p.id_publicacion AND pc.rol='COAUTOR' LEFT JOIN usuario uc ON uc.id_usuario=pc.usuario JOIN estado_pub e ON p.estado=e.id_est_pub WHERE pp.usuario=:usuario", [':usuario'=>(int)$usuario]);
        if ($libros === []) return [];

        $parametros = [];
        $marcadores = [];
        foreach ($libros as $indice => $libro) {
            $marcador = ':libro' . $indice;
            $marcadores[] = $marcador;
            $parametros[$marcador] = (int)$libro['id_libro'];
        }
        $editorialesPorLibro = [];
        foreach ($this->consulta('SELECT le.libro,e.id_editorial,e.nombre FROM libro_editorial le INNER JOIN editorial e ON e.id_editorial=le.editorial WHERE le.libro IN (' . implode(',', $marcadores) . ') ORDER BY e.nombre', $parametros) as $editorial) {
            $editorialesPorLibro[(int)$editorial['libro']][] = ['id_editorial'=>(int)$editorial['id_editorial'], 'nombre'=>$editorial['nombre']];
        }
        foreach ($libros as &$libro) $libro['editoriales'] = $editorialesPorLibro[(int)$libro['id_libro']] ?? [];
        unset($libro);
        return $libros;
    }
    public function detalleLibro(int $idPublicacion): ?array {
        $filas = $this->consulta('SELECT p.id_publicacion,p.nombre,p.otros_autores,p.estado,p.anio,l.id_libro,l.tipo,l.rol,l.ref_ext,l.traduccion,l.lugar FROM publicacion p INNER JOIN libro l ON l.publicacion=p.id_publicacion WHERE p.id_publicacion=:id', [':id'=>$idPublicacion]);
        if ($filas === []) return null;
        $libro = $filas[0];
        $libro['editoriales'] = $this->cargarEditLib((int)$libro['id_libro']); $this->agregarParticipantesDetalle($libro, $idPublicacion);
        return $libro;
    }
    private function agregarParticipantesDetalle(array &$detalle, int $publicacion): void {
        $porRol = [];
        foreach ($this->listarParticipaciones($publicacion) as $fila) {
            $interno = $fila['usuario'] !== null;
            $academico = $interno ? (new Usuario())->obtenerUsuarioAcademicoPorId((int)$fila['usuario']) : null;
            $porRol[$fila['rol']] = ['rol'=>$fila['rol'], 'tipo'=>$interno ? 'INTERNO' : 'EXTERNO', 'usuario'=>$interno ? (int)$fila['usuario'] : null, 'nombre'=>$academico['nombre'] ?? null, 'nombre_externo'=>$interno ? null : $fila['nombre_externo']];
        }
        $detalle['autor'] = $porRol['AUTOR'] ?? null; $detalle['coautor'] = $porRol['COAUTOR'] ?? null;
    }
    public function mostrarOtrPub($usuario){
        return $this->consulta('SELECT id_otra_pub,tipo,descripcion,usuario FROM otra_publicacion WHERE usuario=:usuario', [':usuario'=>(int)$usuario]);
    }
    public function mostrarOtrPubId($id_pub){
        return $this->consulta('SELECT id_otra_pub,tipo,descripcion,usuario FROM otra_publicacion WHERE id_otra_pub=:id', [':id'=>(int)$id_pub]);
    }
    public function eliminar($id_pub){
        return ejecutarEscritura('DELETE FROM publicacion WHERE id_publicacion = :id', [':id'=>(int)$id_pub]);
    }
    public function eliminarOtraPub($id_pub){
        return ejecutarEscritura('DELETE FROM otra_publicacion WHERE id_otra_pub=:id', [':id'=>(int)$id_pub]);
    }
}
?>
