<div class="space-y-10">
    <div class="space-y-1">
        <flux:heading size="xl" level="1">Analytics</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">
            Page views and unique visitors for your portfolio.
        </flux:text>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <flux:card class="space-y-1">
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Page views (all time)</flux:text>
            <flux:heading size="lg">{{ number_format($totalsAllTime['page_views']) }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-1">
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Unique visitors (all time)</flux:text>
            <flux:heading size="lg">{{ number_format($totalsAllTime['unique_visitors']) }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-1">
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Page views (30 days)</flux:text>
            <flux:heading size="lg">{{ number_format($totalsLast30Days['page_views']) }}</flux:heading>
        </flux:card>

        <flux:card class="space-y-1">
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Unique visitors (30 days)</flux:text>
            <flux:heading size="lg">{{ number_format($totalsLast30Days['unique_visitors']) }}</flux:heading>
        </flux:card>
    </section>

    <section class="space-y-4">
        <flux:heading size="lg" level="2">Pages</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Page</flux:table.column>
                <flux:table.column align="end">Views (all time)</flux:table.column>
                <flux:table.column align="end">Unique (all time)</flux:table.column>
                <flux:table.column align="end">Views (30 days)</flux:table.column>
                <flux:table.column align="end">Unique (30 days)</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($pageStats as $page)
                    @php
                        $recent = collect($pageStatsLast30Days)->firstWhere('route_name', $page['route_name']);
                    @endphp
                    <flux:table.row>
                        <flux:table.cell>{{ $page['label'] }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($page['page_views']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($page['unique_visitors']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($recent['page_views'] ?? 0) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($recent['unique_visitors'] ?? 0) }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>

    <section class="space-y-4">
        <flux:heading size="lg" level="2">Blog posts</flux:heading>

        @if ($blogPostStats === [])
            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.text>No blog post views recorded yet.</flux:callout.text>
            </flux:callout>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Post</flux:table.column>
                    <flux:table.column align="end">Views (all time)</flux:table.column>
                    <flux:table.column align="end">Unique (all time)</flux:table.column>
                    <flux:table.column align="end">Views (30 days)</flux:table.column>
                    <flux:table.column align="end">Unique (30 days)</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($blogPostStats as $post)
                        @php
                            $recent = collect($blogPostStatsLast30Days)->firstWhere('slug', $post['slug']);
                        @endphp
                        <flux:table.row>
                            <flux:table.cell>{{ $post['title'] }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format($post['page_views']) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format($post['unique_visitors']) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format($recent['page_views'] ?? 0) }}</flux:table.cell>
                            <flux:table.cell align="end">{{ number_format($recent['unique_visitors'] ?? 0) }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </section>

    <section class="space-y-4">
        <flux:heading size="lg" level="2">Daily trend (30 days)</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column align="end">Page views</flux:table.column>
                <flux:table.column align="end">Unique visitors</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach (array_reverse($dailyTrend) as $day)
                    <flux:table.row>
                        <flux:table.cell>{{ \Illuminate\Support\Carbon::parse($day['date'])->format('M j, Y') }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($day['page_views']) }}</flux:table.cell>
                        <flux:table.cell align="end">{{ number_format($day['unique_visitors']) }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
