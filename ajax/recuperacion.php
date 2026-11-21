<?php
declare(strict_types=1);
require_once __DIR__.'/../src/bootstrap/session.php';
if(session_status()!==PHP_SESSION_ACTIVE)session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');header('Referrer-Policy: no-referrer');header('X-Content-Type-Options: nosniff');
function resetReply(int $status,string $message,array $extra=[]): never
{
    http_response_code($status);echo json_encode(['ok'=>$status===200,'mensaje'=>$message]+$extra,JSON_UNESCAPED_UNICODE);exit;
}
if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){header('Allow: POST');resetReply(405,'Método no permitido.');}
$csrf=$_POST['csrf']??null;
if(!is_string($csrf)||!isset($_SESSION['csrf_reset'])||!is_string($_SESSION['csrf_reset'])||!hash_equals($_SESSION['csrf_reset'],$csrf))resetReply(403,'Recarga la página e intenta nuevamente.');
$op=$_POST['op']??null;
if(!in_array($op,['solicitar','verificar','restablecer'],true))resetReply(400,'Operación no válida.');
try {
    require_once __DIR__.'/../src/bootstrap/app.php';
    $config=app_config();
    $mailer=new \App\Security\PasswordResetMailer($config);
    // Comprobar configuración antes de consultar existencia de una cuenta.
    if($op==='solicitar')$mailer->client();
    $store=new \App\Security\PasswordResetStore(\App\Security\PasswordResetStore::directory($config));
    $service=new \App\Security\PasswordReset(conexion(),$store,fn($to,$url)=>$mailer->send($to,$url),$config->get('APP_URL'));
    $origin=$_SERVER['REMOTE_ADDR']??'';
    if($op==='solicitar'){
        $email=$_POST['correo']??null;
        if(!is_string($email)||strlen($email)>254||!filter_var(trim($email),FILTER_VALIDATE_EMAIL))resetReply(422,'Ingresa un correo electrónico válido.');
        $retry=$service->request(trim($email),$origin);
        if(!$retry)resetReply(200,'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña. Revisa también la carpeta de spam.');
    }else{
        $ticket=$store->limiter->reserveOrigin($origin);$retry=$ticket['retry_after'];
        if(!$retry){
            $token=$_POST['token']??null;
            if(!is_string($token))resetReply(422,'El enlace no es válido. Solicita uno nuevo.');
            if($op==='verificar'){
                if(!$service->verify($token))resetReply(422,'El enlace venció, ya fue utilizado o no es válido. Solicita uno nuevo.');
                resetReply(200,'Enlace válido.');
            }
            $password=$_POST['pass']??null;$confirmation=$_POST['pass2']??null;
            if(!is_string($password)||!is_string($confirmation)||!\App\Security\PasswordCredentials::isValid($password))resetReply(422,'Ingresa una contraseña válida de 1 a 200 caracteres.');
            if(!hash_equals($password,$confirmation))resetReply(422,'Las contraseñas deben ser iguales.');
            if(!$service->complete($token,$password,$confirmation))resetReply(422,'El enlace venció, ya fue utilizado o no es válido. Solicita uno nuevo.');
            // No iniciar sesión automáticamente. Limpiar únicamente la sesión del navegador solicitante.
            $_SESSION=[];session_regenerate_id(true);
            resetReply(200,'Contraseña actualizada. Inicia sesión con tu nueva contraseña.');
        }
    }
    header('Retry-After: '.(int)$retry);resetReply(429,'Se alcanzó el límite de intentos. Espera '.(int)$retry.' segundos antes de volver a intentar.',['retry_after'=>(int)$retry]);
}catch(\Throwable){error_log('PASSWORD_RESET_UNAVAILABLE');resetReply(503,'La recuperación no está disponible temporalmente. Intenta más tarde. Si acabas de guardar una nueva contraseña, prueba ingresar; si no funciona, solicita otro enlace.');}
