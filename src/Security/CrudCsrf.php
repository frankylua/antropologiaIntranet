<?php
declare(strict_types=1);

namespace App\Security;

/** Protege escrituras autenticadas; no sustituye autorización ni tokens de autoalta. */
final class CrudCsrf
{
    private const WRITES = [
        'admin' => ['insert-update', 'update-perfil', 'delete'],
        'docente' => ['insert-update', 'agregar-docencia', 'update-inf-pers', 'update-inf-pers-perfil',
            'update-inf-prog', 'update-inf-prog-perfil', 'update-estado-profesor', 'update-rol-admin', 'delete'],
        'estudiante' => ['insert-update', 'update-inf-pers', 'update-inf-prog', 'update-permiso-tipo-est', 'delete'],
    ];

    private static function authenticatedContext(): bool
    {
        foreach (['login', 'admin', 'comite', 'docente', 'estudiante', 'aceptado'] as $key) {
            if (isset($_SESSION[$key])) return true;
        }
        return false;
    }

    private static function login(): int
    {
        $id = $_SESSION['login'] ?? null;
        if (!is_int($id) && !is_string($id)) return 0;
        return (int)filter_var($id, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    }

    public static function token(): string
    {
        $login = self::login();
        if ($login < 1 || session_status() !== PHP_SESSION_ACTIVE) return '';
        $stored = $_SESSION['csrf_crud'] ?? null;
        if (!is_array($stored) || ($stored['login'] ?? null) !== $login
            || !self::validFormat($stored['token'] ?? null)) {
            $_SESSION['csrf_crud'] = ['login'=>$login, 'token'=>bin2hex(random_bytes(32))];
        }
        return $_SESSION['csrf_crud']['token'];
    }

    private static function validFormat(mixed $value): bool
    {
        return is_string($value) && preg_match('/\A[a-f0-9]{64}\z/D', $value) === 1;
    }

    public static function enforce(string $resource): void
    {
        $operation = $_POST['op'] ?? null;
        if (!in_array($operation, self::WRITES[$resource] ?? [], true)) return;
        // Sin actor autenticado, los guards vigentes resuelven rechazo o autoalta con token propio.
        if (!self::authenticatedContext()) return;
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            self::reject(405, 'SOLICITUD_INVALIDA', 'La operación requiere una solicitud POST.');
        }
        $stored = $_SESSION['csrf_crud'] ?? null;
        $received = $_SERVER['HTTP_X_CRUD_CSRF_TOKEN'] ?? null;
        if (session_status() !== PHP_SESSION_ACTIVE || self::login() < 1
            || !is_array($stored) || ($stored['login'] ?? null) !== self::login()
            || !self::validFormat($stored['token'] ?? null) || !self::validFormat($received)
            || !hash_equals($stored['token'], $received)) {
            self::reject(403, 'CSRF_INVALIDO', 'La solicitud de seguridad no es válida. Recargue la página y vuelva a intentar; no se guardaron cambios.');
        }
    }

    private static function reject(int $status, string $code, string $message): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok'=>false, 'error'=>$code, 'mensaje'=>$message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
