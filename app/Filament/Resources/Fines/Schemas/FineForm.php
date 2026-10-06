<?php

namespace App\Filament\Resources\Fines\Schemas;

use App\Enums\FineStatus;
use App\Enums\FineType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('book_return_id')
                    ->relationship('bookReturn', 'id')
                    ->required(),
                Select::make('type')
                    ->options(FineType::class)
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(FineStatus::class)
                    ->default('Unpaid')
                    ->required(),
                DateTimePicker::make('paid_at'),
                TextInput::make('paid_by')
                    ->numeric(),
            ]);
    }
}
