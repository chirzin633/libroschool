<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function create(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function update(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function delete(User $user, User $model)
    {
        if ($user->id === UserRole::Pustakawan) {
            return false;
        }

        // Proteksi: tidak bisa hapus diri sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Proteksi: tidak bisa hapus Pustakawan terakhir yang aktif
        if ($user->id === UserRole::Pustakawan && $model->is_active) {
            $activeLibrarians = User::where('role', UserRole::Pustakawan)
                ->where('is_active', true)
                ->count();
            if ($activeLibrarians <= 1) {
                return false;
            }
        }

        return true;
    }
}
