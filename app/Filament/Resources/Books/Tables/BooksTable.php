<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Book;
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
use Illuminate\Database\Eloquent\Builder;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Book::query()->withTrashed())
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->limit(40)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('author')
                    ->label('Penulis')
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('rack.name')
                    ->label('Rak')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('stock')
                    ->label('Stok Total')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('available_stock')
                    ->label('Tersedia')
                    ->color(fn(int $state): string => $state <= 0 ? 'danger' : ($state <= 3 ? 'warning' : 'success'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->withTrashed()
                    ),

                SelectFilter::make('rack_id')
                    ->label('Rak')
                    ->relationship(
                        name: 'rack',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->withTrashed()
                    ),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Book $record, DeleteAction $action) {
                        $hasActiveBorrowing = $record->borrowingDetails()
                            ->whereHas('borrowing', fn($q) => $q->where('status', 'Borrowed'))
                            ->exists();

                        if ($hasActiveBorrowing) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus')
                                ->body("Buku \"{$record->title}\" masih memiliki peminjaman aktif.")
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