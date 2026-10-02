<?php

namespace App\Filament\Resources\Members\Tables;

use App\Enums\MemberStatus;
use App\Enums\MemberType;
use App\Enums\UserRole;
use App\Models\Member;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Member::query()->withTrashed())
            ->columns([
                TextColumn::make('member_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->sortable(),

                TextColumn::make('class_position')
                    ->label('Kelas/Jabatan')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(MemberStatus $state) => match ($state) {
                        MemberStatus::Active => 'success',
                        MemberStatus::Inactive => 'danger'
                    })
                    ->sortable(),

                TextColumn::make('registered_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)

            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(MemberType::class),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(MemberStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn() => Auth::user()?->role === UserRole::Pustakawan),
                DeleteAction::make()
                    ->visible(fn() => Auth::user()?->role === UserRole::Pustakawan)
                    ->before(function (Member $record, DeleteAction $action) {
                        if ($record->hasActiveBorrowings()) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus')
                                ->body("Anggota \"{$record->name}\" masih memiliki peminjaman aktif.")
                                ->send();
                            $action->halt();
                        }
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => Auth::user()?->role === UserRole::Pustakawan)
                        ->before(function (Collection $records, DeleteBulkAction $action) {
                            $blocked = $records->filter(fn(Member $member) => $member->hasActiveBorrowings());

                            if ($blocked->isNotEmpty()) {
                                $names = $blocked->pluck('name')->join(', ');
                                Notification::make()
                                    ->danger()
                                    ->title('Beberapa anggota tidak dapat dihapus')
                                    ->body("Anggota berikut masih memiliki peminjaman aktif: {$names}")
                                    ->send();
                                $action->halt();
                            }
                        }),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(null);
    }
}