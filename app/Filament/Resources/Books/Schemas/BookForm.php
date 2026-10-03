<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Informasi Buku")
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('author')
                            ->label('Penulis')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('publisher')
                            ->label('Penerbit')
                            ->maxLength(255),

                        TextInput::make('publication_year')
                            ->label('Tahun Terbit')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y')),

                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(20),

                        TextInput::make('price')
                            ->label('Harga Buku')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0),
                    ]),

                Section::make('Kategori & Rak')
                    ->columns(1)
                    ->schema([
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->withTrashed()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('rack_id')
                            ->label('Rak')
                            ->relationship(
                                name: 'rack',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->withTrashed()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Section::make('Stok')
                    ->columns(1)
                    ->description('Stok hanya dapat diubah saat membuat buku baru. Untuk edit, gunakan field penyesuaian stok.')
                    ->schema([
                        TextInput::make('stock')
                            ->label('Stok Total')
                            ->integer()
                            ->required()
                            ->minValue(0)
                            ->visibleOn('create'),

                        TextInput::make('available_stock')
                            ->label('Stok Tersedia')
                            ->integer()
                            ->required()
                            ->minValue(0)
                            ->visibleOn('create'),

                        //  Field khusus edit: penyesuaian stok (+/-)
                        TextInput::make('stock_adjustment')
                            ->label('Penyesuaian Stock')
                            ->integer()
                            ->default(0)
                            ->helperText(fn($record) => "Stok saat ini: {$record?->stock} | Tersedia: {$record?->available_stock}")
                            ->visibleOn('edit')
                    ])
            ]);
    }
}