<?php
declare(strict_types=1);

namespace App\Identity;

final class IdentityResolution
{
    /** @param list<string> $inconsistencies */
    public function __construct(
        private readonly int $loginId,
        private readonly bool $loginFound,
        private readonly string $userCardinality,
        private readonly ?int $userId,
        private readonly string $studentCardinality,
        private readonly ?int $studentStatus,
        private readonly string $professorCardinality,
        private readonly array $inconsistencies,
    ) {
    }

    public function loginId(): int { return $this->loginId; }
    public function loginFound(): bool { return $this->loginFound; }
    public function userCardinality(): string { return $this->userCardinality; }
    public function userId(): ?int { return $this->userId; }
    public function studentCardinality(): string { return $this->studentCardinality; }
    public function studentStatus(): ?int { return $this->studentStatus; }
    public function professorCardinality(): string { return $this->professorCardinality; }

    /** @return list<string> */
    public function inconsistencies(): array { return $this->inconsistencies; }
}
