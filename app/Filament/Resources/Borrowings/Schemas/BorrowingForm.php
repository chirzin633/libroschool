<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use App\Models\Book;
use App\Services\SettingService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Peminjaman')
                    ->columns(1)
                    ->description('Kode transaksi, tanggal jatuh tempo, dan status akan dibuat otomatis.')
                    ->schema([
                        Select::make('member_id')
                            ->label('Anggota')
                            ->relationship('member', 'name')
                            ->searchable(['name', 'member_code', 'class_position'])
                            ->preload()
                            ->live()
                            ->helperText('Cari berdasarkan nama, kode anggota, atau kelas/jabatan.')
                            ->required(),

                        Select::make('book_ids')
                            ->label('Buku yang Dipinjam')
                            ->multiple()
                            ->options(fn() => Book::query()
                                ->where('available_stock', '>', 0)
                                ->orderBy('title')
                                ->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->minItems(1)
                            ->maxItems(SettingService::getInt('max_books_per_borrowing', 3))
                            ->helperText('Maksimal ' . SettingService::getInt('max_books_per_borrowing', 3) . ' buku per transaksi'),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
