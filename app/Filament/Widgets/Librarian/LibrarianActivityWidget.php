<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\BorrowingStatus;
use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class LibrarianActivityWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getStats(): array
    {
        $borrowedCount = BorrowingDetail::whereHas('borrowing', fn($q) => $q->where('status', BorrowingStatus::Borrowed))->count();

        $overdueCount = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->where('due_at', '<', now()->toDateString())->count();

        return [
            Stat::make('Buku Sedang Dipinjam', $borrowedCount)
                ->description('Eksemplar yang sedang dipinjam')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('warning'),

            Stat::make('Buku Terlambat', $overdueCount)
                ->description('Transaksi melewati jatuh tempo')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
        ];
    }
}
