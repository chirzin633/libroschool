<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    // Semua role bisa melihat peminjaman
    public function viewAny(User $user)
    {
        return true;
    }

    // Semua role bisa memproses peminjaman
    public function create(User $user)
    {
        return true;
    }

    // Tidak ada edit peminjaman yang sudah dibuat
    public function update(User $user, Borrowing $borrowing)
    {
        return false;
    }

    // Peminjaman tidak bisa dihapus manual, hanya berubah status via ReturnService
    public function delete(User $user, Borrowing $borrowing)
    {
        return false;
    }
}
