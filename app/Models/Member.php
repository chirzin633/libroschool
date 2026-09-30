<?php

namespace App\Models;

use App\Enums\MemberStatus;
use App\Enums\MemberType;
use Illuminate\Database\Eloquent\Model;
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
}
