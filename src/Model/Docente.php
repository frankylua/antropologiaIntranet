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
        if (!in_array($estadoProfesor, [0, 1, 2, 3], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login WHERE u.nombres LIKE :busqueda';
        $parametros = ['busqueda' => $busqueda . '%'];
        if ($excluirLogin > 0) {
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
        if (!in_array($estadoProfesor, [0, 1, 2, 3], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login';
        $parametros = [];
        $condiciones = [];
        if ($excluirLogin > 0) {
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
        $sql="SELECT u.id_usuario, u.pais_res, u.pais_nac, u.login, u.fecha_nac, u.nombres, u.ap_mat, u.ap_pat, u.tipo_doc, u.nro_doc, u.inst_usuario, u.genero, u.region, u.comuna, u.telefono, u.direccion, u.cont_em, u.tel_em, l.id_login, l.correo, p.id_profesor, p.usuario, p.vinculo, p.cat_academica, p.anio_ingreso, p.estado_profesor, i.id_inst, i.inst, pu.id_pueblo, pu.pueblo, lu.id_linea_usuario, lu.linea_inv, li.id_linea_inv, li.linea, pr.pais as p_resi, pn.pais as p_nac FROM usuario u JOIN login l ON u.login=l.id_login JOIN profesor p ON u.id_usuario=p.usuario JOIN institucion i ON u.inst_usuario=i.id_inst JOIN pais pn ON u.pais_nac=pn.id_pais JOIN pais pr ON u.pais_res=pr.id_pais JOIN pueblo pu ON u.pueblo=pu.id_pueblo JOIN linea_usuario lu ON u.id_usuario=lu.usuario JOIN linea_investigacion li ON li.id_linea_inv=lu.linea_inv WHERE u.id_usuario= :id_usuario order by u.nombres";
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
        $sql="SELECT u.inst_usuario,u.id_usuario,p.id_profesor,p.vinculo,p.cat_academica,p.anio_ingreso,p.estado_profesor,l.linea_inv,lo.id_login FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario JOIN linea_usuario l ON u.id_usuario=l.usuario JOIN login lo ON u.login=lo.id_login WHERE u.id_usuario=:id_usuario";
        $consulta = conexion()->prepare($sql);
        $consulta->execute(['id_usuario' => (int) $id_usu]);
        return $consulta->fetchAll();
    }
    public function mostrarProfTipo($tipo, int $excluirLogin = 0, int $estadoProfesor = 0){
        if (!in_array($estadoProfesor, [0, 1, 2, 3], true)) {
            return [];
        }
        $sql = 'SELECT u.id_usuario,u.nombres,u.ap_pat,u.ap_mat,p.vinculo,p.estado_profesor,l.id_login '
            . 'FROM usuario u JOIN profesor p ON u.id_usuario=p.usuario '
            . 'JOIN login l ON u.login=l.id_login WHERE p.vinculo=:vinculo';
        $parametros = ['vinculo' => (int) $tipo];
        if ($excluirLogin > 0) {
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

    public function cambiarEstadoProfesor(int $idLogin, int $estadoObjetivo): array{
        if (!in_array($estadoObjetivo, [self::ESTADO_ACEPTADO, self::ESTADO_RECHAZADO], true)) {
            return ['ok' => false, 'codigo' => 'ESTADO_OBJETIVO_INVALIDO'];
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
            if (count($identidades) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'IDENTIDAD_DOCENTE_INCOMPATIBLE'];
            }

            $estadoActual = (int) $identidades[0]['estado_profesor'];
            if (!in_array($estadoActual, [self::ESTADO_PENDIENTE, self::ESTADO_ACEPTADO, self::ESTADO_RECHAZADO], true)) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'ESTADO_PROFESOR_INVALIDO'];
            }

            $consultaPermisos = $conexion->prepare(
                'SELECT id_permiso FROM permiso_login '
                . 'WHERE id_login = :id_login ORDER BY id_permiso, id_per_log FOR UPDATE'
            );
            $consultaPermisos->execute(['id_login' => $idLogin]);
            $permisos = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
            $representacionesValidas = [
                [self::PERMISO_DOCENTE],
                [self::PERMISO_ADMIN, self::PERMISO_DOCENTE],
                [self::PERMISO_COMITE, self::PERMISO_DOCENTE],
            ];
            if (
                !in_array($permisos, $representacionesValidas, true)
                || ($estadoActual !== self::ESTADO_ACEPTADO && $permisos !== [self::PERMISO_DOCENTE])
            ) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'PERMISOS_INCOMPATIBLES'];
            }

            $transicionValida =
                ($estadoActual === self::ESTADO_PENDIENTE
                    && in_array($estadoObjetivo, [self::ESTADO_ACEPTADO, self::ESTADO_RECHAZADO], true))
                || ($estadoActual === self::ESTADO_RECHAZADO && $estadoObjetivo === self::ESTADO_ACEPTADO);
            if (!$transicionValida) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'TRANSICION_ESTADO_INVALIDA'];
            }

            $actualizarEstado = $conexion->prepare(
                'UPDATE profesor SET estado_profesor = :estado_objetivo '
                . 'WHERE id_profesor = :id_profesor AND estado_profesor = :estado_actual'
            );
            $actualizarEstado->execute([
                'estado_objetivo' => $estadoObjetivo,
                'id_profesor' => (int) $identidades[0]['id_profesor'],
                'estado_actual' => $estadoActual,
            ]);
            if ($actualizarEstado->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible actualizar el estado del Profesor.');
            }

            $verificarEstado = $conexion->prepare(
                'SELECT estado_profesor FROM profesor WHERE id_profesor = :id_profesor'
            );
            $verificarEstado->execute(['id_profesor' => (int) $identidades[0]['id_profesor']]);
            $estadoFinal = array_map('intval', $verificarEstado->fetchAll(\PDO::FETCH_COLUMN));
            if ($estadoFinal !== [$estadoObjetivo]) {
                throw new \RuntimeException('La transicion de estado no pudo verificarse.');
            }

            $conexion->commit();
            return [
                'ok' => true,
                'codigo' => 'ESTADO_PROFESOR_ACTUALIZADO',
                'estado_profesor' => $estadoObjetivo,
            ];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
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

    public function eliminarProfesorInequivoco(int $idLogin): array{
        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $consultaLogin = $conexion->prepare(
                'SELECT id_login FROM login WHERE id_login = :id_login FOR UPDATE'
            );
            $consultaLogin->execute(['id_login' => $idLogin]);
            if (count($consultaLogin->fetchAll()) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'LOGIN_INEXISTENTE'];
            }

            $consultaUsuario = $conexion->prepare(
                'SELECT id_usuario FROM usuario WHERE login = :id_login FOR UPDATE'
            );
            $consultaUsuario->execute(['id_login' => $idLogin]);
            $usuarios = $consultaUsuario->fetchAll(\PDO::FETCH_COLUMN);
            if (count($usuarios) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'IDENTIDAD_INCOMPATIBLE'];
            }
            $idUsuario = (int) $usuarios[0];

            $consultaProfesor = $conexion->prepare(
                'SELECT id_profesor FROM profesor WHERE usuario = :id_usuario FOR UPDATE'
            );
            $consultaProfesor->execute(['id_usuario' => $idUsuario]);
            if (count($consultaProfesor->fetchAll()) !== 1) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'OBJETIVO_NO_PROFESOR'];
            }

            $consultaAdmin = $conexion->prepare(
                'SELECT id_admin FROM admin WHERE id_login = :id_login FOR UPDATE'
            );
            $consultaAdmin->execute(['id_login' => $idLogin]);
            if ($consultaAdmin->fetch() !== false) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'REPRESENTACION_INCOMPATIBLE'];
            }

            $consultaEstudiante = $conexion->prepare(
                'SELECT id_est FROM estudiante WHERE usuario = :id_usuario FOR UPDATE'
            );
            $consultaEstudiante->execute(['id_usuario' => $idUsuario]);
            if ($consultaEstudiante->fetch() !== false) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'IDENTIDAD_ESTUDIANTE'];
            }

            $consultaPermisos = $conexion->prepare(
                'SELECT id_permiso FROM permiso_login '
                . 'WHERE id_login = :id_login ORDER BY id_per_log FOR UPDATE'
            );
            $consultaPermisos->execute(['id_login' => $idLogin]);
            $permisos = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
            if (
                in_array(self::PERMISO_ADMIN, $permisos, true)
                || in_array(self::PERMISO_COMITE, $permisos, true)
            ) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'DOCENTE_CON_ROL_ADMINISTRATIVO'];
            }
            if ($permisos !== [self::PERMISO_DOCENTE]) {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => 'PERMISOS_INCOMPATIBLES'];
            }

            $eliminacion = $this->eliminar($idLogin);
            if (!$eliminacion instanceof \PDOStatement || $eliminacion->rowCount() !== 1) {
                throw new \RuntimeException('El DELETE docente no eliminó exactamente un login.');
            }

            $conexion->commit();
            return ['ok' => true, 'codigo' => 'ELIMINADO'];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

}