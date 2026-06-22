<?php

namespace App\Livewire\Pages\Analytics;

use App\Services\Analytics\AnalyticsRepository;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Analytics')]
class Dashboard extends Component
{
    public function render(AnalyticsRepository $analytics)
    {
        $since = Carbon::now()->subDays(30)->startOfDay();

        return view('livewire.pages.analytics.dashboard', [
            'totalsAllTime' => $analytics->totals(),
            'totalsLast30Days' => $analytics->totals($since),
            'pageStats' => $analytics->pageStats(),
            'pageStatsLast30Days' => $analytics->pageStats($since),
            'blogPostStats' => $analytics->blogPostStats(),
            'blogPostStatsLast30Days' => $analytics->blogPostStats($since),
            'dailyTrend' => $analytics->dailyTrend(),
        ]);
    }
}
