<?php

namespace App\Models;

use App\Enums\BorrowingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    protected $fillable = [
        'borrowing_code',
        'member_id',
        'created_by',
        'borrowed_at',
        'due_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => BorrowingStatus::class,
        'borrowed_at' => 'date',
        'due_at' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class)->withTrashed();
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }
}
