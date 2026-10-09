<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Override;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function beforeSave()
    {
        $record = $this->getRecord();
        $currentUser = Auth::user();
        $data = $this->form->getState();

        if ($currentUser->id === $record->id && isset($data['is_active']) && !$data['is_active']) {
            Notification::make()
                ->danger()
                ->title('Gagal menonaktifkan')
                ->body('Anda tidak dapat menonaktifkan akun Anda sendiri.')
                ->send();
            $this->halt();
            return;
        }

        if ($record->role === UserRole::Pustakawan && $record->is_active && isset($data['is_active']) && !$data['is_active']) {

            $activeLibrarians = User::where('role', UserRole::Pustakawan)
                ->where('is_active', true)
                ->count();

            if ($activeLibrarians <= 1) {
                Notification::make()
                    ->danger()
                    ->title('Gagal menonaktifkan')
                    ->body('Tidak dapat menonaktifkan Pustakawan terakhir yang aktif. Sistem akan terkunci.')
                    ->send();
                $this->halt();
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (User $record, DeleteAction $action) {

                    $currentUser = Auth::user();

                    if ($currentUser->id === $record->id) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal menghapus')
                            ->body('Anda tidak dapat menghapus akun Anda sendiri.')
                            ->send();
                        $action->halt();
                        return;
                    }

                    if ($record->role === UserRole::Pustakawan && $record->is_active) {

                        $activeLibrarians = User::where('role', UserRole::Pustakawan)
                            ->where('is_active', true)
                            ->count();

                        if ($activeLibrarians <= 1) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal menghapus')
                                ->body('Tidak dapat menghapus Pustakawan terakhir yang aktif.')
                                ->send();
                            $action->halt();
                        }
                    }
                }),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    #[Override]
    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }
}