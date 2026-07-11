<?php
declare(strict_types=1);

// Carga variables de entorno desde .env (no versionado)
require_once __DIR__ . '/env.php';

// Datos de conexión (leídos desde .env)
define('DB_HOST',     env('DB_HOST', 'localhost'));
define('DB_NAME',     env('DB_NAME', 'c1441353_antr_db'));
define('DB_USERNAME', env('DB_USERNAME', 'root'));
define('DB_PASS',     env('DB_PASS', ''));
define('DB_ENCODE',   env('DB_ENCODE', 'utf8mb4'));

// Metadatos del proyecto
define('NOM_PRO',  env('APP_NAME', 'Doctorado Antropología'));
define('APP_ENV',  env('APP_ENV', 'prod'));    // dev | prod
define('APP_URL',  env('APP_URL', 'http://localhost/antropologiaIntranet/'));

// Manejo de errores según entorno
if (APP_ENV === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
