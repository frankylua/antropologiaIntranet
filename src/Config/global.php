<?php
declare(strict_types=1);

// Adaptador temporal para consumidores heredados.
require_once dirname(__DIR__) . '/bootstrap/app.php';

// Datos de conexión (leídos desde .env)
defined('DB_HOST')     || define('DB_HOST', app_config('DB_HOST'));
defined('DB_NAME')     || define('DB_NAME', app_config('DB_NAME'));
defined('DB_USERNAME') || define('DB_USERNAME', app_config('DB_USERNAME'));
defined('DB_PASS')     || define('DB_PASS', app_config('DB_PASS'));
defined('DB_ENCODE')   || define('DB_ENCODE', app_config('DB_ENCODE'));

// Metadatos del proyecto
defined('NOM_PRO') || define('NOM_PRO', app_config('APP_NAME'));
defined('APP_ENV') || define('APP_ENV', app_config('APP_ENV'));
defined('APP_URL') || define('APP_URL', app_config('APP_URL'));

// Manejo de errores según entorno
if (APP_ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
