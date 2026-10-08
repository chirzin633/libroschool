<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\UserRole;
use App\Models\Borrowing;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Override;

class BorrowingTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren peminjaman (30 Hari Terakhir';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getData(): array
    {
        $data = Borrowing::query()
            ->where('borrowed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(borrowed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->toDateString());
        $mapped = $data->keyBy('date');

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => $dates->map(fn($d) => $mapped->get($d)?->count ?? 0),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
