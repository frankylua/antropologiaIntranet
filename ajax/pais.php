<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap/session.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../src/bootstrap/app.php';

use App\Model\Pais;

/** @param mixed $payload */
function responderPais(int $statusCode, $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        responderPais(405, [
            'ok' => false,
            'codigo' => 'METODO_NO_PERMITIDO',
            'mensaje' => 'Utilice POST.',
        ]);
    }

    if (($_POST['op'] ?? null) !== 'read') {
        responderPais(400, [
            'ok' => false,
            'codigo' => 'OPERACION_INVALIDA',
            'mensaje' => 'Operación inválida.',
        ]);
    }

    $pais = new Pais();
    responderPais(200, $pais->mostrarConsultaOrdenada());
} catch (Throwable $exception) {
    error_log('Error interno en País: ' . $exception->getMessage());
    responderPais(500, [
        'ok' => false,
        'codigo' => 'ERROR_INTERNO',
        'mensaje' => 'No fue posible consultar el catálogo de países.',
    ]);
}
