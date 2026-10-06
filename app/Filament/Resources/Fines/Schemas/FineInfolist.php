<?php

namespace App\Filament\Resources\Fines\Schemas;

use App\Enums\FineStatus;
use App\Enums\FineType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FineInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Denda')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bookReturn.borrowingDetail.borrowing.borrowing_code')
                            ->label('Kode Transaksi'),

                        TextEntry::make('bookReturn.borrowingDetail.member.name')
                            ->label('Anggota'),

                        TextEntry::make('bookReturn.borrowingDetail.book.title')
                            ->label('Buku'),

                        TextEntry::make('type')
                            ->label('Jenis Denda')
                            ->badge()
                            ->color(fn(FineType $state) => match ($state) {
                                FineType::Late => 'warning',
                                FineType::Damaged => 'danger',
                                FineType::Lost => 'danger'
                            }),

                        TextEntry::make('amount')
                            ->label('Nominal')
                            ->money('IDR'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(FineStatus $state) => match ($state) {
                                FineStatus::Unpaid => 'danger',
                                FineStatus::Paid => 'success'
                            }),
                    ]),

                Section::make('Informasi Pembayaran')
                    ->visible(fn($record) => $record->status === FineStatus::Paid)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('paid_at')
                            ->label('Tanggal Pembayaran')
                            ->dateTime('d M Y'),

                        TextEntry::make('paidByUser.name')
                            ->label('Petugas Penerima')
                            ->placeholder('-')
                    ]),

                Section::make('Detail Pengembalian')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('bookReturn.returned_at')
                            ->label('Tanggal Kembali')
                            ->date('d M Y'),

                        TextEntry::make('bookreturn.condition')
                            ->label('Kondisi Buku')
                            ->badge(),

                        TextEntry::make('bookReturn.late_days')
                            ->label('Hari Terlambat')
                            ->suffix(' hari'),

                        TextEntry::make('bookReturn.notes')
                            ->label('Catatan Kondisi')
                            ->placeholder('-')
                            ->columnSpanFull()
                    ])
            ]);
    }
}
