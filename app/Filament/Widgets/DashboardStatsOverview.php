<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Post;
use App\Models\AffiliateLink;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Postingan', Post::count())
                ->description('Total artikel di blog')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Total Link Afiliasi', AffiliateLink::count())
                ->description('Total link afiliasi aktif')
                ->descriptionIcon('heroicon-m-link')
                ->color('success'),
            Stat::make('Total Klik Keseluruhan', (int) AffiliateLink::sum('click_count'))
                ->description('Total klik dari semua link')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('warning'),
        ];
    }
}
