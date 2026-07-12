<?php

namespace App\Security;

final class Authorization
{
    public static function hasAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (isset($_SESSION[$permission])) {
                return true;
            }
        }

        return false;
    }
}
