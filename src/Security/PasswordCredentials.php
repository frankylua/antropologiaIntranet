<?php
declare(strict_types=1);

namespace App\Security;

use PDO;

/** Único límite entre contraseñas recibidas y credenciales persistidas. */
final class PasswordCredentials
{
    private const OPTIONS = ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 1];

    public static function isValid(string $password): bool
    {
        return trim($password) !== '' && preg_match('/\A[^\x00]{1,200}\z/u', $password) === 1;
    }

    public static function hash(string $password): string
    {
        // No normalizar: mayúsculas, espacios, barras y Unicode forman parte del secreto.
        if (!self::isValid($password)) {
            throw new \InvalidArgumentException('PASSWORD_INVALIDA');
        }
        if (!defined('PASSWORD_ARGON2ID')) {
            throw new \RuntimeException('PASSWORD_ALGORITHM_UNAVAILABLE');
        }
        return password_hash($password, PASSWORD_ARGON2ID, self::OPTIONS);
    }

    public static function find(PDO $pdo, string $email, string $password): ?int
    {
        if (!self::isValid($password)) return null;
        $q = $pdo->prepare('SELECT id_login, pass FROM login WHERE correo = :correo');
        $q->execute(['correo' => $email]);
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) !== 1 || (int)$rows[0]['id_login'] <= 0) return null;
        $id = (int)$rows[0]['id_login'];
        return self::matches($pdo, $id, (string)$rows[0]['pass'], $password) ? $id : null;
    }

    /** Identidad del presupuesto: respeta la collation real sin comprobar el secreto. */
    public static function accountIdentity(PDO $pdo, string $email): string
    {
        $q = $pdo->prepare('SELECT id_login FROM login WHERE correo = :correo');
        $q->execute(['correo'=>$email]);
        $ids = $q->fetchAll(PDO::FETCH_COLUMN);
        return count($ids) === 1 && (int)$ids[0] > 0
            ? 'login:' . (int)$ids[0]
            : 'input:' . mb_strtolower(trim($email), 'UTF-8');
    }

    private static function matches(PDO $pdo, int $id, string $stored, string $password): bool
    {
        if ($password === '') return false;
        $info = password_get_info($stored);
        if ($info['algo'] !== null) {
            // Evitar la aceptación del sufijo truncado por bcrypt en credenciales importadas.
            if ($info['algoName'] === 'bcrypt' && strlen($password) > 72) return false;
            return password_verify($password, $stored);
        }
        // Un hash reconocido pero dañado jamás se interpreta como contraseña heredada.
        if (preg_match('/\A\$(?:2[abxy]|argon2(?:id|i|d))\$/', $stored)) return false;
        // Mantener exclusivamente para texto heredado la comparación/collation previa.
        $q = $pdo->prepare('SELECT id_login FROM login WHERE id_login = :id_login AND pass = :pass');
        $q->execute(['id_login' => $id, 'pass' => $password]);
        return $q->fetchColumn() !== false;
    }

    /** Revalidar bajo bloqueo impide sobrescribir un cambio de contraseña concurrente. */
    public static function finalize(PDO $pdo, int $id, string $password): bool
    {
        if (!$pdo->beginTransaction()) throw new \RuntimeException('PASSWORD_TRANSACTION_FAILED');
        try {
            $q = $pdo->prepare('SELECT pass FROM login WHERE id_login = :id_login FOR UPDATE');
            $q->execute(['id_login' => $id]);
            $stored = $q->fetchColumn();
            if (!is_string($stored) || !self::matches($pdo, $id, $stored, $password)) {
                $pdo->rollBack();
                return false;
            }
            if (!defined('PASSWORD_ARGON2ID')) throw new \RuntimeException('PASSWORD_ALGORITHM_UNAVAILABLE');
            if (password_needs_rehash($stored, PASSWORD_ARGON2ID, self::OPTIONS)) {
                $q = $pdo->prepare('UPDATE login SET pass = :pass WHERE id_login = :id_login');
                $q->execute(['pass' => self::hash($password), 'id_login' => $id]);
            }
            if (!$pdo->commit()) throw new \RuntimeException('PASSWORD_COMMIT_FAILED');
            return true;
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $error;
        }
    }
}
