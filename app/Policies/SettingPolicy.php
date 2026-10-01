<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class SettingPolicy
{
    // Hanya Pustakawan yang bisa melihat & mengubah pengaturan sistem
    public function viewAny(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function update(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }
}
