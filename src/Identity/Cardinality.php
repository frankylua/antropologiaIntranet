<?php
declare(strict_types=1);

namespace App\Identity;

final class Cardinality
{
    public const NONE = 'NONE';
    public const SINGLE = 'SINGLE';
    public const MULTIPLE = 'MULTIPLE';

    private function __construct()
    {
    }

    public static function fromCount(int $count): string
    {
        if ($count <= 0) {
            return self::NONE;
        }

        return $count === 1 ? self::SINGLE : self::MULTIPLE;
    }
}
