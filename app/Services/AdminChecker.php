<?php

namespace App\Services;

use App\Entities\User;

class AdminChecker
{
    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
