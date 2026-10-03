<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Models\Borrowing;
use App\Models\Member;
use App\Services\BorrowingService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Override;
use Throwable;

class CreateBorrowing extends CreateRecord
{
    protected static string $resource = BorrowingResource::class;

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    #[Override]
    protected function handleRecordCreation(array $data): Borrowing
    {
        try {
            $member = Member::findOrFail($data['member_id']);

            return BorrowingService::create(
                member: $member,
                bookIds: $data['book_ids'],
                staffId: Auth::id(),
                notes: $data['notes'] ?? null,
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal memproses pinjaman')
                ->body($e->getMessage())
                ->send();

            $this->halt();

            throw $e;
        }
    }
}