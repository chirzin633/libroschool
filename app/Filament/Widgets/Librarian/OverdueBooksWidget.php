<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\BorrowingStatus;
use App\Enums\UserRole;
use App\Models\Borrowing;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class OverdueBooksWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }


    protected function getStats(): array
    {
        $count = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->where('due_at', '<', now()->toDateString())->count();

        return [
            Stat::make('Buku Terlambat', $count)
                ->description('Transaksi melewati jatuh tempo')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
        ];
    }
}
