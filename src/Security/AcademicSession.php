<?php
declare(strict_types=1);

namespace App\Security;

use App\Model\Login;
use DomainException;
use PDO;

/** Actividad del perfil: nunca modifica permisos almacenados ni relaciones. */
final class AcademicSession
{
    private static ?bool $ready = null;
    private static bool $refreshed = false;
    private const ROLES = [1=>'admin', 2=>'comite', 3=>'aceptado', 4=>'docente', 5=>'estudiante'];
    private const KEYS = ['login','admin','comite','aceptado','docente','estudiante','id_usuario','estado_profesor','capacidades'];

    public static function migrationReady(PDO $pdo): bool
    {
        if (self::$ready !== null) return self::$ready;
        try {
            $q = $pdo->query('SELECT tipo FROM tipo_estudiante WHERE id_tipo_est=6');
            if (strtolower(trim((string)$q->fetchColumn())) !== 'inactivo(a)') return self::$ready = false;
            $q = $pdo->query("SELECT CHECK_CLAUSE FROM information_schema.CHECK_CONSTRAINTS "
                . "WHERE CONSTRAINT_SCHEMA=DATABASE() AND CONSTRAINT_NAME='chk_profesor_estado'");
            $clause = preg_replace('/[\\s`]+/', '', strtolower((string)$q->fetchColumn()));
            return self::$ready = str_contains($clause, 'in(1,2,3,4)');
        } catch (\Throwable $error) {
            return self::$ready = false;
        }
    }

    public static function requireMigration(PDO $pdo): void
    {
        if (!self::migrationReady($pdo)) throw new DomainException('INACTIVACION_NO_DISPONIBLE',409);
    }

    /** Conserva las filas originales; filtra solo el acceso efectivo del perfil inactivo. */
    public static function activity(PDO $pdo, int $login, array $permissions): array
    {
        foreach ($permissions as $row) {
            if ((int)($row['id_login'] ?? 0) !== $login) throw new DomainException('IDENTIDAD_INCOMPATIBLE',409);
        }
        $ids = array_map(static fn($row)=>(int)$row['id_permiso'], $permissions);
        $data = (new Login())->obtenerActividadPorLogin($login);
        $students = $data['estudiantes'];
        $professors = $data['profesores'];
        if ((in_array(5,$ids,true) && (count($students)!==1 || count($data['usuarios'])!==1))
            || (in_array(4,$ids,true) && (count($professors)!==1 || count($data['usuarios'])!==1))
            || count($students)>1 || count($professors)>1) {
            throw new DomainException('IDENTIDAD_INCOMPATIBLE',409);
        }
        $student = count($students)===1 ? (int)$students[0] : null;
        $professor = count($professors)===1 ? (int)$professors[0] : null;
        if (($student!==null && !in_array($student,[1,2,3,4,5,6,7],true))
            || ($professor!==null && !in_array($professor,[1,2,3,4],true))) {
            throw new DomainException('IDENTIDAD_INCOMPATIBLE',409);
        }
        $inactiveStudent = $student===6 && self::migrationReady($pdo);
        $inactiveProfessor = $professor===4;
        $inactive = $inactiveStudent || $inactiveProfessor;
        $effective = array_values(array_filter($permissions,static function($row) use($inactiveStudent,$inactiveProfessor,$inactive,$student,$ids) {
            $id=(int)$row['id_permiso'];
            if (($id===5 && $inactiveStudent) || ($id===4 && $inactiveProfessor)) return false;
            // El permiso histórico 3 aislado no rescata un perfil inactivo.
            if ($id===3 && $inactive && !(in_array(5,$ids,true) && in_array($student,[2,3],true))) return false;
            return true;
        }));
        $usable = array_filter($effective,static fn($row)=>isset(self::ROLES[(int)$row['id_permiso']]));
        if ($inactive && !$usable) throw new DomainException('PERFIL_INACTIVO',403);
        return $data + ['student'=>$student,'professor'=>$professor,'inactive'=>$inactive,'permissions'=>$effective];
    }

    /** Revalidación por petición para sesiones autenticadas antes de la inactivación. */
    public static function enforce(): void
    {
        // Algunos módulos cargan app.php antes de session_start(). Abrir únicamente
        // una sesión ya presentada por cookie permite revalidarla antes de sus gates.
        if (session_status()===PHP_SESSION_NONE && isset($_COOKIE[session_name()])
            && is_string($_COOKIE[session_name()]) && $_COOKIE[session_name()]!=='') {
            session_start();
        }
        if (self::$refreshed || session_status()!==PHP_SESSION_ACTIVE) return;
        $hasContext=isset($_SESSION['login']);
        foreach (self::ROLES as $role) $hasContext=$hasContext || isset($_SESSION[$role]);
        if (!$hasContext) return;
        $script = str_replace('\\','/',(string)($_SERVER['SCRIPT_FILENAME'] ?? ''));
        if (str_ends_with($script,'/ajax/login.php')) return;
        self::$refreshed = true;
        try {
            $value=$_SESSION['login']??null;
            $login=(is_int($value)||is_string($value))
                ? filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]) : false;
            if ($login===false) throw new DomainException('SESION_INVALIDA',401);
            $pdo = \conexion();
            $q = $pdo->prepare('SELECT id_login,id_permiso FROM permiso_login WHERE id_login=:login ORDER BY id_permiso');
            $q->execute(['login'=>$login]);
            $data = self::activity($pdo,$login,$q->fetchAll(PDO::FETCH_ASSOC));
            if (!$data['inactive']) return;
            foreach (self::KEYS as $key) unset($_SESSION[$key]);
            $_SESSION['login']=$login;
            foreach ($data['permissions'] as $row) {
                if (isset(self::ROLES[(int)$row['id_permiso']])) $_SESSION[self::ROLES[(int)$row['id_permiso']]]=$login;
            }
            $caps=[];
            if (isset($_SESSION['estudiante'])) $caps[]='perfil.ver';
            if (isset($_SESSION['estudiante']) && in_array($data['student'],[1,2,3,4,5,7],true)) $caps[]='reglamento.ver';
            $doc = isset($_SESSION['docente']) && $data['professor']===2;
            $student = isset($_SESSION['estudiante']) && in_array($data['student'],[2,3],true);
            $global = isset($_SESSION['admin']) || isset($_SESSION['comite']);
            if ($doc) $caps[]='docente.habilitado';
            if ($global || $doc || $student || isset($_SESSION['aceptado'])) $caps[]='cursos.ver';
            if ($global || $doc || $student) $caps[]='calendario.ver';
            $_SESSION['capacidades']=$caps;
            if (isset($_SESSION['docente'])) $_SESSION['estado_profesor']=$data['professor'];
            if (isset($_SESSION['docente']) || isset($_SESSION['estudiante'])) {
                $_SESSION['id_usuario']=[['id_usuario'=>(int)$data['usuarios'][0]]];
            }
            // La vista propia puede haber evaluado su gate antes de cargar header.php.
            if ((str_ends_with($script,'/form-doc/info.docente.php') && !isset($_SESSION['docente']))
                || (str_ends_with($script,'/form-doc/info.estudiante.php') && !isset($_SESSION['estudiante']))) {
                header('Location: '.rtrim((string)\app_config('APP_URL'),'/').'/index.php');
                exit;
            }
        } catch (\Throwable $error) {
            $domain = $error instanceof DomainException;
            $code = $domain ? $error->getMessage() : 'ERROR_VALIDACION_ACCESO';
            $status = $domain ? (int)$error->getCode() : 503;
            if (in_array($code,['PERFIL_INACTIVO','SESION_INVALIDA'],true)) foreach (self::KEYS as $key) unset($_SESSION[$key]);
            if (!$domain) error_log('[ACADEMIC_SESSION] '.get_class($error));
            http_response_code($status);
            if (str_contains($script,'/ajax/') || isset($_POST['op'])) {
                header('Content-Type: application/json; charset=utf-8');
                header('Cache-Control: no-store');
                echo json_encode(['ok'=>false,'error'=>$code,'mensaje'=>$code==='PERFIL_INACTIVO'
                    ? 'El perfil está inactivo. Solicite su reactivación a Admin o Comité.'
                    : 'No fue posible validar el acceso de la cuenta.'],JSON_UNESCAPED_UNICODE);
            } else {
                header('Location: '.rtrim((string)\app_config('APP_URL'),'/').'/form-doc/login.php');
            }
            exit;
        }
    }
}
