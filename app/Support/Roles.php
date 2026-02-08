<?php

namespace App\Support;

use Illuminate\Auth\Access\AuthorizationException;

class Roles
{
    public static function require($user, array $allowed): void
    {
        if (!in_array($user->role, $allowed, true)) {
            throw new AuthorizationException('Role not allowed.');
        }
    }
}
