<?php
declare(strict_types=1);

use App\Config\Configuration;

if (!isset($GLOBALS['app_config']) || !$GLOBALS['app_config'] instanceof Configuration) {
    require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

    $GLOBALS['app_config'] = Configuration::resolve(dirname(__DIR__, 2) . '/.env');
}

if (!function_exists('app_config')) {
    function app_config(?string $key = null): Configuration|string
    {
        $configuration = $GLOBALS['app_config'] ?? null;
        if (!$configuration instanceof Configuration) {
            throw new RuntimeException('La configuración de la aplicación no ha sido inicializada.');
        }

        return $key === null ? $configuration : $configuration->get($key);
    }
}

require_once dirname(__DIR__) . '/Config/conexion.php';

return $GLOBALS['app_config'];
