<?php

namespace App\Filament\Widgets\Librarian;

use App\Enums\FineType;
use App\Enums\UserRole;
use App\Models\Fine;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Override;

class FineDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Jenis Denda';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '200px';

    #[Override]
    public static function canView(): bool
    {
        return Auth::user()?->role === UserRole::Pustakawan;
    }

    protected function getData(): array
    {
        $data = Fine::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return [
            'datasets' => [
                [
                    'data' => [
                        $data->get(FineType::Late->value) ?? 0,
                        $data->get(FineType::Damaged->value) ?? 0,
                        $data->get(FineType::Lost->value) ?? 0
                    ],
                    'backgroundColor' => ['#f59e0b', '#f97316', '#ef4444']
                ]
            ],
            'labels' => ['Keterlambatan', 'Kerusakan', 'Kehilangan']
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
