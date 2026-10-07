<?php

namespace App\Filament\Widgets\Staff;

use App\Enums\BorrowingStatus;
use App\Enums\UserRole;
use App\Models\Borrowing;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class TodayOverdueWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Staff;
    }

    protected function getStats(): array
    {
        $count = Borrowing::where('status', BorrowingStatus::Borrowed)
            ->where('due_at', '<', now()->toDateString())
            ->count();

        return [
            Stat::make('Keterlambatan Aktif', $count)
                ->description('Transaksi melewati jatuh tempo')
                ->icon('heroicon-o-clock')
                ->color('danger')
        ];
    }
}
