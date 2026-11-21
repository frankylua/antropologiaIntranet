<?php
declare(strict_types=1);
namespace App\Security;
use App\Config\Configuration;
use PHPMailer\PHPMailer\PHPMailer;

final class PasswordResetMailer
{
    public function __construct(private Configuration $config) {}
    public function client(): PHPMailer
    {
        $c=$this->config;
        foreach(['SMTP_HOST','SMTP_USERNAME','SMTP_PASSWORD','SMTP_FROM'] as $key) if(trim($c->get($key))==='') throw new \RuntimeException('RESET_MAIL_NOT_CONFIGURED');
        if(!filter_var($c->get('SMTP_FROM'),FILTER_VALIDATE_EMAIL)
            || !in_array($c->get('SMTP_SECURITY'),['ssl','tls'],true)
            || !ctype_digit($c->get('SMTP_PORT')) || (int)$c->get('SMTP_PORT')<1 || (int)$c->get('SMTP_PORT')>65535
            || preg_match('/[\r\n;,\s\/]/',$c->get('SMTP_HOST'))) throw new \RuntimeException('RESET_MAIL_NOT_CONFIGURED');
        $mail=new PHPMailer(true);$mail->isSMTP();$mail->Host=$c->get('SMTP_HOST');$mail->Port=(int)$c->get('SMTP_PORT');
        $mail->SMTPAuth=true;$mail->SMTPSecure=$c->get('SMTP_SECURITY');$mail->Username=$c->get('SMTP_USERNAME');$mail->Password=$c->get('SMTP_PASSWORD');
        $mail->Timeout=10;$mail->Timelimit=15;$mail->SMTPDebug=0;$mail->CharSet='UTF-8';
        $mail->setFrom($c->get('SMTP_FROM'),$c->get('SMTP_FROM_NAME'));
        return $mail;
    }
    public function send(string $recipient,string $url): void
    {
        $mail=$this->client();$mail->addAddress($recipient);$mail->Subject='Recuperación de contraseña — Doctorado en Antropología';
        $mail->Body="Se solicitó restablecer la contraseña de tu cuenta.\n\nAbre este enlace en el navegador para elegir una nueva contraseña:\n".$url."\n\nEl enlace vence en 15 minutos y solo se puede usar una vez.\nSi no lo solicitaste, puedes ignorar este mensaje. Tu contraseña no ha cambiado.\n";
        $mail->send();
    }
}
