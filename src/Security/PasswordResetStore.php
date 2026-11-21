<?php
declare(strict_types=1);
namespace App\Security;
use PDO;
use Closure;
use App\Config\Configuration;

/** Datos operativos locales: nunca guarda el token que recibe el usuario. */
final class PasswordResetStore
{
    private PDO $db;
    private Closure $clock;
    public LoginAttemptLimiter $limiter;
    public static function directory(Configuration $config): string
    {
        return $config->get('PASSWORD_RESET_DIR') ?: sys_get_temp_dir().'/antropologia-reset-'.substr(hash('sha256',dirname(__DIR__,2).'|'.$config->get('DB_HOST').'|'.$config->get('DB_NAME')),0,24);
    }
    public function __construct(string $directory, ?Closure $clock=null)
    {
        $this->clock=$clock??static fn():int=>time();
        // El limitador existente valida ruta absoluta, resolución física y exclusión de webroot.
        $this->limiter=new LoginAttemptLimiter($directory.'/limits',3,10,900,900,$this->clock);
        $parent=dirname((string)realpath($directory.'/limits'));
        $file=$parent.'/tokens.sqlite';
        foreach (['','-journal','-wal','-shm'] as $suffix) if(is_link($file.$suffix)) throw new \RuntimeException('RESET_STORE_UNAVAILABLE');
        $mask=umask(0077);
        try {$this->db=new PDO('sqlite:'.$file,null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
        finally {umask($mask);}
        $this->db->exec('PRAGMA busy_timeout=3000');
        $this->db->exec('CREATE TABLE IF NOT EXISTS tokens (digest TEXT PRIMARY KEY, login_id INTEGER NOT NULL, credential TEXT NOT NULL, email TEXT NOT NULL, expires INTEGER NOT NULL)');
        $this->db->exec('CREATE INDEX IF NOT EXISTS tokens_login ON tokens(login_id)');
    }
    public function put(string $token,int $login,string $credential,string $email): void
    {
        $this->db->exec('BEGIN IMMEDIATE');
        try {
            $q=$this->db->prepare('DELETE FROM tokens WHERE expires<=?');$q->execute([($this->clock)()]);
            if((int)$this->db->query('SELECT COUNT(*) FROM tokens')->fetchColumn()>=10000) throw new \RuntimeException('RESET_STORE_FULL');
            $q=$this->db->prepare('INSERT INTO tokens VALUES (?,?,?,?,?)');
            $q->execute([hash('sha256',$token),$login,hash('sha256',$credential),hash('sha256',$email),($this->clock)()+900]);
            $this->db->exec('COMMIT');
        } catch(\Throwable $e) {$this->db->exec('ROLLBACK');throw $e;}
    }
    public function read(string $token): ?array
    {
        if(preg_match('/\A[a-f0-9]{64}\z/D',$token)!==1) return null;
        $q=$this->db->prepare('SELECT * FROM tokens WHERE digest=? AND expires>?');$q->execute([hash('sha256',$token),($this->clock)()]);
        $r=$q->fetch();return is_array($r)?$r:null;
    }
    public function take(string $token): ?array
    {
        $this->db->exec('BEGIN IMMEDIATE');
        try {$r=$this->read($token);if($r)$this->remove($token);$this->db->exec('COMMIT');return $r;}
        catch(\Throwable $e){$this->db->exec('ROLLBACK');throw $e;}
    }
    public function remove(string $token): void
    {
        $q=$this->db->prepare('DELETE FROM tokens WHERE digest=?');$q->execute([hash('sha256',$token)]);
    }
    public function removeLogin(int $login): void
    {
        $q=$this->db->prepare('DELETE FROM tokens WHERE login_id=?');$q->execute([$login]);
    }
}
