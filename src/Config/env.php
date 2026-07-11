<?php
declare(strict_types=1);

if (!function_exists("env")) {
    function env(string $key, mixed $default = null): mixed {
        static $loaded = false;
        if (!$loaded) {
            $envFile = __DIR__ . "/../../.env";
            if (is_readable($envFile)) {
                foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === "" || str_starts_with($line, "#")) { continue; }
                    if (!str_contains($line, "=")) { continue; }
                    [$k, $v] = explode("=", $line, 2);
                    $k = trim($k);
                    $v = trim($v);
                    if (strlen($v) >= 2 && ($v[0] === "\"" || $v[0] === "'")) {
                        $v = substr($v, 1, -1);
                    }
                    $_ENV[$k] = $v;
                }
            }
            $loaded = true;
        }
        return $_ENV[$key] ?? $default;
    }
}