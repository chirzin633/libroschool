<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CodeGeneratorService
{
    public static function generateMemberCode(?int $year = null)
    {
        $targetYear = $year ?? now()->year;
        $prefix = "MBR-{$targetYear}-";

        return DB::transaction(function () use ($prefix, $targetYear) {
            $lockKey = "libroschool_member_seq_{$targetYear}";
            DB::statement("SELECT pg_advisory_xact_lock(hashtext(?))", [$lockKey]);

            $lastCode = Member::withTrashed()
                ->where('member_code', 'LIKE', "{$prefix}")
                ->orderByDesc('member_code')
                ->value('member_code');

            if ($lastCode) {
                $currentNumber = (int) substr($lastCode, strlen($prefix));
                $nextNumber = $currentNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return $prefix . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateBorrowingCode(Carbon $date)
    {
        $dateStr = $date->format('Ymd');
        $prefix = "BRW-{$dateStr}-";

        return DB::transaction(function () use ($prefix, $dateStr) {
            $lockKey = "libroschool_borrowing_seq_{$dateStr}";
            DB::statement("SELECT pg_advisory_xact_lock(hashtext(?))", [$lockKey]);

            $lastCode = Borrowing::where('borrowing_code', 'LIKE', "{$prefix}%")->orderByDesc('borrowing_code')->value('borrowing_code');

            $nextNumber = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;

            return $prefix . str_pad((string)$nextNumber, 4, 0, STR_PAD_LEFT);
        });
    }
}
