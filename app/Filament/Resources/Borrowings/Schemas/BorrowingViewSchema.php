<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use App\Enums\BorrowingStatus;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class BorrowingViewSchema
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make('Informasi Transaksi')
          ->columns(2)
          ->schema([
            TextInput::make('borrowing_code')
              ->label('Kode Transaksi')
              ->readOnly()
              ->dehydrated(false),

            Select::make('member_id')
              ->label('Anggota')
              ->relationship('member', 'name')
              ->disabled(),

            TextEntry::make('borrowed_at')
              ->label('Tanggal Pinjam')
              ->date('d F Y')
              ->color('gray'),

            TextEntry::make('due_at')
              ->label('Jatuh Tempo')
              ->date('d F Y')
              ->color(fn($record) => $record->status === BorrowingStatus::Borrowed && now()->gt($record->due_at) ? 'danger' : 'gray'),

            TextEntry::make('status')
              ->label('Status')
              ->state(fn($record) => $record->status?->getLabel() ?? '-')
              ->badge()
              ->color(fn($record) => match ($record->status) {
                BorrowingStatus::Borrowed => 'warning',
                BorrowingStatus::Returned => 'success',
                default => 'gray'
              })
              ->dehydrated(false),

            Select::make('created_by')
              ->label('Petugas Input')
              ->relationship('staff', 'name')
              ->disabled(),
          ]),

        Section::make('Daftar Buku')
          ->description(fn($record) => "Total {$record->details->count()} buku")
          ->schema(function ($record) {

            if (!$record || $record->details->isEmpty()) {
              return [
                TextEntry::make('no_books')
                  ->state('Tidak ada buku dalam transaksi ini')
                  ->color('gray'),
              ];
            }
            $components = [];

            foreach ($record->details as $index => $detail) {
              $book = $detail->book;
              $components[] = Section::make("Buku #" . ($index + 1))
                ->collapsible()
                ->collapsed($index > 0)
                ->columns(2)
                ->schema([
                  TextEntry::make("title_{$detail->id}")
                    ->label('Judul Buku')
                    ->state($book?->title ?? '-'),

                  TextEntry::make("author_{$detail->id}")
                    ->label('Penulis')
                    ->state($book?->author ?? '-'),

                  TextEntry::make("category_{$detail->id}")
                    ->label('Kategori')
                    ->state($book?->category?->name ?? '-'),

                  TextEntry::make("rack_{$detail->id}")
                    ->label('Rak')
                    ->state($book?->rack?->name ?? '-'),
                ]);
            }

            return $components;
          }),

        Section::make('Catatan')
          ->schema([
            Textarea::make('notes')
              ->label('Catatan Peminjaman')
              ->rows(3)
              ->readOnly()
              ->dehydrated(false),
          ])
          ->visible(fn($record) => filled($record->notes)),
      ]);
  }
}
