<?php
declare(strict_types=1);

namespace App\Security;

use App\Config\Configuration;
use Closure;
use PDO;

/** Presupuesto de admisión local, atómico entre procesos, independiente de cookies. */
final class LoginAttemptLimiter
{
    private PDO $store;
    private string $secret;
    private Closure $clock;
    private const MAX_BUCKETS = 10000;

    public static function fromConfiguration(Configuration $config): self
    {
        $directory = $config->get('LOGIN_RATE_LIMIT_DIR');
        if ($directory === '') {
            $identity = dirname(__DIR__, 2) . '|' . $config->get('DB_HOST') . '|' . $config->get('DB_NAME');
            $directory = sys_get_temp_dir() . '/antropologia-login-' . substr(hash('sha256', $identity), 0, 24);
        }
        return new self($directory, (int)$config->get('LOGIN_RATE_ACCOUNT_MAX'),
            (int)$config->get('LOGIN_RATE_IP_MAX'), (int)$config->get('LOGIN_RATE_WINDOW_SECONDS'),
            (int)$config->get('LOGIN_RATE_BLOCK_SECONDS'));
    }

    public function __construct(string $directory, private int $accountMax = 5, private int $originMax = 60,
        private int $window = 900, private int $pause = 60, ?Closure $clock = null)
    {
        $this->clock = $clock ?? static fn(): int => time();
        try {
            if ($accountMax < 1 || $accountMax > 10000 || $originMax < 1 || $originMax > 10000
                || $window < 1 || $window > 86400 || $pause < 1 || $pause > 3600) {
                throw new \RuntimeException('Invalid limits');
            }
            $directory = $this->privateDirectory($directory);
            $file = $directory . '/attempts.sqlite';
            if (is_link($file) || is_link($file . '-journal')) throw new \RuntimeException('Invalid store');
            $mask = umask(0077);
            try {
                $this->store = new PDO('sqlite:' . $file, null, null,
                    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
            } finally { umask($mask); }
            $this->store->exec('PRAGMA busy_timeout=1500');
            $this->store->exec('PRAGMA secure_delete=ON');
            $this->store->exec('CREATE TABLE IF NOT EXISTS metadata (name TEXT PRIMARY KEY, value TEXT NOT NULL)');
            $this->store->exec('CREATE TABLE IF NOT EXISTS buckets (bucket TEXT PRIMARY KEY, attempts INTEGER NOT NULL CHECK(attempts>=0),'
                . 'window_start INTEGER NOT NULL, blocked_until INTEGER NOT NULL, expires_at INTEGER NOT NULL)');
            $this->secret = $this->transaction(function (): string {
                $q = $this->store->query("SELECT value FROM metadata WHERE name='hmac'");
                $value = $q->fetchColumn();
                if ($value === false) {
                    $value = bin2hex(random_bytes(32));
                    $q = $this->store->prepare('INSERT INTO metadata(name,value) VALUES(:name,:value)');
                    $q->execute(['name'=>'hmac','value'=>$value]);
                }
                if (!is_string($value) || !preg_match('/\A[a-f0-9]{64}\z/D', $value)) throw new \RuntimeException('Invalid metadata');
                return $value;
            });
        } catch (\Throwable $error) {
            throw new \RuntimeException('LOGIN_LIMITER_UNAVAILABLE', 0, $error);
        }
    }

    /** @return array{key:string,retry_after:int} */
    public function reserveOrigin(string $address): array
    {
        $packed = @inet_pton($address);
        if ($packed === false) throw new \RuntimeException('LOGIN_LIMITER_UNAVAILABLE');
        if (strlen($packed) === 16 && substr($packed, 0, 12) === str_repeat("\0", 10) . "\xff\xff") {
            $packed = substr($packed, 12); // Una IPv4 mapeada comparte presupuesto con su forma IPv4.
        }
        return $this->reserve('origin:' . bin2hex($packed), $this->originMax);
    }

    /** @return array{key:string,retry_after:int} */
    public function reserveAccount(string $identity): array
    {
        return $this->reserve('account:' . $identity, $this->accountMax);
    }

    /** @param array{key:string,retry_after:int} $ticket */
    public function succeeded(array $ticket): void
    {
        $this->transaction(function () use ($ticket): void {
            $q = $this->store->prepare('DELETE FROM buckets WHERE bucket=:bucket');
            $q->execute(['bucket'=>$ticket['key']]);
        });
    }

    private function reserve(string $identity, int $maximum): array
    {
        $key = hash_hmac('sha256', $identity, $this->secret);
        return $this->transaction(function () use ($key, $maximum): array {
            $now = ($this->clock)();
            $q = $this->store->prepare('DELETE FROM buckets WHERE expires_at<=:now');
            $q->execute(['now'=>$now]);
            $q = $this->store->prepare('SELECT attempts,window_start,blocked_until FROM buckets WHERE bucket=:bucket');
            $q->execute(['bucket'=>$key]);
            $row = $q->fetch();
            if ($row !== false && (int)$row['blocked_until'] > $now) {
                return ['key'=>$key,'retry_after'=>(int)$row['blocked_until'] - $now];
            }
            if ($row === false) {
                if ((int)$this->store->query('SELECT COUNT(*) FROM buckets')->fetchColumn() >= self::MAX_BUCKETS) {
                    throw new \RuntimeException('Store capacity exceeded');
                }
            }
            if ($row === false || (int)$row['blocked_until'] > 0 || $now >= (int)$row['window_start'] + $this->window) {
                $row = ['attempts'=>0, 'window_start'=>$now, 'blocked_until'=>0];
            }
            $attempts = (int)$row['attempts'] + 1;
            $until = $attempts >= $maximum ? $now + $this->pause : 0;
            $q = $this->store->prepare('INSERT INTO buckets(bucket,attempts,window_start,blocked_until,expires_at) '
                . 'VALUES(:bucket,:attempts,:start,:blocked,:expires) ON CONFLICT(bucket) DO UPDATE SET '
                . 'attempts=excluded.attempts,window_start=excluded.window_start,blocked_until=excluded.blocked_until,expires_at=excluded.expires_at');
            $q->execute(['bucket'=>$key,'attempts'=>$attempts,'start'=>(int)$row['window_start'],
                'blocked'=>$until,'expires'=>max((int)$row['window_start']+$this->window, $until)]);
            return ['key'=>$key,'retry_after'=>0];
        });
    }

    private function transaction(Closure $action): mixed
    {
        $started = false;
        try {
            $this->store->exec('BEGIN IMMEDIATE');
            $started = true;
            $result = $action();
            if ($this->store->exec('COMMIT') === false) throw new \RuntimeException('Commit failed');
            return $result;
        } catch (\Throwable $error) {
            if ($started) {
                try { $this->store->exec('ROLLBACK'); } catch (\Throwable) { /* Mantener rechazo seguro. */ }
            }
            throw new \RuntimeException('LOGIN_LIMITER_UNAVAILABLE', 0, $error);
        }
    }

    private function privateDirectory(string $directory): string
    {
        $directory = rtrim(str_replace('\\', '/', $directory), '/');
        if (str_contains($directory, "\0") || !preg_match('~^(?:[A-Za-z]:/|/)~', $directory)
            || str_starts_with($directory, '//') || preg_match('~(?:^|/)\.{1,2}(?:/|$)~', $directory)) {
            throw new \RuntimeException('Private directory required');
        }
        $cursor = $directory; $tail = [];
        while (!is_dir($cursor)) {
            if (file_exists($cursor)) throw new \RuntimeException('Invalid directory');
            $tail[] = basename($cursor); $parent = dirname($cursor);
            if ($parent === $cursor) throw new \RuntimeException('Invalid directory');
            $cursor = $parent;
        }
        $resolved = rtrim(str_replace('\\', '/', (string)realpath($cursor)), '/');
        if ($tail !== []) $resolved .= '/' . implode('/', array_reverse($tail));
        foreach ([dirname(__DIR__, 2), $_SERVER['DOCUMENT_ROOT'] ?? ''] as $publicRoot) {
            if (!is_string($publicRoot) || $publicRoot === '' || realpath($publicRoot) === false) continue;
            $root = rtrim(str_replace('\\', '/', (string)realpath($publicRoot)), '/');
            $target = $resolved;
            if (PHP_OS_FAMILY === 'Windows') { $root = strtolower($root); $target = strtolower($target); }
            if ($target === $root || str_starts_with($target, $root . '/')) throw new \RuntimeException('Public store rejected');
        }
        if (!is_dir($resolved) && !@mkdir($resolved, 0700, true) && !is_dir($resolved)) throw new \RuntimeException('Store unavailable');
        if (!is_writable($resolved)) throw new \RuntimeException('Store unavailable');
        return $resolved;
    }
}
