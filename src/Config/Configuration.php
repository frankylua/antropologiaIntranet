<?php
declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class Configuration
{
    /** @var array<string, string> */
    private array $values;

    /** @param array<string, string> $values */
    private function __construct(array $values)
    {
        $this->values = $values;
    }

    public static function resolve(string $environmentFile): self
    {
        $fileValues = self::readEnvironmentFile($environmentFile);
        $defaults = [
            'APP_NAME' => 'Doctorado Antropología',
            'DB_ENCODE' => 'utf8mb4',
        ];
        $required = [
            'APP_NAME', 'APP_ENV', 'APP_URL', 'DB_HOST', 'DB_NAME',
            'DB_USERNAME', 'DB_PASS', 'DB_ENCODE',
        ];

        $values = [];
        foreach ($required as $key) {
            [$isExternal, $externalValue] = self::externalValue($key);
            if ($isExternal) {
                $values[$key] = $externalValue;
            } elseif (array_key_exists($key, $fileValues)) {
                $values[$key] = $fileValues[$key];
            } elseif (array_key_exists($key, $defaults)) {
                $values[$key] = $defaults[$key];
            } else {
                throw new RuntimeException("Falta la variable de configuración obligatoria: {$key}.");
            }
        }

        self::validate($values);

        return new self($values);
    }

    public function get(string $key): string
    {
        if (!array_key_exists($key, $this->values)) {
            throw new RuntimeException("Variable de configuración desconocida: {$key}.");
        }

        return $this->values[$key];
    }

    /** @return array{0: bool, 1: string} */
    private static function externalValue(string $key): array
    {
        $processValue = getenv($key);
        if ($processValue !== false) {
            return [true, (string) $processValue];
        }
        if (array_key_exists($key, $_ENV)) {
            return [true, (string) $_ENV[$key]];
        }
        if (array_key_exists($key, $_SERVER)) {
            return [true, (string) $_SERVER[$key]];
        }

        return [false, ''];
    }

    /** @return array<string, string> */
    private static function readEnvironmentFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }
        if (!is_readable($path)) {
            throw new RuntimeException('El archivo .env existe, pero no es legible.');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new RuntimeException('No fue posible leer el archivo .env.');
        }

        $values = [];
        foreach ($lines as $number => $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                $lineNumber = $number + 1;
                throw new RuntimeException("El archivo .env es inválido en la línea {$lineNumber}.");
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                $lineNumber = $number + 1;
                throw new RuntimeException("El archivo .env contiene una clave inválida en la línea {$lineNumber}.");
            }
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                if (strlen($value) < 2 || substr($value, -1) !== $quote) {
                    $lineNumber = $number + 1;
                    throw new RuntimeException("El archivo .env contiene un valor inválido en la línea {$lineNumber}.");
                }
                $value = substr($value, 1, -1);
            }
            $values[$key] = $value;
        }

        return $values;
    }

    /** @param array<string, string> $values */
    private static function validate(array $values): void
    {
        foreach (['APP_NAME', 'APP_ENV', 'APP_URL', 'DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_ENCODE'] as $key) {
            if ($values[$key] === '') {
                throw new RuntimeException("La variable de configuración {$key} no puede estar vacía.");
            }
        }
        if (!in_array($values['APP_ENV'], ['dev', 'prod'], true)) {
            throw new RuntimeException('La variable APP_ENV contiene un ambiente desconocido.');
        }
        if (filter_var($values['APP_URL'], FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('La variable APP_URL no contiene una URL válida.');
        }
        if (strtolower($values['DB_ENCODE']) !== 'utf8mb4') {
            throw new RuntimeException('La variable DB_ENCODE contiene una codificación no permitida.');
        }
    }
}
