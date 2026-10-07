<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\BorrowingStatus;
use App\Enums\UserRole;
use App\Models\BorrowingDetail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class BorrowedBooksWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getStats(): array
    {
        $count = BorrowingDetail::whereHas('borrowing', fn($q) => $q->where('status', BorrowingStatus::Borrowed))->count();

        return [
            Stat::make('Buku Sedang Dipinjam', $count)
                ->description('Eksamplar yang sedang dipinjam')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('warning')
        ];
    }
}
