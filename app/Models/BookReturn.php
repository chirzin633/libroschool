<?php

namespace App\Models;

use App\Enums\ReturnCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookReturn extends Model
{
    protected $fillable = [
        'borrowing_detail_id',
        'returned_at',
        'condition',
        'late_days',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'condition' => ReturnCondition::class,
        'returned_at' => 'date',
        'late_days' => 'integer',
    ];

    public function borrowingDetail(): BelongsTo
    {
        return $this->belongsTo(BorrowingDetail::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }
}
