<?php

namespace App\Filament\Resources\Racks\Tables;

use App\Models\Rack;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RacksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Rack::query()->withTrashed())
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Rak')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nama Rak')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('books_count')
                    ->label('Jumlah Buku')
                    ->counts('books')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Rack $record, DeleteAction $action) {
                        $hasActiveBooks = $record->books()
                            ->whereHas('borrowingDetails.borrowing', fn($q) => $q->where('status', 'Borrowed'))
                            ->exists();

                        if ($hasActiveBooks) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus')
                                ->body("Rak \"{$record->name}\" masih memiliki buku dengan peminjaman aktif.")
                                ->send();
                            $action->halt();
                        }
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}