<?php

namespace App\Services;

use App\Enums\BorrowingStatus;
use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Enums\ReturnCondition;
use App\Models\BookReturn;
use App\Models\Borrowing;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    public static function processBatchReturn(int $borrowingId, array $returnsData, int $staffId): Borrowing
    {
        return DB::transaction(function () use ($borrowingId, $returnsData, $staffId) {
            $borrowing = Borrowing::with('details.book')->where('id', $borrowingId)->lockForUpdate()->firstOrFail();

            if ($borrowing->status === BorrowingStatus::Returned) {
                throw ValidationException::withMessages([
                    'borrowing' => 'This transaction has already been returned.'
                ]);
            }

            $today = now()->toDateString();
            $fineLatePerDay = SettingService::getInt('fine_late_per_day', 1000);
            $fineDamagedPerBook = SettingService::getInt('fine_damaged_per_book', 20000);

            foreach ($borrowing->details as $detail) {
                $returnInput = $returnsData[$detail->id] ?? null;

                if (!$returnInput) {
                    throw ValidationException::withMessages([
                        'returns' => "The return data for the \"{$detail->book->title}\" book has not been filled in."
                    ]);
                }

                $condition = $returnInput['condition'] instanceof ReturnCondition ? $returnInput['condition'] : ReturnCondition::from($returnInput['condition']);

                $notes = $returnInput['notes'] ?? null;

                // Hitung keterlambatan (LOST = 0 hari terlambat)
                $lateDays = 0;
                if ($condition !== ReturnCondition::Lost) {
                    $dueDate = Carbon::parse($borrowing->due_at);
                    $returnDate = Carbon::parse($today);
                    $lateDays = max(0, $dueDate->diffInDays($returnDate, false));
                }

                // Buat record pengembalian
                $bookReturn = BookReturn::create([
                    'borrowing_detail_id' => $detail->id,
                    'returned_at' => $today,
                    'condition' => $condition,
                    'late_days' => $lateDays,
                    'created_by' => $staffId,
                    'notes' => $notes
                ]);

                // Generate denda
                if ($lateDays > 0 && $condition !== ReturnCondition::Lost) {
                    Fine::create([
                        'book_return_id' => $bookReturn->id,
                        'type' => FineType::Late,
                        'amount' => $lateDays * $fineLatePerDay,
                        'status' => FineStatus::Unpaid,
                    ]);
                }

                if ($condition === ReturnCondition::Damaged) {
                    Fine::create([
                        'book_return_id' => $bookReturn->id,
                        'type' => FineType::Damaged,
                        'amount' => $fineDamagedPerBook,
                        'status' => FineStatus::Unpaid
                    ]);
                }

                if ($condition === ReturnCondition::Lost) {
                    Fine::create([
                        'book_return_id' => $bookReturn->id,
                        'type' => FineType::Lost,
                        'amount' => $detail->book->price,
                        'status' => FineStatus::Unpaid
                    ]);

                    // LOST: kurangi stock total (bukan available_stock)
                    $detail->book->decrement('stock');
                } else {
                    // GOOD & DAMAGED: kembalikan ke available_stock
                    $detail->book->increment('available_stock');
                }
            }

            $borrowing->update(['status' => BorrowingStatus::Returned]);

            return $borrowing->fresh(['details.bookReturn.fines']);
        });
    }
}
