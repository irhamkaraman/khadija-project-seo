<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ClickLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyClicksChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Grafik Klik Harian';

    protected function getData(): array
    {
        // Get last 7 days clicks
        $data = ClickLog::select('clicked_date', DB::raw('SUM(clicks) as total_clicks'))
            ->where('clicked_date', '>=', Carbon::now()->subDays(6)->toDateString())
            ->groupBy('clicked_date')
            ->orderBy('clicked_date', 'asc')
            ->get();

        // Fill empty days
        $labels = [];
        $clicks = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->toDateString();
            $labels[] = Carbon::parse($dateStr)->format('d M');
            $record = $data->firstWhere('clicked_date', $dateStr);
            $clicks[] = $record ? (int) $record->total_clicks : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Klik',
                    'data' => $clicks,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
