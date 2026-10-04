<?php

namespace App\Filament\Resources\Borrowings;

use App\Filament\Resources\Borrowings\Pages\CreateBorrowing;
use App\Filament\Resources\Borrowings\Pages\EditBorrowing;
use App\Filament\Resources\Borrowings\Pages\ListBorrowings;
use App\Filament\Resources\Borrowings\Pages\ViewBorrowing;
use App\Filament\Resources\Borrowings\Schemas\BorrowingForm;
use App\Filament\Resources\Borrowings\Tables\BorrowingsTable;
use App\Models\Borrowing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Override;

class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowRightCircle;
    protected static ?string $navigationLabel = "Borrowing";
    protected static ?string $modelLabel = "Borrowing";
    protected static ?int $navigationSort = 6;


    // Semua role bisa mengakses peminjaman (operasional harian)
    #[Override]
    public static function canAccess(): bool
    {
        return Auth::check();
    }

    public static function form(Schema $schema): Schema
    {
        return BorrowingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BorrowingsTable::configure($table);
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
            'index' => ListBorrowings::route('/'),
            'create' => CreateBorrowing::route('/create'),
            'edit' => EditBorrowing::route('/{record}/edit'),
            'view' => ViewBorrowing::route('/{record}'),
        ];
    }
}
