<?php
declare(strict_types=1);
// Ruta heredada retirada: los cambios vigentes pasan por los editores autorizados.
http_response_code(410);
header('Content-Type: text/plain; charset=utf-8');
echo 'Esta ruta ya no está disponible. Use la edición de perfil autorizada de la intranet.';
