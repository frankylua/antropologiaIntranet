<?php
declare(strict_types=1);

namespace App\Config;

use PDO;

final class ConnectionAuthority
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function connection(Configuration $configuration): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = 'mysql:host=' . $configuration->get('DB_HOST')
            . ';dbname=' . $configuration->get('DB_NAME')
            . ';charset=' . $configuration->get('DB_ENCODE');

        self::$connection = new PDO(
            $dsn,
            $configuration->get('DB_USERNAME'),
            $configuration->get('DB_PASS'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        return self::$connection;
    }
}
