<?php
declare(strict_types=1);
namespace App\Model;

class Docente extends Usuario{
    private const PERMISO_ADMIN = 1;
    private const PERMISO_COMITE = 2;
    private const PERMISO_DOCENTE = 4;
    private const ESTADO_PENDIENTE = 1;
    private const ESTADO_ACEPTADO = 2;
    private const ESTADO_RECHAZADO = 3;

    public function __construct(){
    }
    public function insertar($catAcad,$anioIng,$vinculo,$usuario,$estadoProfesor){
        $estadoProfesor = (int) $estadoProfesor;
        if (!in_array($estadoProfesor, [self::ESTADO_PENDIENTE, self::ESTADO_ACEPTADO], true)) {
            return false;
        }
        //ingesar docente
        $sql="INSERT INTO profesor (id_profesor,usuario,vinculo,cat_academica,anio_ingreso,estado_profesor) VALUES (NULL,'$usuario','$vinculo','$catAcad','$anioIng','$estadoProfesor')";
        return ejecutarConsulta($sql);   
    }
   
    // public function mostrarPorId($id_curso){
    //     $sql="SELECT * FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  WHERE c.id_curso='$id_curso'";
    //     return resultadoConsultaPorId($sql);
    // }
        
    // public function mostrar(){
         
    //     $sql="SELECT c.id_curso,n.nom_curso,c.periodo,c.anio_curso FROM curso c JOIN nombre_curso n ON c.nom_curso=n.id_nom_curso  ORDER BY c.anio_curso DESC";
    //     return ejecutarConsultaResultados($sql);
        
    // }

    public function buscarProf($busqueda){
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'WHERE p.estado_profesor = :estado_profesor '
            . 'AND u.nombres LIKE :busqueda ORDER BY u.nombres';
        $parametros = [
            'estado_profesor' => self::ESTADO_ACEPTADO,
            'busqueda' => $busqueda . '%',
        ];
        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll(\PDO::FETCH_BOTH);
    }
    public function buscarProfAdministrativo($busqueda, int $excluirLogin = 0, int $estadoProfesor = 0){
        if (!in_array($estadoProfesor, [0, 1, 2, 3, 4], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login WHERE u.nombres LIKE :busqueda';
        $parametros = ['busqueda' => $busqueda . '%'];
        if ($estadoProfesor !== 4) $sql .= ' AND p.estado_profesor<>4';
        if ($excluirLogin > 0 && $estadoProfesor !== 4) {
            $sql .= ' AND l.id_login <> :excluir_login';
            $parametros['excluir_login'] = $excluirLogin;
        }
        if ($estadoProfesor > 0) {
            $sql .= ' AND p.estado_profesor = :estado_profesor';
            $parametros['estado_profesor'] = $estadoProfesor;
        }
        $sql .= ' ORDER BY u.nombres';
        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll();
    }
    public function mostrarProf(int $excluirLogin = 0, int $estadoProfesor = 0){
        if (!in_array($estadoProfesor, [0, 1, 2, 3, 4], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login';
        $parametros = [];
        $condiciones = $estadoProfesor===4 ? [] : ['p.estado_profesor<>4'];
        if ($excluirLogin > 0 && $estadoProfesor !== 4) {
            $condiciones[] = 'l.id_login <> :excluir_login';
            $parametros['excluir_login'] = $excluirLogin;
        }
        if ($estadoProfesor > 0) {
            $condiciones[] = 'p.estado_profesor = :estado_profesor';
            $parametros['estado_profesor'] = $estadoProfesor;
        }
        if ($condiciones !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $sql .= ' ORDER BY u.nombres';
        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll();
    }
    public function mostrarProfId($id_usu){    
        $sql="SELECT u.id_usuario, u.pais_res, u.pais_nac, u.login, u.fecha_nac, u.nombres, u.ap_mat, u.ap_pat, u.tipo_doc, u.nro_doc, u.inst_usuario, u.genero, u.region, u.comuna, u.telefono, u.direccion, u.cont_em, u.tel_em, l.id_login, l.correo, p.id_profesor, p.usuario, p.vinculo, p.cat_academica, p.anio_ingreso, p.estado_profesor, i.id_inst, i.inst, pu.id_pueblo, pu.pueblo, lu.id_linea_usuario, lu.linea_inv, li.id_linea_inv, li.linea, pr.pais as p_resi, pn.pais as p_nac FROM usuario u JOIN login l ON u.login=l.id_login JOIN profesor p ON u.id_usuario=p.usuario JOIN institucion i ON u.inst_usuario=i.id_inst JOIN pais pn ON u.pais_nac=pn.id_pais JOIN pais pr ON u.pais_res=pr.id_pais JOIN pueblo pu ON u.pueblo=pu.id_pueblo LEFT JOIN linea_usuario lu ON u.id_usuario=lu.usuario LEFT JOIN linea_investigacion li ON li.id_linea_inv=lu.linea_inv WHERE u.id_usuario= :id_usuario order by u.nombres";
        $consulta = conexion()->prepare($sql);
        $consulta->execute(['id_usuario' => (int) $id_usu]);
        return $consulta->fetchAll();
    }
    public function obtenerIdentidadProfesorPorUsuario(int $idUsuario): ?array{
        $consulta = conexion()->prepare(
            'SELECT u.id_usuario, u.login AS id_login, p.id_profesor, p.estado_profesor '
            . 'FROM usuario u JOIN profesor p ON p.usuario = u.id_usuario '
            . 'WHERE u.id_usuario = :id_usuario'
        );
        $consulta->execute(['id_usuario' => $idUsuario]);
        $filas = $consulta->fetchAll(\PDO::FETCH_ASSOC);
        return count($filas) === 1 ? $filas[0] : null;
    }
    public function obtenerRolAdministrativo(int $idLogin): string{
        $consulta = conexion()->prepare(
            'SELECT id_permiso FROM permiso_login '
            . 'WHERE id_login = :id_login AND id_permiso IN (:admin, :comite) '
            . 'ORDER BY id_permiso'
        );
        $consulta->execute([
            'id_login' => $idLogin,
            'admin' => self::PERMISO_ADMIN,
            'comite' => self::PERMISO_COMITE,
        ]);
        $permisos = array_map('intval', $consulta->fetchAll(\PDO::FETCH_COLUMN));
        if ($permisos === [self::PERMISO_ADMIN]) {
            return 'admin';
        }
        if ($permisos === [self::PERMISO_COMITE]) {
            return 'comite';
        }
        return $permisos === [] ? 'none' : 'conflicto';
    }
    public function mostrarDatosProg($id_usu){
        $sql="SELECT u.inst_usuario,u.id_usuario,p.id_profesor,p.vinculo,p.cat_academica,p.anio_ingreso,p.estado_profesor,l.linea_inv,lo.id_login FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario LEFT JOIN linea_usuario l ON u.id_usuario=l.usuario JOIN login lo ON u.login=lo.id_login WHERE u.id_usuario=:id_usuario";
        $consulta = conexion()->prepare($sql);
        $consulta->execute(['id_usuario' => (int) $id_usu]);
        return $consulta->fetchAll();
    }
    public function mostrarProfTipo($tipo, int $excluirLogin = 0, int $estadoProfesor = 0){
        if (!in_array($estadoProfesor, [0, 1, 2, 3, 4], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login WHERE p.vinculo=:vinculo';
        $parametros = ['vinculo' => (int) $tipo];
        if ($estadoProfesor !== 4) $sql .= ' AND p.estado_profesor<>4';
        if ($excluirLogin > 0 && $estadoProfesor !== 4) {
            $sql .= ' AND l.id_login <> :excluir_login';
            $parametros['excluir_login'] = $excluirLogin;
        }
        if ($estadoProfesor > 0) {
            $sql .= ' AND p.estado_profesor = :estado_profesor';
            $parametros['estado_profesor'] = $estadoProfesor;
        }
        $sql .= ' ORDER BY u.nombres';
        $consulta = conexion()->prepare($sql);
        $consulta->execute($parametros);
        return $consulta->fetchAll();
    }
    public function editarDatProg($id_usu,$vinculo,$catAcad,$anioIng){
        $sql="UPDATE profesor SET vinculo='$vinculo', cat_academica='$catAcad',anio_ingreso='$anioIng'  WHERE usuario ='$id_usu'";
        return ejecutarConsulta($sql);
    }

    /** Confirma persona y credenciales juntas; no modifica estados, roles ni programa. */
    public function editarPersonalAtomico(int $actor, array $rolesSesion, ?int $usuarioObjetivo, ?int $loginObjetivo, array $datos): void
    {
        $pdo = conexion();
        if ($pdo->inTransaction()) throw new \LogicException('TRANSACCION_YA_ABIERTA');
        $iniciada = false;
        try {
            if (!$pdo->beginTransaction()) throw new \RuntimeException('TRANSACCION_NO_INICIADA');
            $iniciada = true;
            $q = $pdo->prepare('SELECT id_login FROM login WHERE id_login=:login FOR UPDATE');
            $q->execute(['login'=>$actor]);
            if ($q->fetchColumn() === false) throw new \DomainException('NO_AUTENTICADO',401);
            $q = $pdo->prepare('SELECT id_permiso FROM permiso_login WHERE id_login=:login FOR UPDATE');
            $q->execute(['login'=>$actor]);
            $permisos = array_map('intval',$q->fetchAll(\PDO::FETCH_COLUMN));
            $propio = $usuarioObjetivo === null && $loginObjetivo === null;
            $gestor = (($rolesSesion['admin'] ?? false) && in_array(1,$permisos,true))
                || (($rolesSesion['comite'] ?? false) && in_array(2,$permisos,true));
            if (($propio && (!($rolesSesion['docente'] ?? false) || !in_array(4,$permisos,true)))
                || (!$propio && !$gestor)) throw new \DomainException('NO_AUTORIZADO',403);
            $login = $propio ? $actor : $loginObjetivo;
            $q = $pdo->prepare('SELECT id_login FROM login WHERE id_login=:login FOR UPDATE');
            $q->execute(['login'=>$login]);
            if ($q->fetchColumn() === false) throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            $q = $pdo->prepare('SELECT id_usuario FROM usuario WHERE login=:login FOR UPDATE');
            $q->execute(['login'=>$login]);
            $usuarios = $q->fetchAll(\PDO::FETCH_COLUMN);
            if (count($usuarios) !== 1 || (!$propio && (int)$usuarios[0] !== $usuarioObjetivo)) {
                throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            }
            $usuario = (int)$usuarios[0];
            $q = $pdo->prepare('SELECT id_profesor,estado_profesor FROM profesor WHERE usuario=:usuario FOR UPDATE');
            $q->execute(['usuario'=>$usuario]);
            $profesores = $q->fetchAll(\PDO::FETCH_ASSOC);
            if (count($profesores) !== 1 || !in_array((int)$profesores[0]['estado_profesor'],[1,2,3,4],true)) {
                throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            }
            if ($propio && (int)$profesores[0]['estado_profesor'] === 4) throw new \DomainException('NO_AUTORIZADO',403);

            $personal = $this->editarInfPers($datos['nombres'],$datos['ap_mat'],$datos['ap_pat'],$datos['fech_nac'],
                $datos['documento'],$datos['nro_doc'],$datos['pais_nac'],$datos['genero'],$datos['pais_res'],
                $datos['region'],$datos['comuna'],$datos['telefono'],$datos['direccion'],$datos['cont_em'],
                $datos['tel_em'],$datos['correo'],$datos['pass'],$datos['pueblo'],$usuario,(int)$login);
            if (!($personal instanceof \PDOStatement)) throw new \RuntimeException('PERSONA_NO_CONFIRMADA');
            try {
                $credenciales = $this->editarCredencialesDocente($usuario,(int)$login,$datos['correo'],$datos['pass']);
            } catch (\PDOException $error) {
                // Solo esta escritura de login puede generar el conflicto de correo único.
                if ((string)$error->getCode() === '23000' && (int)($error->errorInfo[1] ?? 0) === 1062) {
                    throw new \DomainException('CORREO_EN_USO',409,$error);
                }
                throw $error;
            }
            if (!$credenciales) throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            if (!$pdo->commit()) throw new \RuntimeException('COMMIT_NO_CONFIRMADO');
        } catch (\Throwable $error) {
            if ($iniciada && $pdo->inTransaction()) $pdo->rollBack();
            throw $error;
        }
    }

    public function editarCredencialesDocente(int $idUsuario, int $idLogin, string $correo, string $pass = ''): bool{
        $conexion = conexion();
        $consultaIdentidad = $conexion->prepare(
            'SELECT l.id_login FROM login l '
            . 'JOIN usuario u ON u.login = l.id_login '
            . 'JOIN profesor p ON p.usuario = u.id_usuario '
            . 'WHERE u.id_usuario = :id_usuario AND l.id_login = :id_login'
        );
        $consultaIdentidad->execute([
            'id_usuario' => $idUsuario,
            'id_login' => $idLogin,
        ]);
        if (count($consultaIdentidad->fetchAll()) !== 1) {
            return false;
        }

        if ($pass === '') {
            $consultaLogin = $conexion->prepare(
                'UPDATE login SET correo = :correo WHERE id_login = :id_login'
            );
            $parametros = [
                'correo' => $correo,
                'id_login' => $idLogin,
            ];
        } else {
            $consultaLogin = $conexion->prepare(
                'UPDATE login SET correo = :correo, pass = :pass WHERE id_login = :id_login'
            );
            $parametros = [
                'correo' => $correo,
                'pass' => $pass,
                'id_login' => $idLogin,
            ];
        }

        return $consultaLogin->execute($parametros);
    }

    public function crearProfesorIntegral(array $datos, int $estadoProfesor): array{
        if (!in_array($estadoProfesor, [self::ESTADO_PENDIENTE, self::ESTADO_ACEPTADO], true)) {
            return ['ok' => false, 'codigo' => 'ESTADO_PROFESOR_INVALIDO'];
        }
        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $idInstitucion = (int) $datos['inst_usuario'];
            if ($idInstitucion > 0) {
                $consultaInstitucion = $conexion->prepare(
                    'SELECT id_inst FROM institucion WHERE id_inst = :id_inst FOR UPDATE'
                );
                $consultaInstitucion->execute(['id_inst' => $idInstitucion]);
                if ($consultaInstitucion->fetch() === false) {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'INSTITUCION_INVALIDA'];
                }
            } else {
                $nuevaInstitucion = trim((string) $datos['nueva_institucion']);
                if ($nuevaInstitucion === '') {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'INSTITUCION_INVALIDA'];
                }
                $insertarInstitucion = $conexion->prepare(
                    'INSERT INTO institucion (inst) VALUES (:institucion)'
                );
                $insertarInstitucion->execute(['institucion' => $nuevaInstitucion]);
                $idInstitucion = (int) $conexion->lastInsertId();
                if ($insertarInstitucion->rowCount() !== 1 || $idInstitucion < 1) {
                    throw new \RuntimeException('No fue posible crear la institución del Profesor.');
                }
            }

            $insertarLogin = $conexion->prepare(
                'INSERT INTO login (correo, pass) VALUES (:correo, :pass)'
            );
            $insertarLogin->execute([
                'correo' => $datos['correo'],
                'pass' => $datos['pass'],
            ]);
            $idLogin = (int) $conexion->lastInsertId();
            if ($insertarLogin->rowCount() !== 1 || $idLogin < 1) {
                throw new \RuntimeException('No fue posible crear el login Docente.');
            }

            $insertarUsuario = $conexion->prepare(
                'INSERT INTO usuario '
                . '(pueblo, pais_res, pais_nac, login, fecha_nac, nombres, ap_mat, ap_pat, tipo_doc, nro_doc, '
                . 'inst_usuario, genero, region, comuna, telefono, direccion, cont_em, tel_em) '
                . 'VALUES (:pueblo, :pais_res, :pais_nac, :login, :fecha_nac, :nombres, :ap_mat, :ap_pat, '
                . ':tipo_doc, :nro_doc, :inst_usuario, :genero, :region, :comuna, :telefono, :direccion, :cont_em, :tel_em)'
            );
            $insertarUsuario->execute([
                'pueblo' => (int) $datos['pueblo'] > 0 ? (int) $datos['pueblo'] : 1,
                'pais_res' => (int) $datos['pais_res'],
                'pais_nac' => (int) $datos['pais_nac'],
                'login' => $idLogin,
                'fecha_nac' => $datos['fecha_nac'],
                'nombres' => $datos['nombres'],
                'ap_mat' => $datos['ap_mat'],
                'ap_pat' => $datos['ap_pat'],
                'tipo_doc' => (int) $datos['tipo_doc'],
                'nro_doc' => $datos['nro_doc'],
                'inst_usuario' => $idInstitucion,
                'genero' => (int) $datos['genero'],
                'region' => $datos['region'],
                'comuna' => $datos['comuna'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion'],
                'cont_em' => $datos['cont_em'],
                'tel_em' => $datos['tel_em'],
            ]);
            $idUsuario = (int) $conexion->lastInsertId();
            if ($insertarUsuario->rowCount() !== 1 || $idUsuario < 1) {
                throw new \RuntimeException('No fue posible crear el usuario Docente.');
            }

            $insertarProfesor = $conexion->prepare(
                'INSERT INTO profesor (usuario, vinculo, cat_academica, anio_ingreso, estado_profesor) '
                . 'VALUES (:usuario, :vinculo, :cat_academica, :anio_ingreso, :estado_profesor)'
            );
            $insertarProfesor->execute([
                'usuario' => $idUsuario,
                'vinculo' => (int) $datos['vinculo'],
                'cat_academica' => (int) $datos['cat_academica'],
                'anio_ingreso' => (int) $datos['anio_ingreso'],
                'estado_profesor' => $estadoProfesor,
            ]);
            if ($insertarProfesor->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible crear el Profesor.');
            }

            $lineas = array_values(array_unique(array_map('intval', $datos['lineas'])));
            if ($lineas === [] || in_array(0, $lineas, true)) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'LINEAS_INVALIDAS'];
            }
            foreach ($lineas as $linea) {
                $consultaLinea = $conexion->prepare(
                    'SELECT id_linea_inv FROM linea_investigacion WHERE id_linea_inv = :linea FOR UPDATE'
                );
                $consultaLinea->execute(['linea' => $linea]);
                if ($consultaLinea->fetch() === false) {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'LINEAS_INVALIDAS'];
                }
                $insertarLinea = $conexion->prepare(
                    'INSERT INTO linea_usuario (usuario, linea_inv) VALUES (:usuario, :linea)'
                );
                $insertarLinea->execute(['usuario' => $idUsuario, 'linea' => $linea]);
                if ($insertarLinea->rowCount() !== 1) {
                    throw new \RuntimeException('No fue posible guardar una línea de investigación.');
                }
            }

            $insertarPermiso = $conexion->prepare(
                'INSERT INTO permiso_login (id_login, id_permiso) VALUES (:id_login, :id_permiso)'
            );
            $insertarPermiso->execute([
                'id_login' => $idLogin,
                'id_permiso' => self::PERMISO_DOCENTE,
            ]);
            if ($insertarPermiso->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible asignar el permiso Docente.');
            }

            $conexion->commit();
            return [
                'ok' => true,
                'codigo' => 'DOCENTE_CREADO',
                'id_login' => $idLogin,
                'id_usuario' => $idUsuario,
            ];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function agregarDocenciaAdministrativa(int $idLogin, array $datos): array{
        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $consultaLogin = $conexion->prepare(
                'SELECT id_login, correo FROM login WHERE id_login = :id_login FOR UPDATE'
            );
            $consultaLogin->execute(['id_login' => $idLogin]);
            $login = $consultaLogin->fetchAll();
            if (count($login) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'LOGIN_INEXISTENTE'];
            }

            $consultaAdmin = $conexion->prepare(
                'SELECT id_admin FROM admin WHERE id_login = :id_login FOR UPDATE'
            );
            $consultaAdmin->execute(['id_login' => $idLogin]);
            $administradores = $consultaAdmin->fetchAll(\PDO::FETCH_COLUMN);
            if (count($administradores) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'ORIGEN_ADMIN_INVALIDO'];
            }

            $consultaPermisos = $conexion->prepare(
                'SELECT id_permiso FROM permiso_login '
                . 'WHERE id_login = :id_login ORDER BY id_permiso, id_per_log FOR UPDATE'
            );
            $consultaPermisos->execute(['id_login' => $idLogin]);
            $permisos = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
            if (
                count($permisos) !== 1
                || !in_array($permisos[0], [self::PERMISO_ADMIN, self::PERMISO_COMITE], true)
            ) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'PERMISOS_ORIGEN_INVALIDOS'];
            }

            $consultaUsuario = $conexion->prepare(
                'SELECT id_usuario FROM usuario WHERE login = :id_login FOR UPDATE'
            );
            $consultaUsuario->execute(['id_login' => $idLogin]);
            if ($consultaUsuario->fetch() !== false) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'DOCENCIA_YA_EXISTENTE'];
            }

            $idInstitucion = (int) $datos['inst_usuario'];
            if ($idInstitucion > 0) {
                $consultaInstitucion = $conexion->prepare(
                    'SELECT id_inst FROM institucion WHERE id_inst = :id_inst FOR UPDATE'
                );
                $consultaInstitucion->execute(['id_inst' => $idInstitucion]);
                if ($consultaInstitucion->fetch() === false) {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'INSTITUCION_INVALIDA'];
                }
            } else {
                $nuevaInstitucion = trim((string) $datos['nueva_institucion']);
                if ($nuevaInstitucion === '') {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'INSTITUCION_INVALIDA'];
                }
                $insertarInstitucion = $conexion->prepare(
                    'INSERT INTO institucion (inst) VALUES (:institucion)'
                );
                $insertarInstitucion->execute(['institucion' => $nuevaInstitucion]);
                $idInstitucion = (int) $conexion->lastInsertId();
                if ($insertarInstitucion->rowCount() !== 1 || $idInstitucion < 1) {
                    throw new \RuntimeException('No fue posible crear la institución durante la transición.');
                }
            }

            $insertarUsuario = $conexion->prepare(
                'INSERT INTO usuario '
                . '(pueblo, pais_res, pais_nac, login, fecha_nac, nombres, ap_mat, ap_pat, tipo_doc, nro_doc, '
                . 'inst_usuario, genero, region, comuna, telefono, direccion, cont_em, tel_em) '
                . 'VALUES (:pueblo, :pais_res, :pais_nac, :login, :fecha_nac, :nombres, :ap_mat, :ap_pat, '
                . ':tipo_doc, :nro_doc, :inst_usuario, :genero, :region, :comuna, :telefono, :direccion, :cont_em, :tel_em)'
            );
            $insertarUsuario->execute([
                'pueblo' => (int) $datos['pueblo'] > 0 ? (int) $datos['pueblo'] : 1,
                'pais_res' => (int) $datos['pais_res'],
                'pais_nac' => (int) $datos['pais_nac'],
                'login' => $idLogin,
                'fecha_nac' => $datos['fecha_nac'],
                'nombres' => $datos['nombres'],
                'ap_mat' => $datos['ap_mat'],
                'ap_pat' => $datos['ap_pat'],
                'tipo_doc' => (int) $datos['tipo_doc'],
                'nro_doc' => $datos['nro_doc'],
                'inst_usuario' => $idInstitucion,
                'genero' => (int) $datos['genero'],
                'region' => $datos['region'],
                'comuna' => $datos['comuna'],
                'telefono' => $datos['telefono'],
                'direccion' => $datos['direccion'],
                'cont_em' => $datos['cont_em'],
                'tel_em' => $datos['tel_em'],
            ]);
            $idUsuario = (int) $conexion->lastInsertId();
            if ($insertarUsuario->rowCount() !== 1 || $idUsuario < 1) {
                throw new \RuntimeException('No fue posible crear el usuario Docente.');
            }

            $insertarProfesor = $conexion->prepare(
                'INSERT INTO profesor (usuario, vinculo, cat_academica, anio_ingreso, estado_profesor) '
                . 'VALUES (:usuario, :vinculo, :cat_academica, :anio_ingreso, :estado_profesor)'
            );
            $insertarProfesor->execute([
                'usuario' => $idUsuario,
                'vinculo' => (int) $datos['vinculo'],
                'cat_academica' => (int) $datos['cat_academica'],
                'anio_ingreso' => (int) $datos['anio_ingreso'],
                'estado_profesor' => self::ESTADO_ACEPTADO,
            ]);
            if ($insertarProfesor->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible crear el Profesor.');
            }

            $lineas = array_values(array_unique(array_map('intval', $datos['lineas'])));
            if ($lineas === [] || in_array(0, $lineas, true)) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'LINEAS_INVALIDAS'];
            }
            foreach ($lineas as $linea) {
                $consultaLinea = $conexion->prepare(
                    'SELECT id_linea_inv FROM linea_investigacion WHERE id_linea_inv = :linea FOR UPDATE'
                );
                $consultaLinea->execute(['linea' => $linea]);
                if ($consultaLinea->fetch() === false) {
                    $conexion->rollBack();
                    return ['ok' => false, 'codigo' => 'LINEAS_INVALIDAS'];
                }
                $insertarLinea = $conexion->prepare(
                    'INSERT INTO linea_usuario (usuario, linea_inv) VALUES (:usuario, :linea)'
                );
                $insertarLinea->execute(['usuario' => $idUsuario, 'linea' => $linea]);
                if ($insertarLinea->rowCount() !== 1) {
                    throw new \RuntimeException('No fue posible guardar una línea de investigación.');
                }
            }

            $insertarPermiso = $conexion->prepare(
                'INSERT INTO permiso_login (id_login, id_permiso) VALUES (:id_login, :id_permiso)'
            );
            $insertarPermiso->execute([
                'id_login' => $idLogin,
                'id_permiso' => self::PERMISO_DOCENTE,
            ]);
            if ($insertarPermiso->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible agregar el permiso Docente.');
            }

            $eliminarAdmin = $conexion->prepare('DELETE FROM admin WHERE id_login = :id_login');
            $eliminarAdmin->execute(['id_login' => $idLogin]);
            if ($eliminarAdmin->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible completar la transición de representación.');
            }

            $conexion->commit();
            return [
                'ok' => true,
                'codigo' => 'DOCENCIA_AGREGADA',
                'id_login' => $idLogin,
                'id_usuario' => $idUsuario,
                'correo' => $login[0]['correo'],
                'permiso_administrativo' => $permisos[0],
            ];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    /** Programa y estado comparten la transacción abierta por el endpoint. */
    public function guardarProgramaDocente(int $usuario, array $input): void {
        $pdo=conexion();
        if (!$pdo->inTransaction()) throw new \RuntimeException('TRANSACCION_REQUERIDA');
        $values=[];
        foreach (['instTrab','vinculo','catAcad','anio_ing'] as $field) {
            $raw=$input[$field]??null;
            if ((!is_int($raw)&&!is_string($raw)) || !preg_match('/^[1-9]\d*$/D',(string)$raw)
                || filter_var($raw,FILTER_VALIDATE_INT)===false) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
            $values[$field]=(int)$raw;
        }
        if (!in_array($values['vinculo'],[1,2,3],true) || !in_array($values['catAcad'],[1,2,3,4,5],true)) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
        $lines=$input['lineaInv']??null;
        if (!is_array($lines) || !$lines || count($lines)>100) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
        foreach ($lines as &$line) {
            if ((!is_int($line)&&!is_string($line)) || !preg_match('/^[1-9]\d*$/D',(string)$line)
                || filter_var($line,FILTER_VALIDATE_INT)===false) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
            $line=(int)$line;
        } unset($line);
        $lines=array_values(array_unique($lines));
        $q=$pdo->prepare('SELECT id_inst FROM institucion WHERE id_inst=:inst FOR UPDATE');
        $q->execute(['inst'=>$values['instTrab']]);
        if ($q->fetchColumn()===false) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
        $q=$pdo->prepare('SELECT id_linea_inv FROM linea_investigacion WHERE id_linea_inv IN ('.implode(',',array_fill(0,count($lines),'?')).') FOR UPDATE');
        $q->execute($lines);
        if (count($q->fetchAll(\PDO::FETCH_COLUMN))!==count($lines)) throw new \DomainException('DATOS_PROGRAMA_INVALIDOS',422);
        $q=$pdo->prepare('UPDATE profesor SET vinculo=:vinculo,cat_academica=:categoria,anio_ingreso=:anio WHERE usuario=:usuario');
        $q->execute(['vinculo'=>$values['vinculo'],'categoria'=>$values['catAcad'],'anio'=>$values['anio_ing'],'usuario'=>$usuario]);
        $q=$pdo->prepare('UPDATE usuario SET inst_usuario=:inst WHERE id_usuario=:usuario');
        $q->execute(['inst'=>$values['instTrab'],'usuario'=>$usuario]);
        $q=$pdo->prepare('SELECT linea_inv FROM linea_usuario WHERE usuario=:usuario');
        $q->execute(['usuario'=>$usuario]); $current=array_map('intval',$q->fetchAll(\PDO::FETCH_COLUMN));
        foreach (array_diff($lines,$current) as $line) {
            $q=$pdo->prepare('INSERT INTO linea_usuario (usuario,linea_inv) VALUES (:usuario,:linea)');
            $q->execute(['usuario'=>$usuario,'linea'=>$line]);
        }
        foreach (array_diff($current,$lines) as $line) {
            $q=$pdo->prepare('DELETE FROM linea_usuario WHERE usuario=:usuario AND linea_inv=:linea');
            $q->execute(['usuario'=>$usuario,'linea'=>$line]);
        }
    }

    public function cambiarEstadoProfesor(int $idLogin, int $estadoObjetivo): array {
        if (!in_array($estadoObjetivo,[1,2,3,4],true)) return ['ok'=>false,'codigo'=>'ESTADO_OBJETIVO_INVALIDO'];
        $pdo=conexion(); $own=!$pdo->inTransaction();
        if ($own) $pdo->beginTransaction();
        try {
            $q=$pdo->prepare('SELECT id_usuario FROM usuario WHERE login=:login FOR UPDATE');
            $q->execute(['login'=>$idLogin]); $users=$q->fetchAll(\PDO::FETCH_COLUMN);
            if (count($users)!==1) throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            $q=$pdo->prepare('SELECT id_profesor,estado_profesor FROM profesor WHERE usuario=:usuario FOR UPDATE');
            $q->execute(['usuario'=>$users[0]]); $profiles=$q->fetchAll(\PDO::FETCH_ASSOC);
            if (count($profiles)!==1 || !in_array((int)$profiles[0]['estado_profesor'],[1,2,3,4],true)) {
                throw new \DomainException('IDENTIDAD_DOCENTE_INCOMPATIBLE',409);
            }
            $current=(int)$profiles[0]['estado_profesor'];
            if ($estadoObjetivo===4 || $current===4) \App\Security\AcademicSession::requireMigration($pdo);
            $q=$pdo->prepare('SELECT id_permiso FROM permiso_login WHERE id_login=:login FOR UPDATE');
            $q->execute(['login'=>$idLogin]); $permissions=array_map('intval',$q->fetchAll(\PDO::FETCH_COLUMN));
            if (!in_array(4,$permissions,true) || array_diff($permissions,[1,2,3,4,5])) {
                throw new \DomainException('PERMISOS_INCOMPATIBLES',409);
            }
            // Inactivar conserva roles. Su habilitación académica requiere Aceptado al reactivar.
            if ($estadoObjetivo!==4 && $estadoObjetivo!==2 && array_intersect($permissions,[1,2])) {
                throw new \DomainException('DOCENTE_ADMIN_DEBE_ESTAR_ACEPTADO',409);
            }
            $q=$pdo->prepare('UPDATE profesor SET estado_profesor=:estado WHERE id_profesor=:profesor');
            if (!$q->execute(['estado'=>$estadoObjetivo,'profesor'=>$profiles[0]['id_profesor']])) throw new \RuntimeException('ESTADO_NO_CONFIRMADO');
            if ($own) $pdo->commit();
            return ['ok'=>true,'codigo'=>'ESTADO_PROFESOR_ACTUALIZADO','estado_profesor'=>$estadoObjetivo];
        } catch (\DomainException $error) {
            if ($own && $pdo->inTransaction()) $pdo->rollBack();
            return ['ok'=>false,'codigo'=>$error->getMessage()];
        } catch (\Throwable $error) {
            if ($own && $pdo->inTransaction()) $pdo->rollBack();
            throw $error;
        }
    }

    public function cambiarRolAdministrativo(int $idLogin, string $rolObjetivo): array{
        $roles = [
            'none' => null,
            'admin' => self::PERMISO_ADMIN,
            'comite' => self::PERMISO_COMITE,
        ];
        if (!array_key_exists($rolObjetivo, $roles)) {
            return ['ok' => false, 'codigo' => 'ROL_OBJETIVO_INVALIDO'];
        }

        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $consultaIdentidad = $conexion->prepare(
                'SELECT u.id_usuario, p.id_profesor, p.estado_profesor FROM login l '
                . 'JOIN usuario u ON u.login = l.id_login '
                . 'JOIN profesor p ON p.usuario = u.id_usuario '
                . 'WHERE l.id_login = :id_login FOR UPDATE'
            );
            $consultaIdentidad->execute(['id_login' => $idLogin]);
            $identidades = $consultaIdentidad->fetchAll(\PDO::FETCH_ASSOC);
            if (
                count($identidades) !== 1
                || (int) $identidades[0]['estado_profesor'] !== self::ESTADO_ACEPTADO
            ) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE'];
            }

            $consultaPermisos = $conexion->prepare(
                'SELECT id_permiso FROM permiso_login '
                . 'WHERE id_login = :id_login ORDER BY id_permiso, id_per_log FOR UPDATE'
            );
            $consultaPermisos->execute(['id_login' => $idLogin]);
            $permisos = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
            $conteo = array_count_values($permisos);
            if (
                ($conteo[self::PERMISO_DOCENTE] ?? 0) !== 1
                || ($conteo[self::PERMISO_ADMIN] ?? 0) > 1
                || ($conteo[self::PERMISO_COMITE] ?? 0) > 1
                || (($conteo[self::PERMISO_ADMIN] ?? 0) === 1 && ($conteo[self::PERMISO_COMITE] ?? 0) === 1)
                || array_diff($permisos, [self::PERMISO_ADMIN, self::PERMISO_COMITE, self::PERMISO_DOCENTE]) !== []
            ) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'PERMISOS_INCOMPATIBLES'];
            }

            $eliminarRol = $conexion->prepare(
                'DELETE FROM permiso_login '
                . 'WHERE id_login = :id_login AND id_permiso IN (:admin, :comite)'
            );
            $eliminarRol->execute([
                'id_login' => $idLogin,
                'admin' => self::PERMISO_ADMIN,
                'comite' => self::PERMISO_COMITE,
            ]);

            $permisoObjetivo = $roles[$rolObjetivo];
            if ($permisoObjetivo !== null) {
                $insertarRol = $conexion->prepare(
                    'INSERT INTO permiso_login (id_login, id_permiso) VALUES (:id_login, :id_permiso)'
                );
                $insertarRol->execute([
                    'id_login' => $idLogin,
                    'id_permiso' => $permisoObjetivo,
                ]);
                if ($insertarRol->rowCount() !== 1) {
                    throw new \RuntimeException('No fue posible asignar el rol administrativo.');
                }
            }

            $consultaPermisos->execute(['id_login' => $idLogin]);
            $estadoFinal = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
            sort($estadoFinal);
            $esperado = $permisoObjetivo === null
                ? [self::PERMISO_DOCENTE]
                : [$permisoObjetivo, self::PERMISO_DOCENTE];
            sort($esperado);
            if ($estadoFinal !== $esperado) {
                throw new \RuntimeException('La transición de rol produjo un estado inválido.');
            }

            $conexion->commit();
            return ['ok' => true, 'codigo' => 'ROL_ADMINISTRATIVO_ACTUALIZADO', 'rol' => $rolObjetivo];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function eliminarProfesorInequivoco(int $idLogin): array {
        // Compatibilidad de operación: ahora solo actualiza el estado, nunca borra identidades.
        return $this->cambiarEstadoProfesor($idLogin,4);
    }
}
