<?php

namespace App\Filament\Resources\Racks;

use App\Enums\UserRole;
use App\Filament\Resources\Racks\Pages\CreateRack;
use App\Filament\Resources\Racks\Pages\EditRack;
use App\Filament\Resources\Racks\Pages\ListRacks;
use App\Filament\Resources\Racks\Schemas\RackForm;
use App\Filament\Resources\Racks\Tables\RacksTable;
use App\Models\Rack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Override;

class RackResource extends Resource
{
    protected static ?string $model = Rack::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArchiveBox;
    protected static ?string $navigationLabel = "Book Rack";
    protected static ?string $modelLabel = 'Rack';
    protected static ?int $navigationSort = 5;

    #[Override]
    public static function canAccess(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    public static function form(Schema $schema): Schema
    {
        return RackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RacksTable::configure($table);
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
            'index' => ListRacks::route('/'),
            'create' => CreateRack::route('/create'),
            'edit' => EditRack::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}