<?php

namespace App\Filament\Resources\Racks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Rak')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Rak')
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: RK-001')
                            ->helperText('Kode unik untuk identifikasi rak.')
                            ->required(),

                        TextInput::make('name')
                            ->label('Nama Rak')
                            ->maxLength(255)
                            ->placeholder('Contoh: Fiksi Indonesia A-C')
                            ->required(),

                        TextInput::make('location')
                            ->label('Lokasi Fisik')
                            ->maxLength(255)
                            ->placeholder('Contoh: Lantai 2, Sayap Barat')
                    ])->columns(1),
            ]);
    }
}