<?php

namespace App\Filament\Widgets\Staff;

use App\Enums\UserRole;
use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class AvailableBooksWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Staff;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Stok Tersedia', Book::sum('available_stock'))
                ->description('Buku yang bisa dipinjam saat ini')
                ->icon('heroicon-o-book-open')
                ->color('primary'),

            Stat::make('Total Judul', Book::count())
                ->description('Judul dalam katalog')
                ->icon('heroicon-o-queue-list')
                ->color('info')
        ];
    }
}
