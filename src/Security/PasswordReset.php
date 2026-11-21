<?php
declare(strict_types=1);
namespace App\Security;
use PDO;
use Closure;

final class PasswordReset
{
    public function __construct(private PDO $pdo,private PasswordResetStore $store,private Closure $send,private string $baseUrl) {}
    public function request(string $email,string $origin): int
    {
        $ip=$this->store->limiter->reserveOrigin($origin);if($ip['retry_after'])return $ip['retry_after'];
        $identity=PasswordCredentials::accountIdentity($this->pdo,$email);
        $account=$this->store->limiter->reserveAccount($identity);if($account['retry_after'])return $account['retry_after'];
        $q=$this->pdo->prepare('SELECT id_login,correo,pass FROM login WHERE correo=:correo');$q->execute(['correo'=>$email]);$rows=$q->fetchAll(PDO::FETCH_ASSOC);
        if(count($rows)!==1)return 0;
        $u=$rows[0];$token=bin2hex(random_bytes(32));
        $this->store->put($token,(int)$u['id_login'],(string)$u['pass'],(string)$u['correo']);
        try {($this->send)((string)$u['correo'],$this->url($token));}
        catch(\Throwable) {$this->store->remove($token);error_log('PASSWORD_RESET_MAIL_FAILED');}
        return 0; // Mismo resultado público para correo inexistente o fallo de entrega.
    }
    private function url(string $token): string
    {
        $parts=parse_url($this->baseUrl);
        if(!is_array($parts)||!isset($parts['host'])||(isset($parts['user'])||isset($parts['pass']))||isset($parts['query'])||isset($parts['fragment'])||!in_array($parts['scheme']??'', ['https','http'],true))throw new \RuntimeException('RESET_URL_INVALID');
        if($parts['scheme']!=='https'&&!in_array(strtolower($parts['host']),['localhost','127.0.0.1','[::1]'],true))throw new \RuntimeException('RESET_HTTPS_REQUIRED');
        return rtrim($this->baseUrl,'/').'/form-doc/rec.pass.php#token='.$token;
    }
    private function current(array $ticket): ?array
    {
        $q=$this->pdo->prepare('SELECT id_login,correo,pass FROM login WHERE id_login=:id');$q->execute(['id'=>$ticket['login_id']]);$u=$q->fetch(PDO::FETCH_ASSOC);
        return is_array($u)&&hash_equals($ticket['credential'],hash('sha256',(string)$u['pass']))&&hash_equals($ticket['email'],hash('sha256',(string)$u['correo']))?$u:null;
    }
    public function verify(string $token): bool
    {
        $ticket=$this->store->read($token);return $ticket!==null&&$this->current($ticket)!==null;
    }
    public function complete(string $token,string $password,string $confirmation): bool
    {
        if(!PasswordCredentials::isValid($password)||!hash_equals($password,$confirmation))throw new \InvalidArgumentException('PASSWORD_INVALIDA');
        if(!$this->verify($token))return false;
        $hash=PasswordCredentials::hash($password);
        // Consumo local atómico previo al UPDATE: un fallo posterior exige un enlace nuevo.
        $ticket=$this->store->take($token);if(!$ticket)return false;
        $this->pdo->beginTransaction();
        try {
            $suffix=$this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql'?' FOR UPDATE':'';
            $q=$this->pdo->prepare('SELECT id_login FROM login WHERE id_login=:id'.$suffix);$q->execute(['id'=>$ticket['login_id']]);
            if($q->fetchColumn()===false||$this->current($ticket)===null){$this->pdo->rollBack();return false;}
            $q=$this->pdo->prepare('UPDATE login SET pass=:pass WHERE id_login=:id');$q->execute(['pass'=>$hash,'id'=>$ticket['login_id']]);
            if($q->rowCount()!==1)throw new \RuntimeException('RESET_UPDATE_FAILED');
            $this->pdo->commit();
        } catch(\Throwable $e){if($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
        try {$this->store->removeLogin((int)$ticket['login_id']);}catch(\Throwable){error_log('PASSWORD_RESET_CLEANUP_FAILED');}
        return true;
    }
}
