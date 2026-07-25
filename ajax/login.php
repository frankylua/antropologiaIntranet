<?php
declare(strict_types=1);

use App\Identity\Cardinality;
use App\Identity\IdentityResolver;
use App\Model\Login;

if (strlen(session_id()) < 1) {
    session_start();
}

require_once __DIR__ . '/../src/bootstrap/app.php';

/**
 * @param array<mixed> $payload
 */
function respondLoginJson(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * @param list<array<string, mixed>> $permissions
 */
function observeIdentity(int $loginId, array $permissions): void
{
    try {
        $identityResolver = new IdentityResolver(conexion());
        $identityResolution = $identityResolver->resolveByLoginId($loginId);
        $legacyStudentSignal = false;
        $legacyProfessorSignal = false;

        foreach ($permissions as $permission) {
            $permissionId = isset($permission['id_permiso']) ? (int) $permission['id_permiso'] : 0;
            $legacyStudentSignal = $legacyStudentSignal || $permissionId === 5;
            $legacyProfessorSignal = $legacyProfessorSignal || $permissionId === 4;
        }

        $identityEventCodes = [
            'LOGIN_NO_ENCONTRADO' => 'IDENTITY_LOGIN_NO_ENCONTRADO',
            'LOGIN_CON_MULTIPLES_USUARIOS' => 'IDENTITY_LOGIN_MULTIPLE_USERS',
            'USUARIO_CON_MULTIPLES_ESTUDIANTES' => 'IDENTITY_USER_MULTIPLE_STUDENTS',
            'USUARIO_CON_MULTIPLES_PROFESORES' => 'IDENTITY_USER_MULTIPLE_PROFESSORS',
            'TIPO_EST_INVALIDO' => 'IDENTITY_INVALID_STUDENT_STATUS',
        ];

        foreach ($identityResolution->inconsistencies() as $inconsistency) {
            if (isset($identityEventCodes[$inconsistency])) {
                error_log($identityEventCodes[$inconsistency]);
            }
        }

        if ($identityResolution->studentCardinality() === Cardinality::SINGLE && !$legacyStudentSignal) {
            error_log('IDENTITY_STUDENT_WITHOUT_LEGACY_ROLE');
        } elseif (
            $identityResolution->userCardinality() === Cardinality::SINGLE
            && $identityResolution->studentCardinality() === Cardinality::NONE
            && $legacyStudentSignal
        ) {
            error_log('IDENTITY_LEGACY_STUDENT_WITHOUT_RELATION');
        }

        if ($identityResolution->professorCardinality() === Cardinality::SINGLE && !$legacyProfessorSignal) {
            error_log('IDENTITY_PROFESSOR_WITHOUT_LEGACY_ROLE');
        } elseif (
            $identityResolution->userCardinality() === Cardinality::SINGLE
            && $identityResolution->professorCardinality() === Cardinality::NONE
            && $legacyProfessorSignal
        ) {
            error_log('IDENTITY_LEGACY_PROFESSOR_WITHOUT_RELATION');
        }
    } catch (\Throwable $exception) {
        error_log('IDENTITY_RESOLUTION_FAILED');
    }
}

/**
 * @param array<string, mixed> $authContext
 * @param list<array<string, mixed>> $permissions
 */
function isValidAuthContext(
    array $authContext,
    array $permissions,
    int $loginId,
    bool $shouldGrantRegulations
): bool {
    $roleByPermission = [
        1 => 'admin',
        2 => 'comite',
        3 => 'aceptado',
        4 => 'docente',
        5 => 'estudiante',
    ];
    $allowedKeys = [
        'login',
        'admin',
        'comite',
        'aceptado',
        'docente',
        'estudiante',
        'id_usuario',
        'capacidades',
    ];

    if (
        $loginId <= 0
        || ($authContext['login'] ?? null) !== $loginId
        || !isset($authContext['capacidades'])
        || !is_array($authContext['capacidades'])
        || array_diff(array_keys($authContext), $allowedKeys) !== []
    ) {
        return false;
    }

    $permissionIds = [];
    foreach ($permissions as $permission) {
        if (
            !isset($permission['id_login'], $permission['id_permiso'])
            || !is_scalar($permission['id_login'])
            || !is_scalar($permission['id_permiso'])
            || (int) $permission['id_login'] !== $loginId
        ) {
            return false;
        }

        $permissionIds[] = (int) $permission['id_permiso'];
    }
    $permissionIds = array_values(array_unique($permissionIds));

    foreach ($roleByPermission as $permissionId => $role) {
        $roleExpected = in_array($permissionId, $permissionIds, true);
        if ($roleExpected !== array_key_exists($role, $authContext)) {
            return false;
        }
        if ($roleExpected && $authContext[$role] !== $loginId) {
            return false;
        }
    }

    $capabilities = $authContext['capacidades'];
    if (
        count($capabilities) !== count(array_unique($capabilities))
        || array_values($capabilities) !== $capabilities
    ) {
        return false;
    }
    foreach ($capabilities as $capability) {
        if (!is_string($capability) || !in_array($capability, ['perfil.ver', 'reglamento.ver'], true)) {
            return false;
        }
    }

    if (
        in_array('perfil.ver', $capabilities, true) !== in_array(5, $permissionIds, true)
        || in_array('reglamento.ver', $capabilities, true) !== $shouldGrantRegulations
    ) {
        return false;
    }

    $requiresUserId = isset($authContext['docente']) || isset($authContext['estudiante']);
    if ($requiresUserId !== array_key_exists('id_usuario', $authContext)) {
        return false;
    }
    if ($requiresUserId) {
        $userRows = $authContext['id_usuario'];
        if (
            !is_array($userRows)
            || count($userRows) !== 1
            || !isset($userRows[0]['id_usuario'])
            || !is_scalar($userRows[0]['id_usuario'])
            || (int) $userRows[0]['id_usuario'] <= 0
        ) {
            return false;
        }
    }

    return true;
}

$email = isset($_POST['correo']) && is_string($_POST['correo']) ? $_POST['correo'] : '';
$password = isset($_POST['pass']) && is_string($_POST['pass']) ? $_POST['pass'] : '';

if ($email === '' || $password === '') {
    respondLoginJson(401, ['error' => 'AUTHENTICATION_FAILED']);
}

try {
    $connection = conexion();
    $credentialsStatement = $connection->prepare(
        'SELECT id_login FROM login WHERE correo = :correo AND pass = :pass'
    );
    $credentialsStatement->execute([
        'correo' => $email,
        'pass' => $password,
    ]);
    $loginIds = array_map('intval', $credentialsStatement->fetchAll(\PDO::FETCH_COLUMN));

    if ($loginIds === []) {
        respondLoginJson(401, ['error' => 'AUTHENTICATION_FAILED']);
    }
    if (count($loginIds) !== 1 || $loginIds[0] <= 0) {
        throw new \UnexpectedValueException('AUTH_CONTEXT_LOGIN_AMBIGUOUS');
    }

    $loginId = $loginIds[0];
    $permissionsStatement = $connection->prepare(
        'SELECT id_login, id_permiso FROM permiso_login WHERE id_login = :login_id ORDER BY id_permiso'
    );
    $permissionsStatement->execute(['login_id' => $loginId]);
    $permissions = $permissionsStatement->fetchAll(\PDO::FETCH_ASSOC);

    if ($permissions === []) {
        error_log('AUTH_CONTEXT_PERMISSIONS_EMPTY');
        respondLoginJson(403, ['error' => 'AUTH_CONTEXT_PERMISSIONS_EMPTY']);
    }

    $login = new Login();
    $authContext = [
        'login' => $loginId,
        'capacidades' => [],
    ];
    $capabilities = [];
    $roleByPermission = [
        1 => 'admin',
        2 => 'comite',
        3 => 'aceptado',
        4 => 'docente',
        5 => 'estudiante',
    ];

    foreach ($permissions as $permission) {
        if (
            !isset($permission['id_login'], $permission['id_permiso'])
            || !is_scalar($permission['id_login'])
            || !is_scalar($permission['id_permiso'])
            || (int) $permission['id_login'] !== $loginId
        ) {
            throw new \UnexpectedValueException('AUTH_CONTEXT_PERMISSION_MISMATCH');
        }

        $permissionId = (int) $permission['id_permiso'];
        if (isset($roleByPermission[$permissionId])) {
            $authContext[$roleByPermission[$permissionId]] = $loginId;
        }
        if ($permissionId === 5) {
            $capabilities['perfil.ver'] = true;
        }
    }

    observeIdentity($loginId, $permissions);

    $academicStates = $login->obtenerEstadosAcademicosPorLogin($loginId);
    if (!is_array($academicStates)) {
        throw new \UnexpectedValueException('AUTH_CONTEXT_STATES_INVALID');
    }

    $shouldGrantRegulations = false;
    if (count($academicStates) === 1) {
        $studentStatus = isset($academicStates[0]['tipo_est']) ? (int) $academicStates[0]['tipo_est'] : 0;
        $shouldGrantRegulations = in_array($studentStatus, [1, 2, 3, 4, 5, 7], true);
        if ($shouldGrantRegulations) {
            $capabilities['reglamento.ver'] = true;
        }
    } elseif (count($academicStates) > 1) {
        error_log('AUTHORIZATION_STATE_AMBIGUOUS');
    }

    $authContext['capacidades'] = array_values(array_keys($capabilities));

    if (isset($authContext['docente']) || isset($authContext['estudiante'])) {
        $userRows = $login->retornarIdUsu($loginId);
        if (
            !is_array($userRows)
            || count($userRows) !== 1
            || !isset($userRows[0]['id_usuario'])
            || !is_scalar($userRows[0]['id_usuario'])
            || (int) $userRows[0]['id_usuario'] <= 0
        ) {
            throw new \UnexpectedValueException('AUTH_CONTEXT_USER_ID_INVALID');
        }
        $authContext['id_usuario'] = $userRows;
    }

    if (!isValidAuthContext($authContext, $permissions, $loginId, $shouldGrantRegulations)) {
        throw new \UnexpectedValueException('AUTH_CONTEXT_VALIDATION_FAILED');
    }

    $managedKeys = [
        'login',
        'admin',
        'comite',
        'aceptado',
        'docente',
        'estudiante',
        'id_usuario',
        'capacidades',
    ];
    $sessionWithoutAuthContext = array_diff_key($_SESSION, array_fill_keys($managedKeys, true));
    $sessionAfterAuthentication = $sessionWithoutAuthContext + $authContext;

    if (!@session_regenerate_id(true)) {
        error_log('AUTH_CONTEXT_SESSION_REGENERATION_FAILED');
        respondLoginJson(500, ['error' => 'AUTH_CONTEXT_TECHNICAL_FAILURE']);
    }

    $_SESSION = $sessionAfterAuthentication;
    respondLoginJson(200, $permissions);
} catch (\Throwable $exception) {
    $technicalCode = $exception instanceof \UnexpectedValueException
        && str_starts_with($exception->getMessage(), 'AUTH_CONTEXT_')
            ? $exception->getMessage()
            : 'AUTH_CONTEXT_BUILD_FAILED';
    error_log($technicalCode);
    respondLoginJson(500, ['error' => 'AUTH_CONTEXT_TECHNICAL_FAILURE']);
}
