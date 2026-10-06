<?php

namespace App\Filament\Resources\Fines;

use App\Filament\Resources\Fines\Pages\CreateFine;
use App\Filament\Resources\Fines\Pages\EditFine;
use App\Filament\Resources\Fines\Pages\ListFines;
use App\Filament\Resources\Fines\Pages\ViewFine;
use App\Filament\Resources\Fines\Schemas\FineForm;
use App\Filament\Resources\Fines\Schemas\FineInfolist;
use App\Filament\Resources\Fines\Tables\FinesTable;
use App\Models\Fine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Override;

class FineResource extends Resource
{
    protected static ?string $model = Fine::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?string $modelLabel = "Fine";
    protected static ?string $navigationLabel = "Fine";
    protected static ?int $navigationSort = 7;

    #[Override]
    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public static function form(Schema $schema): Schema
    {
        return FineForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FineInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFines::route('/'),
            'create' => CreateFine::route('/create'),
            'view' => ViewFine::route('/{record}'),
            'edit' => EditFine::route('/{record}/edit'),
        ];
    }
}
