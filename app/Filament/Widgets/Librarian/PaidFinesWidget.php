<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\FineStatus;
use App\Enums\UserRole;
use App\Models\Fine;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class PaidFinesWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getStats(): array
    {
        $todayPaid = Fine::where('status', FineStatus::Paid)
            ->whereDate('paid_at', today())
            ->sum('amount');

        $monthPaid = Fine::where('status', FineStatus::Paid)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');


        return [
            Stat::make('Denda Terbayar Hari Ini', 'Rp ' . number_format($todayPaid, 0, ',', '.'))
                ->description('Pemasukan denda hari ini')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Denda Terbayar Bulan Ini', 'Rp ' . number_format($monthPaid, 0, ',', '.'))
                ->description('Pemasukan denda bulan')
                ->icon('heroicon-o-calendar')
                ->color('success')
        ];
    }
}
