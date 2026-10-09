<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(User::query()->withTrashed())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(UserRole $state) => match ($state) {
                        UserRole::Pustakawan => 'primary',
                        UserRole::Staff => 'info'
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(UserRole::class),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (User $record, DeleteAction $action) {
                        $currentUser = Auth::user();

                        if ($currentUser->id === $record->id) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal mengapus')
                                ->body("Anda tidak dapat menghapus akun anda sendiri")
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
                                    ->body('Tidak dapat menghapus Pustakawan terakhir yang aktif. Sistem akan terkunci')
                                    ->send();
                                $action->halt();
                            }
                        }
                    })
            ])->defaultSort('name');
    }
}