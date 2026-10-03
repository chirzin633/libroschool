<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Override;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan available_stock tidak melebihi stock saat create
        if (($data['available_stock'] ?? 0) > ($data['stock'] ?? 0)) {
            throw ValidationException::withMessages([
                'available_stock' => 'Stok tersedia tidak boleh melebihi stok total.'
            ]);
        }

        return $data;
    }
}