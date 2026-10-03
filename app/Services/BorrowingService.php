<?php

namespace App\Services;

use App\Enums\BorrowingStatus;
use App\Enums\FineStatus;
use App\Enums\MemberStatus;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    public static function create(Member $member, array $bookIds, int $staffId, ?string $notes): Borrowing
    {
        if ($member->status !== MemberStatus::Active) {
            throw ValidationException::withMessages(['member_id' => "Member '{$member->name}' is not active"]);
        }

        if ($member->hasUnpaidFines()) {
            throw ValidationException::withMessages(['member_id' => "Member '{$member->name}' has unpaid fines."]);
        }

        $maxBooks = SettingService::getInt('max_books_per_borrowing', 3);
        if (count($bookIds) > $maxBooks) {
            throw ValidationException::withMessages(['books' => "Maximum of {$maxBooks} books per transaction."]);
        }

        // Cek duplikat buku dalam request
        if (count($bookIds) !== count(array_unique($bookIds))) {
            throw ValidationException::withMessages(['books' => 'Duplicate books detected in the selection']);
        }

        return DB::transaction(function () use ($member, $bookIds, $staffId, $notes) {
            $today = now()->toDateString();
            $loanDays = SettingService::getInt('loan_days', 7);

            // Generate kode dengan locking
            $code = CodeGeneratorService::generateBorrowingCode(now());

            $borrowing = Borrowing::create([
                'borrowing_code' => $code,
                'member_id' => $member->id,
                'created_by' => $staffId,
                'borrowed_at' => $today,
                'due_at' => now()->addDays($loanDays)->toDateString(),
                'status' => BorrowingStatus::Borrowed,
                'notes' => $notes
            ]);

            // Lock buku BERURUTAN berdasarkan book_id untuk mencegah deadlock
            $sortedBookIds = collect($bookIds)->sort()->values()->all();
            $books = Book::whereIn('id', $sortedBookIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($sortedBookIds as $bookId) {
                $book = $books->get($bookId);

                if (!$book) {
                    throw ValidationException::withMessages(['books' => "Book ID {$bookId} is not found."]);
                }

                if ($book->available_stock < 1) {
                    throw ValidationException::withMessages([
                        'books' => "\"{$book->title}\" books are out of stock."
                    ]);
                }

                $alreadyBorrowed = BorrowingDetail::query()
                    ->join('borrowings', 'borrowing_details.borrowing_id', '=', 'borrowings.id')
                    ->where('borrowings.member_id', $member->id)
                    ->where('borrowings.status', BorrowingStatus::Borrowed)
                    ->where('borrowing_details.book_id', $bookId)
                    ->exists();

                if ($alreadyBorrowed) {
                    throw ValidationException::withMessages([
                        'books' => "\"{$book->title}\" is currently borrowed by this member and has not been returned yet."
                    ]);
                }

                // Kurangi stok
                $book->decrement('available_stock');

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $bookId
                ]);
            }

            return $borrowing->load('details.book');
        });
    }
}
