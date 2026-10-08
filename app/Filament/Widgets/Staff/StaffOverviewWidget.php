<?php

namespace App\Filament\Widgets\Staff;

use App\Enums\BorrowingStatus;
use App\Enums\UserRole;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class StaffOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Staff;
    }

    protected function getStats(): array
    {
        $availableStock = Book::sum('available_stock');
        $totalTitles = Book::count();

        $borrowedCount = BorrowingDetail::whereHas('borrowing', fn($q) => $q->where('status', BorrowingStatus::Borrowed))->count();

        $overdueCount = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->where('due_at', '<', now()->toDateString())
            ->count();

        return [
            Stat::make('Stok Tersedia', $availableStock)
                ->description('Buku yang bisa dipinjam saat ini')
                ->icon('heroicon-o-book-open')
                ->color('primary'),

            Stat::make('Total Judul', $totalTitles)
                ->description('Judul dalam katalog')
                ->icon('heroicon-o-queue-list')
                ->color('info'),

            Stat::make('Sedang Dipinjam', $borrowedCount)
                ->description('Belum dikembalikan')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('warning'),

            Stat::make('Terlambat', $overdueCount)
                ->description('Melewati jatuh tempo')
                ->icon('heroicon-o-clock')
                ->color('danger')
        ];
    }
}
