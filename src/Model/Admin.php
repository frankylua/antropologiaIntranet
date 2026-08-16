<?php
declare(strict_types=1);
namespace App\Model;

class Admin extends Login {
    private const PERMISO_ADMIN = 1;
    private const PERMISO_COMITE = 2;
    private const PERMISO_DOCENTE = 4;

    public function __construct(){
    }

    public function crear(string $correo, string $pass, string $nombre, int $permiso): int{
        if (!in_array($permiso, [self::PERMISO_ADMIN, self::PERMISO_COMITE], true)) {
            throw new \InvalidArgumentException('Permiso administrativo inválido.');
        }

        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $consultaCorreo = $conexion->prepare('SELECT id_login FROM login WHERE correo = :correo FOR UPDATE');
            $consultaCorreo->execute(['correo' => $correo]);
            if ($consultaCorreo->fetch() !== false) {
                $conexion->rollBack();
                return 0;
            }

            $consultaLogin = $conexion->prepare('INSERT INTO login (correo, pass) VALUES (:correo, :pass)');
            $consultaLogin->execute([
                'correo' => $correo,
                'pass' => $pass,
            ]);
            $idLogin = (int) $conexion->lastInsertId();
            if ($idLogin <= 0) {
                throw new \RuntimeException('No fue posible crear el login administrativo.');
            }

            $consultaPermiso = $conexion->prepare(
                'INSERT INTO permiso_login (id_login, id_permiso) VALUES (:id_login, :id_permiso)'
            );
            $consultaPermiso->execute([
                'id_login' => $idLogin,
                'id_permiso' => $permiso,
            ]);
            if ($consultaPermiso->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible asignar el permiso administrativo.');
            }

            $consultaAdmin = $conexion->prepare(
                'INSERT INTO admin (id_login, nombre) VALUES (:id_login, :nombre)'
            );
            $consultaAdmin->execute([
                'id_login' => $idLogin,
                'nombre' => $nombre,
            ]);
            if ($consultaAdmin->rowCount() !== 1) {
                throw new \RuntimeException('No fue posible crear la identidad administrativa.');
            }

            $conexion->commit();
            return $idLogin;
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function editar(int $idLogin, string $nombre, string $correo, string $pass = ''): string{
        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $cuenta = $this->clasificarCuenta($conexion, $idLogin, true);
            if ($cuenta['codigo'] !== 'CUENTA_VALIDA') {
                $conexion->rollBack();
                return $cuenta['codigo'];
            }
            if ($cuenta['representacion'] !== 'sin_docencia') {
                $conexion->rollBack();
                return 'PERFIL_DOCENTE';
            }

            if ($pass === '') {
                $consultaLogin = $conexion->prepare(
                    'UPDATE login SET correo = :correo WHERE id_login = :id_login'
                );
                $parametrosLogin = [
                    'correo' => $correo,
                    'id_login' => $idLogin,
                ];
            } else {
                $consultaLogin = $conexion->prepare(
                    'UPDATE login SET correo = :correo, pass = :pass WHERE id_login = :id_login'
                );
                $parametrosLogin = [
                    'correo' => $correo,
                    'pass' => $pass,
                    'id_login' => $idLogin,
                ];
            }
            $consultaLogin->execute($parametrosLogin);

            $consultaAdmin = $conexion->prepare(
                'UPDATE admin SET nombre = :nombre WHERE id_login = :id_login'
            );
            $consultaAdmin->execute([
                'nombre' => $nombre,
                'id_login' => $idLogin,
            ]);

            $conexion->commit();
            return 'ACTUALIZADO';
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    public function mostrarPorId(int $idLogin){
        $conexion = conexion();
        $cuenta = $this->clasificarCuenta($conexion, $idLogin);
        return $cuenta['codigo'] === 'CUENTA_VALIDA' ? $cuenta : false;
    }

    public function mostrar(){
        $conexion = conexion();
        $sql = 'SELECT DISTINCT id_login FROM permiso_login '
            . 'WHERE id_permiso IN (:permiso_admin, :permiso_comite)';
        $consulta = $conexion->prepare($sql);
        $consulta->execute([
            'permiso_admin' => self::PERMISO_ADMIN,
            'permiso_comite' => self::PERMISO_COMITE,
        ]);

        $administradores = [];
        foreach ($consulta->fetchAll(\PDO::FETCH_COLUMN) as $idLogin) {
            $cuenta = $this->clasificarCuenta($conexion, (int) $idLogin);
            if ($cuenta['codigo'] === 'CUENTA_VALIDA') {
                $administradores[] = $cuenta;
            } else {
                error_log('[ADMIN_LISTADO] ' . $cuenta['codigo'] . ' id_login=' . (int) $idLogin);
            }
        }

        usort($administradores, static function (array $primero, array $segundo): int {
            return strnatcasecmp((string) $primero['nombre'], (string) $segundo['nombre']);
        });

        return $administradores;
    }

    public function eliminarAdministrativo(int $idLogin, int $actorLogin): array{
        $conexion = conexion();
        $conexion->beginTransaction();

        try {
            $cuenta = $this->clasificarCuenta($conexion, $idLogin, true);
            if ($cuenta['codigo'] !== 'CUENTA_VALIDA') {
                $conexion->rollBack();
                return ['ok' => false, 'codigo' => $cuenta['codigo']];
            }

            if ($idLogin === $actorLogin) {
                $conexion->rollBack();
                return [
                    'ok' => false,
                    'codigo' => $cuenta['representacion'] === 'con_docencia'
                        ? 'AUTORRETIRO_PROHIBIDO'
                        : 'AUTOELIMINACION_PROHIBIDA',
                ];
            }

            if ($cuenta['representacion'] === 'con_docencia') {
                $eliminarPermiso = $conexion->prepare(
                    'DELETE FROM permiso_login WHERE id_login = :id_login AND id_permiso = :id_permiso'
                );
                $eliminarPermiso->execute([
                    'id_login' => $idLogin,
                    'id_permiso' => $cuenta['id_permiso'],
                ]);
                if ($eliminarPermiso->rowCount() !== 1) {
                    throw new \RuntimeException('El retiro del permiso administrativo quedó incompleto.');
                }
                $conexion->commit();
                return [
                    'ok' => true,
                    'codigo' => 'ROL_RETIRADO',
                    'accion' => 'retiro',
                    'rol' => $cuenta['rol'],
                ];
            }

            $eliminarAdmin = $conexion->prepare('DELETE FROM admin WHERE id_login = :id_login');
            $eliminarAdmin->execute(['id_login' => $idLogin]);
            $eliminarPermiso = $conexion->prepare(
                'DELETE FROM permiso_login WHERE id_login = :id_login AND id_permiso = :id_permiso'
            );
            $eliminarPermiso->execute([
                'id_login' => $idLogin,
                'id_permiso' => $cuenta['id_permiso'],
            ]);
            $eliminarLogin = $conexion->prepare('DELETE FROM login WHERE id_login = :id_login');
            $eliminarLogin->execute(['id_login' => $idLogin]);

            if ($eliminarAdmin->rowCount() !== 1 || $eliminarPermiso->rowCount() !== 1 || $eliminarLogin->rowCount() !== 1) {
                throw new \RuntimeException('La eliminación administrativa quedó incompleta.');
            }
            $conexion->commit();
            return [
                'ok' => true,
                'codigo' => 'CUENTA_ELIMINADA',
                'accion' => 'eliminacion',
                'rol' => $cuenta['rol'],
            ];
        } catch (\Throwable $error) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $error;
        }
    }

    private function clasificarCuenta(\PDO $conexion, int $idLogin, bool $bloquear = false): array{
        $clausulaBloqueo = $bloquear ? ' FOR UPDATE' : '';

        $consultaLogin = $conexion->prepare(
            'SELECT id_login, correo FROM login WHERE id_login = :id_login' . $clausulaBloqueo
        );
        $consultaLogin->execute(['id_login' => $idLogin]);
        $login = $consultaLogin->fetchAll();
        if (count($login) !== 1) {
            return ['codigo' => 'LOGIN_INEXISTENTE'];
        }

        $consultaAdmin = $conexion->prepare(
            'SELECT id_admin, nombre FROM admin WHERE id_login = :id_login' . $clausulaBloqueo
        );
        $consultaAdmin->execute(['id_login' => $idLogin]);
        $administradores = $consultaAdmin->fetchAll();

        $consultaPermisos = $conexion->prepare(
            'SELECT id_permiso FROM permiso_login '
            . 'WHERE id_login = :id_login ORDER BY id_permiso, id_per_log' . $clausulaBloqueo
        );
        $consultaPermisos->execute(['id_login' => $idLogin]);
        $permisos = array_map('intval', $consultaPermisos->fetchAll(\PDO::FETCH_COLUMN));
        if ($permisos === [] || count($permisos) !== count(array_unique($permisos))) {
            return ['codigo' => 'PERMISOS_INCOMPATIBLES'];
        }
        $rolesAdministrativos = array_values(array_filter(
            $permisos,
            static fn (int $permiso): bool => in_array($permiso, [self::PERMISO_ADMIN, self::PERMISO_COMITE], true)
        ));
        if (count($rolesAdministrativos) !== 1) {
            return ['codigo' => 'ROL_ADMINISTRATIVO_INCOMPATIBLE'];
        }
        $permisoAdministrativo = $rolesAdministrativos[0];

        $consultaUsuario = $conexion->prepare(
            'SELECT id_usuario, nombres, ap_pat, ap_mat FROM usuario WHERE login = :id_login' . $clausulaBloqueo
        );
        $consultaUsuario->execute(['id_login' => $idLogin]);
        $usuarios = $consultaUsuario->fetchAll();
        if (count($usuarios) > 1) {
            return ['codigo' => 'USUARIOS_INCOMPATIBLES'];
        }

        $profesores = [];
        if (count($usuarios) === 1) {
            $consultaProfesor = $conexion->prepare(
                'SELECT id_profesor FROM profesor WHERE usuario = :id_usuario' . $clausulaBloqueo
            );
            $consultaProfesor->execute(['id_usuario' => (int) $usuarios[0]['id_usuario']]);
            $profesores = $consultaProfesor->fetchAll();
        }

        $tieneDocencia = in_array(self::PERMISO_DOCENTE, $permisos, true);
        if (!$tieneDocencia) {
            if (count($administradores) !== 1 || $usuarios !== [] || $permisos !== [$permisoAdministrativo]) {
                return ['codigo' => 'REPRESENTACION_INCOMPATIBLE'];
            }
            return [
                'codigo' => 'CUENTA_VALIDA',
                'id_login' => $idLogin,
                'id_admin' => (int) $administradores[0]['id_admin'],
                'id_usuario' => null,
                'nombre' => $administradores[0]['nombre'],
                'correo' => $login[0]['correo'],
                'id_permiso' => $permisoAdministrativo,
                'rol' => $permisoAdministrativo === self::PERMISO_ADMIN ? 'Admin' : 'Comité',
                'representacion' => 'sin_docencia',
                'perfil' => 'administrativo',
                'editable' => true,
            ];
        }

        if (count($administradores) !== 0 || count($usuarios) !== 1 || count($profesores) !== 1) {
            return ['codigo' => 'REPRESENTACION_INCOMPATIBLE'];
        }

        return [
            'codigo' => 'CUENTA_VALIDA',
            'id_login' => $idLogin,
            'id_admin' => null,
            'id_usuario' => (int) $usuarios[0]['id_usuario'],
            'nombre' => trim($usuarios[0]['nombres'] . ' ' . $usuarios[0]['ap_pat'] . ' ' . $usuarios[0]['ap_mat']),
            'correo' => $login[0]['correo'],
            'id_permiso' => $permisoAdministrativo,
            'rol' => $permisoAdministrativo === self::PERMISO_ADMIN ? 'Admin' : 'Comité',
            'representacion' => 'con_docencia',
            'perfil' => 'docente',
            'editable' => false,
        ];
    }
}
?>
