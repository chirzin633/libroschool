<?php

namespace App\Filament\Resources\Fines\Tables;

use App\Enums\FineStatus;
use App\Enums\FineType;
use App\Models\Fine;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bookReturn.borrowingDetail.borrowing.borrowing_code')
                    ->label('Kode Transaksi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bookReturn.borrowingDetail.borrowing.member.name')
                    ->label('Anggota')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bookReturn.borrowingDetail.book.title')
                    ->label('Buku')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('type')
                    ->label('Jenis Denda')
                    ->badge()
                    ->color(fn(FineType $state) => match ($state) {
                        FineType::Late => 'warning',
                        FineType::Damaged => 'orange',
                        FineType::Lost => 'danger'
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(FineStatus $state) => match ($state) {
                        FineStatus::Unpaid => 'danger',
                        FineStatus::Paid => 'success'
                    }),

                TextColumn::make('paid_at')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('paidByUser.name')
                    ->label('Petugas Bayar')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(FineStatus::class),

                SelectFilter::make('type')
                    ->label('Jenis Denda')
                    ->options(FineType::class)
            ])
            ->recordActions([
                Action::make('pay')
                    ->label('Bayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Fine $record) => $record->status === FineStatus::Unpaid)
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription(fn(Fine $record) => "Konfirmasi pembayaran denda {$record->type->getLabel()} sebesar Rp" . number_format($record->amount, 0, ',', '.') . "?")
                    ->action(function (Fine $record) {
                        $record->update([
                            'status' => FineStatus::Paid,
                            'paid_at' => now(),
                            'paid_by' => Auth::id(),
                        ]);
                        Notification::make()
                            ->success()
                            ->title('Denda berhasil dibayar')
                            ->send();
                    }),
            ])
            ->defaultSort('crated_at', 'desc');
    }
}
