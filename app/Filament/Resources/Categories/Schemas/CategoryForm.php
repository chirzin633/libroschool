<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kategori')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
            ]);
    }
}