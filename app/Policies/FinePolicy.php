<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    // Semua role bisa melihat denda
    public function viewAny(User $user)
    {
        return true;
    }

    // Semua role bisa memproses pembayaran denda
    public function update(User $user, Fine $fine)
    {
        return true;
    }

    // Denda tidak bisa dihapus atau dibuat manual
    public function create(User $user)
    {
        return false;
    }

    public function delete(User $user, Fine $fine)
    {
        return false;
    }
}
