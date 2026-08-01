<?php
declare(strict_types=1);

return (static function (): bool {
    $sessionPolicy = [
        'session.use_strict_mode' => '1',
        'session.use_cookies' => '1',
        'session.use_only_cookies' => '1',
        'session.use_trans_sid' => '0',
        'session.cookie_httponly' => '1',
        'session.cookie_samesite' => 'Lax',
    ];
    $sessionPolicyStateKey = '__antropologia_intranet_session_policy_applied_v1';
    $sessionPolicyWasApplied = ($GLOBALS[$sessionPolicyStateKey] ?? false) === true;

    if (session_status() === PHP_SESSION_DISABLED) {
        throw new RuntimeException(
            'Las sesiones PHP están deshabilitadas.'
        );
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!$sessionPolicyWasApplied) {
            throw new RuntimeException(
                'El bootstrap de sesión se cargó después de iniciar la sesión.'
            );
        }

        foreach ($sessionPolicy as $directive => $expectedValue) {
            if (ini_get($directive) !== $expectedValue) {
                throw new RuntimeException(
                    "La directiva de sesión {$directive} no coincide con la política aplicada."
                );
            }
        }

        return true;
    }

    if ($sessionPolicyWasApplied) {
        foreach ($sessionPolicy as $directive => $expectedValue) {
            if (ini_get($directive) !== $expectedValue) {
                throw new RuntimeException(
                    "La directiva de sesión {$directive} no coincide con la política aplicada."
                );
            }
        }

        return true;
    }

    $protectedSessionDirectives = [
        'session.cookie_secure',
        'session.cookie_path',
        'session.cookie_domain',
        'session.cookie_lifetime',
        'session.name',
    ];
    $protectedSessionValues = [];
    $originalSessionPolicyValues = [];

    foreach ($protectedSessionDirectives as $directive) {
        $value = ini_get($directive);
        if ($value === false) {
            throw new RuntimeException(
                "No fue posible verificar la directiva de sesión protegida: {$directive}."
            );
        }
        $protectedSessionValues[$directive] = $value;
    }

    foreach ($sessionPolicy as $directive => $expectedValue) {
        $originalValue = ini_get($directive);
        if ($originalValue === false) {
            throw new RuntimeException(
                "No fue posible leer la directiva de sesión: {$directive}."
            );
        }
        $originalSessionPolicyValues[$directive] = $originalValue;
    }

    $appliedSessionDirectives = [];
    foreach ($sessionPolicy as $directive => $expectedValue) {
        $previousValue = @ini_set($directive, $expectedValue);
        if ($previousValue !== false) {
            $appliedSessionDirectives[] = $directive;
        }
        if ($previousValue === false || ini_get($directive) !== $expectedValue) {
            foreach (array_reverse($appliedSessionDirectives) as $appliedDirective) {
                @ini_set($appliedDirective, $originalSessionPolicyValues[$appliedDirective]);
            }
            throw new RuntimeException(
                "No fue posible aplicar la directiva de sesión: {$directive}."
            );
        }
    }

    foreach ($protectedSessionValues as $directive => $originalValue) {
        if (ini_get($directive) !== $originalValue) {
            foreach (array_reverse($appliedSessionDirectives) as $appliedDirective) {
                @ini_set($appliedDirective, $originalSessionPolicyValues[$appliedDirective]);
            }
            throw new RuntimeException(
                "El bootstrap alteró la directiva de sesión protegida: {$directive}."
            );
        }
    }

    $GLOBALS[$sessionPolicyStateKey] = true;

    return true;
})();
