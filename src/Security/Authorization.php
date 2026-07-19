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

    public static function hasCapability(string $capability): bool
    {
        return isset($_SESSION['capacidades'])
            && is_array($_SESSION['capacidades'])
            && in_array($capability, $_SESSION['capacidades'], true);
    }
}
