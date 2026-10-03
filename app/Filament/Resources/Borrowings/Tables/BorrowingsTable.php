<?php

namespace App\Filament\Resources\Borrowings\Tables;

use App\Enums\BorrowingStatus;
use App\Models\Borrowing;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BorrowingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('borrowing_code')
                    ->label('Kode Transaksi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('member.name')
                    ->label('Anggota')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('details.book.title')
                    ->label('Buku')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(3)
                    ->toggleable(),

                TextColumn::make('borrowed_at')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn(Borrowing $record) => $record->status === BorrowingStatus::Borrowed && now()->gt($record->due_at) ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn(BorrowingStatus $state) => match ($state) {
                        BorrowingStatus::Borrowed => 'warning',
                        BorrowingStatus::Returned => 'success'
                    }),

                TextColumn::make('staff.name')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BorrowingStatus::class)
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('borrowed_at', 'desc');
    }
}