<?php

namespace App\Filament\Resources\Members\Pages;

use App\Enums\MemberStatus;
use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function beforeSave()
    {
        $record = $this->getRecord();
        $newStatus = $this->form->getState()['status'] ?? null;

        // tidak bisa dinonaktifkan jika masih ada peminjaman aktif
        if ($newStatus === MemberStatus::Inactive->value && $record->hasActiveBorrowings()) {
            Notification::make()
                ->danger()
                ->title('Tidak dapat menonaktifkan')
                ->body("Anggota \"{$record->name}\" masih memiliki peminjaman aktif.")
                ->send();
            $this->halt();
        }
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-set inactive_at saat berubah ke INACTIVE
        if (($data['status'] ?? null) === MemberStatus::Inactive->value && empty($data['inactive_at'])) {
            $data['inactive_at'] = now()->toDateString();
        }

        // Reset inactive_at jika kembali ACTIVE
        if (($data['status'] ?? null) === MemberStatus::Active->value) {
            $data['inactive_at'] = null;
        }

        return $data;
    }

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}