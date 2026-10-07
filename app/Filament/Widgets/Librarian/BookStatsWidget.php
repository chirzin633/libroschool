<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\UserRole;
use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class BookStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total Judul Buku', Book::count())
                ->description('Judul unik dalam katalog')
                ->icon('heroicon-o-book-open')
                ->color('info'),

            Stat::make('Total Stok Fisik', Book::sum('stock'))
                ->description('Seluruh eksemplar buku')
                ->icon('heroicon-o-archive-box')
                ->color('info')
        ];
    }
}
