<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Override;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;


    protected function beforeSave()
    {
        $data = $this->form->getState();
        $adjustment = (int) ($data['stock_adjustment'] ?? 0);

        if ($adjustment === 0) {
            return;
        }

        $record = $this->getRecord();
        $newStock = $record->stock + $adjustment;
        $newAvailable = $record->available_stock + $adjustment;

        // Validasi constraint DB: available_stock >= 0 AND available_stock <= stock
        if ($newAvailable < 0 || $newAvailable > $newStock) {
            Notification::make()
                ->danger()
                ->title('Penyesuaian stok ditolak')
                ->body("Hasil penyesuaian akan melanggar constraint stok. Stok: {$newStock}, Tersedia: {$newAvailable}")
                ->send();

            throw ValidationException::withMessages([
                'stock_adjustment' => 'Nilai penyesuaian menghasilkan stok tersedia yang tidak valid.'
            ]);
        }
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $adjustment = (int) ($data['stock_adjustment'] ?? 0);

        if (!$adjustment !== 0) {
            $record = $this->getRecord();
            $data['stock'] = $record->stock + $adjustment;
            $data['available_stock'] = $record->available_stock + $adjustment;
        }

        // Hapus field virtual agar tidak masuk ke model
        unset($data['stock_adjustment']);

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