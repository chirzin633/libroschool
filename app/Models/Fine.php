<?php

namespace App\Models;

use App\Enums\FineType;
use Illuminate\Database\Eloquent\Model;
use App\Enums\FineStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'book_return_id',
        'type',
        'amount',
        'status',
        'paid_at',
        'paid_by'
    ];

    protected $casts = [
        'type' => FineType::class,
        'amount' => 'decimal:2',
        'status' => FineStatus::class,
        'paid_at' => 'datetime',
    ];

    public function bookReturn(): BelongsTo
    {
        return $this->belongsTo(BookReturn::class);
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by')->withTrashed();
    }
}
