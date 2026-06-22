<?php

namespace App\Services\Analytics;

use App\Models\PageView;
use App\Services\Blog\PostRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsRepository
{
    public function __construct(
        private PostRepository $posts,
    ) {}

    /**
     * @return array{page_views: int, unique_visitors: int}
     */
    public function totals(?Carbon $since = null): array
    {
        $query = PageView::query();

        if ($since !== null) {
            $query->where('viewed_at', '>=', $since);
        }

        return [
            'page_views' => (int) (clone $query)->count(),
            'unique_visitors' => (int) (clone $query)->distinct('visitor_key')->count('visitor_key'),
        ];
    }

    /**
     * @return list<array{route_name: string, label: string, page_views: int, unique_visitors: int}>
     */
    public function pageStats(?Carbon $since = null): array
    {
        $query = PageView::query()
            ->select([
                'route_name',
                DB::raw('COUNT(*) as page_views'),
                DB::raw('COUNT(DISTINCT visitor_key) as unique_visitors'),
            ])
            ->whereIn('route_name', ['home', 'projects', 'blog.index'])
            ->groupBy('route_name');

        if ($since !== null) {
            $query->where('viewed_at', '>=', $since);
        }

        $labels = [
            'home' => 'Home',
            'projects' => 'Projects',
            'blog.index' => 'Blog index',
        ];

        return $query->get()
            ->map(fn (PageView $row) => [
                'route_name' => $row->route_name,
                'label' => $labels[$row->route_name] ?? $row->route_name,
                'page_views' => (int) $row->page_views,
                'unique_visitors' => (int) $row->unique_visitors,
            ])
            ->sortBy('label')
            ->values()
            ->all();
    }

    /**
     * @return list<array{slug: string, title: string, page_views: int, unique_visitors: int}>
     */
    public function blogPostStats(?Carbon $since = null): array
    {
        $query = PageView::query()
            ->select([
                'blog_slug',
                DB::raw('COUNT(*) as page_views'),
                DB::raw('COUNT(DISTINCT visitor_key) as unique_visitors'),
            ])
            ->where('route_name', 'blog.show')
            ->whereNotNull('blog_slug')
            ->groupBy('blog_slug');

        if ($since !== null) {
            $query->where('viewed_at', '>=', $since);
        }

        $titles = collect($this->posts->all())
            ->mapWithKeys(fn ($post) => [$post->slug => $post->title])
            ->all();

        return $query->get()
            ->map(fn (PageView $row) => [
                'slug' => (string) $row->blog_slug,
                'title' => $titles[$row->blog_slug] ?? (string) $row->blog_slug,
                'page_views' => (int) $row->page_views,
                'unique_visitors' => (int) $row->unique_visitors,
            ])
            ->sortByDesc('page_views')
            ->values()
            ->all();
    }

    /**
     * @return list<array{date: string, page_views: int, unique_visitors: int}>
     */
    public function dailyTrend(int $days = 30): array
    {
        $since = Carbon::now()->subDays($days - 1)->startOfDay();

        $rows = PageView::query()
            ->select([
                DB::raw('DATE(viewed_at) as view_date'),
                DB::raw('COUNT(*) as page_views'),
                DB::raw('COUNT(DISTINCT visitor_key) as unique_visitors'),
            ])
            ->where('viewed_at', '>=', $since)
            ->groupBy('view_date')
            ->orderBy('view_date')
            ->get()
            ->keyBy('view_date');

        $trend = [];

        for ($day = 0; $day < $days; $day++) {
            $date = $since->copy()->addDays($day)->toDateString();
            $row = $rows->get($date);

            $trend[] = [
                'date' => $date,
                'page_views' => (int) ($row->page_views ?? 0),
                'unique_visitors' => (int) ($row->unique_visitors ?? 0),
            ];
        }

        return $trend;
    }
}
