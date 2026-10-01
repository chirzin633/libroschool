<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    // Semua role bisa melihat buku (untuk cek ketersediaan)
    public function viewAny(User $user)
    {
        return true;
    }

    // Hanya Pustakawan yang bisa CRUD master buku
    public function create(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function update(User $user)
    {
        return $user->role === UserRole::Pustakawan;
    }

    public function delete(User $user, Book $book)
    {
        if ($user->role !== UserRole::Pustakawan) {
            return false;
        }

        $hasActiveBorrowing = $book->borrowingDetails()
            ->whereHas('borrowing', fn($q) => $q->where('status', 'Borrowed'))->exists();

        return !$hasActiveBorrowing;
    }
}
