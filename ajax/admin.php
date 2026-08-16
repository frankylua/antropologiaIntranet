<?php
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Admin;
use App\Security\Authorization;

require 'validaciones.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
}

function responderAdmin(int $estado, $respuesta): void
{
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        exit;
}

function exigirGestionAdmin(): void
{
        if (!Authorization::hasAny(['admin'])) {
                responderAdmin(403, [
                        'ok' => false,
                        'error' => 'NO_AUTORIZADO',
                        'mensaje' => 'No tiene autorización para gestionar cuentas administrativas.',
                ]);
        }
}

function exigirPerfilAdministrativoPropio(): void
{
        if (!Authorization::hasAny(['admin', 'comite'])) {
                responderAdmin(403, [
                        'ok' => false,
                        'error' => 'NO_AUTORIZADO',
                        'mensaje' => 'No tiene autorización para consultar este perfil.',
                ]);
        }
}

function loginAutenticado(): int
{
        return isset($_SESSION['login']) && is_scalar($_SESSION['login'])
                ? (int) $_SESSION['login']
                : 0;
}

$nombre = isset($_POST['nombre']) && is_string($_POST['nombre']) ? trim(limpiar_datos($_POST['nombre'])) : '';
$correo = isset($_POST['correo']) && is_string($_POST['correo']) ? trim(limpiar_datos($_POST['correo'])) : '';
$pass = isset($_POST['pass']) && is_string($_POST['pass']) ? limpiar_datos($_POST['pass']) : '';
$permiso = isset($_POST['permiso']) && is_scalar($_POST['permiso']) ? (int) $_POST['permiso'] : 0;
$idLogin = isset($_POST['id_login']) && is_scalar($_POST['id_login']) ? (int) $_POST['id_login'] : 0;
$op = isset($_POST['op']) && is_string($_POST['op']) ? $_POST['op'] : '';
$admin = new Admin();

try {
        switch ($op) {
                case 'insert-update':
                        exigirGestionAdmin();
                        if ($nombre === '' || $correo === '' || filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
                                responderAdmin(400, [
                                        'ok' => false,
                                        'error' => 'DATOS_INVALIDOS',
                                        'mensaje' => 'Nombre y correo electrónico válido son obligatorios.',
                                ]);
                        }

                        if ($idLogin === 0) {
                                if (!in_array($permiso, [1, 2], true)) {
                                        responderAdmin(400, [
                                                'ok' => false,
                                                'error' => 'PERMISO_INVALIDO',
                                                'mensaje' => 'El tipo de cuenta debe ser Admin o Comité.',
                                        ]);
                                }
                                if ($pass === '') {
                                        responderAdmin(400, [
                                                'ok' => false,
                                                'error' => 'PASSWORD_REQUERIDA',
                                                'mensaje' => 'La contraseña es obligatoria para crear la cuenta.',
                                        ]);
                                }

                                $nuevoLogin = $admin->crear($correo, $pass, $nombre, $permiso);
                                if ($nuevoLogin === 0) {
                                        responderAdmin(409, [
                                                'ok' => false,
                                                'error' => 'CORREO_EXISTENTE',
                                                'mensaje' => 'El correo electrónico ya se encuentra registrado.',
                                        ]);
                                }
                                responderAdmin(200, [
                                        'ok' => true,
                                        'mensaje' => 'Cuenta administrativa guardada correctamente.',
                                ]);
                        }

                        $resultadoEdicion = $admin->editar($idLogin, $nombre, $correo, $pass);
                        if ($resultadoEdicion !== 'ACTUALIZADO') {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => $resultadoEdicion,
                                        'mensaje' => $resultadoEdicion === 'PERFIL_DOCENTE'
                                                ? 'Los datos personales deben editarse desde el perfil Docente.'
                                                : 'La cuenta seleccionada no posee una representación administrativa editable.',
                                ]);
                        }
                        responderAdmin(200, [
                                'ok' => true,
                                'mensaje' => 'Cuenta administrativa editada correctamente.',
                        ]);

                case 'read':
                        exigirGestionAdmin();
                        $cuentas = $admin->mostrar();
                        $actorLogin = loginAutenticado();
                        foreach ($cuentas as &$cuenta) {
                                $cuenta['es_actor'] = $actorLogin > 0 && (int) $cuenta['id_login'] === $actorLogin;
                        }
                        unset($cuenta);
                        responderAdmin(200, $cuentas);

                case 'query_id':
                        exigirGestionAdmin();
                        if ($idLogin < 1) {
                                responderAdmin(400, [
                                        'ok' => false,
                                        'error' => 'IDENTIFICADOR_INVALIDO',
                                        'mensaje' => 'El identificador indicado no es válido.',
                                ]);
                        }
                        $respuesta = $admin->mostrarPorId($idLogin);
                        if ($respuesta === false) {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => 'REPRESENTACION_INCOMPATIBLE',
                                        'mensaje' => 'La cuenta seleccionada no posee una representación administrativa válida.',
                                ]);
                        }
                        responderAdmin(200, $respuesta);

                case 'docencia-context':
                        exigirGestionAdmin();
                        if ($idLogin < 1) {
                                responderAdmin(400, [
                                        'ok' => false,
                                        'error' => 'IDENTIFICADOR_INVALIDO',
                                        'mensaje' => 'El identificador indicado no es válido.',
                                ]);
                        }
                        $cuentaDocencia = $admin->mostrarPorId($idLogin);
                        if ($cuentaDocencia === false || $cuentaDocencia['representacion'] !== 'sin_docencia') {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => 'ORIGEN_DOCENCIA_INVALIDO',
                                        'mensaje' => 'Sólo una cuenta administrativa sin Docencia puede iniciar esta transición.',
                                ]);
                        }
                        responderAdmin(200, [
                                'ok' => true,
                                'id_login' => $cuentaDocencia['id_login'],
                                'correo' => $cuentaDocencia['correo'],
                        ]);

                case 'read-perfil':
                        exigirPerfilAdministrativoPropio();
                        $actorLogin = loginAutenticado();
                        if ($actorLogin < 1) {
                                responderAdmin(403, [
                                        'ok' => false,
                                        'error' => 'NO_AUTORIZADO',
                                        'mensaje' => 'No fue posible validar la identidad autenticada.',
                                ]);
                        }
                        $perfil = $admin->mostrarPorId($actorLogin);
                        if ($perfil === false) {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => 'REPRESENTACION_INCOMPATIBLE',
                                        'mensaje' => 'No fue posible resolver el perfil institucional.',
                                ]);
                        }
                        responderAdmin(200, $perfil);

                case 'update-perfil':
                        exigirPerfilAdministrativoPropio();
                        $actorLogin = loginAutenticado();
                        if ($actorLogin < 1) {
                                responderAdmin(403, [
                                        'ok' => false,
                                        'error' => 'NO_AUTORIZADO',
                                        'mensaje' => 'No fue posible validar la identidad autenticada.',
                                ]);
                        }
                        if ($nombre === '' || $correo === '' || filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
                                responderAdmin(400, [
                                        'ok' => false,
                                        'error' => 'DATOS_INVALIDOS',
                                        'mensaje' => 'Nombre y correo electrónico válido son obligatorios.',
                                ]);
                        }
                        $resultadoPerfil = $admin->editar($actorLogin, $nombre, $correo, $pass);
                        if ($resultadoPerfil !== 'ACTUALIZADO') {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => $resultadoPerfil,
                                        'mensaje' => $resultadoPerfil === 'PERFIL_DOCENTE'
                                                ? 'Los datos personales deben editarse desde Mi Perfil Docente.'
                                                : 'No fue posible editar el perfil administrativo.',
                                ]);
                        }
                        responderAdmin(200, [
                                'ok' => true,
                                'mensaje' => 'Perfil administrativo editado correctamente.',
                        ]);

                case 'delete':
                        exigirGestionAdmin();
                        if ($idLogin < 1) {
                                responderAdmin(400, [
                                        'ok' => false,
                                        'error' => 'IDENTIFICADOR_INVALIDO',
                                        'mensaje' => 'El identificador indicado no es válido.',
                                ]);
                        }
                        $actorLogin = loginAutenticado();
                        if ($actorLogin < 1) {
                                responderAdmin(403, [
                                        'ok' => false,
                                        'error' => 'NO_AUTORIZADO',
                                        'mensaje' => 'No fue posible validar la identidad autenticada.',
                                ]);
                        }

                        $resultado = $admin->eliminarAdministrativo($idLogin, $actorLogin);
                        if (!$resultado['ok']) {
                                responderAdmin(409, [
                                        'ok' => false,
                                        'error' => $resultado['codigo'],
                                        'mensaje' => $resultado['codigo'] === 'AUTORRETIRO_PROHIBIDO'
                                                ? 'No puede retirar su propio rol administrativo.'
                                                : ($resultado['codigo'] === 'AUTOELIMINACION_PROHIBIDA'
                                                        ? 'No puede eliminar su propia cuenta administrativa.'
                                                        : 'La cuenta seleccionada no puede modificarse desde esta operación.'),
                                ]);
                        }
                        responderAdmin(200, [
                                'ok' => true,
                                'accion' => $resultado['accion'],
                                'mensaje' => $resultado['accion'] === 'retiro'
                                        ? 'Rol ' . $resultado['rol'] . ' retirado; el perfil Docente permanece íntegro.'
                                        : 'Cuenta ' . $resultado['rol'] . ' eliminada correctamente.',
                        ]);

                default:
                        responderAdmin(400, [
                                'ok' => false,
                                'error' => 'OPERACION_INVALIDA',
                                'mensaje' => 'La operación solicitada no es válida.',
                        ]);
        }
} catch (\PDOException $error) {
        error_log('[ADMIN] ' . $error->getMessage());
        if ($error->getCode() === '23000') {
                responderAdmin(409, [
                        'ok' => false,
                        'error' => 'CONFLICTO_DATOS',
                        'mensaje' => 'Los datos indicados entran en conflicto con una cuenta existente.',
                ]);
        }
        responderAdmin(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible completar la operación administrativa.',
        ]);
} catch (\Throwable $error) {
        error_log('[ADMIN] ' . $error->getMessage());
        responderAdmin(500, [
                'ok' => false,
                'error' => 'ERROR_TECNICO',
                'mensaje' => 'No fue posible completar la operación administrativa.',
        ]);
}

?>