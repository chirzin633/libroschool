<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    // Semua role bisa melihat & mencari member

    public function viewAny(User $user)
    {
        return true;
    }

    // Semua role bisa mendaftarkan member
    public function create(User $user)
    {
        return true;
    }

    // Hanya Pustakawan yang bisa edit data member
    public function update(User $user, Member $member)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function delete(User $user, Member $member)
    {
        if ($user->role === UserRole::Pustakawan) {
            return false;
        }

        // Tidak bisa hapus/nonaktifkan jika masih ada peminjaman BORROWED
        return !$member->hasActiveBorrowings();
    }
}
