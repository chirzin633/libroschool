<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\MemberStatus;
use App\Enums\UserRole;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Override;

class ActiveMemberWidget extends StatsOverviewWidget
{

    protected static ?int $sort = 1;

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Anggota Aktif', Member::where('status', MemberStatus::Active)->count())
                ->description('Anggota terdaftar & aktif')
                ->icon('heroicon-o-users')
                ->color('primary')
        ];
    }
}
