<?php

namespace App\Models;

use App\Enums\MemberStatus;
use App\Enums\MemberType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_code',
        'name',
        'type',
        'class_position',
        'gender',
        'phone',
        'address',
        'photo',
        'status',
        'registered_at',
        'inactive_at',
    ];

    protected $casts = [
        'type' => MemberType::class,
        'status' => MemberStatus::class,
        'registered_at' => 'date',
        'inactive_at' => 'date',
    ];

    public function isEligibleToBorrow(): bool
    {
        return $this->status->canBorrow();
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Cek apakah member memiliki peminjaman aktif (BORROWED)
     * Digunakan untuk proteksi: tidak bisa dinonaktifkan/dihapus jika masih ada pinjaman aktif
     */
    public function hasActiveBorrowings(): bool
    {
        return $this->borrowings()->where('status', 'Borrowed')->exists();
    }

    /**
     * Cek apakah member memiliki denda belum lunas
     * Digunakan untuk validasi sebelum peminjaman baru
     */
    public function hasUnpaidFines(): bool
    {
        return Fine::whereHas('bookReturn.borrowingDetail.borrowing', function (Builder $query) {
            $query->where('member_id', $this->id);
        })->where('status', 'Unpaid')->exists();
    }

    /**
     * Cek apakah member sedang meminjam buku tertentu (lintas transaksi)
     * "tidak boleh meminjam buku yang sama selama masih ada transaksi BORROWED"
     */
    public function isCurrentlyBorrowingBook(int $bookId): bool
    {
        return BorrowingDetail::where('book_id', $bookId)->whereHas('borrowing', function (Builder $query) {
            $query->where('member_id', $this->id)->where('status', 'Borrowed');
        })->exists();
    }

    /**
     * 
     * "Pencarian member dilakukan berdasarkan Nama, Kelas/Jabatan, atau Member Code"
     */
    public function scopeSearch(Builder $query, ?string $term)
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'ilike', "%{$term}%")->orWhere('class_position', 'ilike', "%{$term}%")->orWhere('member_code', 'ilike', "%{$term}%");
        });
    }
}
