<?php
declare(strict_types=1);

namespace App\Identity;

use InvalidArgumentException;
use PDO;

final class IdentityResolver
{
    private const VALID_STUDENT_STATUSES = [1, 2, 3, 4, 5, 6, 7];

    public function __construct(private readonly PDO $connection)
    {
    }

    public function resolveByLoginId(mixed $loginId): IdentityResolution
    {
        if (!is_int($loginId) || $loginId <= 0) {
            throw new InvalidArgumentException('El identificador de login debe ser un entero positivo.');
        }

        if (!$this->loginExists($loginId)) {
            return new IdentityResolution($loginId, false, Cardinality::NONE, null, Cardinality::NONE, null, Cardinality::NONE, ['LOGIN_NO_ENCONTRADO']);
        }

        $users = $this->fetchUserIds($loginId);
        $userCardinality = Cardinality::fromCount(count($users));
        if ($userCardinality !== Cardinality::SINGLE) {
            return new IdentityResolution(
                $loginId, true, $userCardinality, null, Cardinality::NONE, null, Cardinality::NONE,
                $userCardinality === Cardinality::MULTIPLE ? ['LOGIN_CON_MULTIPLES_USUARIOS'] : [],
            );
        }

        $userId = $users[0];
        $students = $this->fetchStudentStatuses($userId);
        $professors = $this->fetchProfessorIds($userId);
        $studentCardinality = Cardinality::fromCount(count($students));
        $professorCardinality = Cardinality::fromCount(count($professors));
        $inconsistencies = [];
        $studentStatus = null;

        if ($studentCardinality === Cardinality::MULTIPLE) {
            $inconsistencies[] = 'USUARIO_CON_MULTIPLES_ESTUDIANTES';
        } elseif ($studentCardinality === Cardinality::SINGLE) {
            $candidateStatus = $students[0];
            if (!in_array($candidateStatus, self::VALID_STUDENT_STATUSES, true)) {
                $inconsistencies[] = 'TIPO_EST_INVALIDO';
            } else {
                $studentStatus = $candidateStatus;
            }
        }

        if ($professorCardinality === Cardinality::MULTIPLE) {
            $inconsistencies[] = 'USUARIO_CON_MULTIPLES_PROFESORES';
        }

        return new IdentityResolution($loginId, true, $userCardinality, $userId, $studentCardinality, $studentStatus, $professorCardinality, $inconsistencies);
    }

    private function loginExists(int $loginId): bool
    {
        $statement = $this->connection->prepare('SELECT id_login FROM login WHERE id_login = :login_id');
        $statement->execute(['login_id' => $loginId]);
        return $statement->fetchColumn() !== false;
    }

    /** @return list<int> */
    private function fetchUserIds(int $loginId): array
    {
        $statement = $this->connection->prepare('SELECT id_usuario FROM usuario WHERE login = :login_id');
        $statement->execute(['login_id' => $loginId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /** @return list<int> */
    private function fetchStudentStatuses(int $userId): array
    {
        $statement = $this->connection->prepare('SELECT tipo_est FROM estudiante WHERE usuario = :user_id');
        $statement->execute(['user_id' => $userId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    /** @return list<int> */
    private function fetchProfessorIds(int $userId): array
    {
        $statement = $this->connection->prepare('SELECT id_profesor FROM profesor WHERE usuario = :user_id');
        $statement->execute(['user_id' => $userId]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }
}
