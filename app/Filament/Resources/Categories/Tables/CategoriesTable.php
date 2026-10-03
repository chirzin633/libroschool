<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
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

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Category::query()->withTrashed())
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('books_count')
                    ->label('Jumlah Buku')
                    ->counts('books')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('delete_at')
                    ->label('Dihapus')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Category $record, DeleteAction $action) {
                        $hasActiveBooks = $record->books()
                            ->whereHas('borrowingDetails.borrowing', fn($q) => $q->where('status', 'Borrowed'))
                            ->exists();

                        if ($hasActiveBooks) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus')
                                ->body("Kategori \"{$record->name}\" masih memiliki buku dengan peminjaman aktif.")
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