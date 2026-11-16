<?php

namespace App\Services\GitHub;

use App\Data\GitHub\ActivityItem;
use App\Data\GitHub\Contribution;
use App\Data\GitHub\MergedPullRequest;
use App\Data\GitHub\RepositoryStats;
use App\Services\Projects\ProjectRepository;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * GitHub data for the site: stats of the projects in config/site.php and the pull requests I got merged elsewhere.
 * Pages never wait on GitHub longer than one request timeout: results stay cached for six hours and are refreshed
 * after the response for a day after that. When GitHub fails, the pages show the projects without stats.
 */
class GitHubActivity
{
    private const int FRESH_SECONDS = 6 * 60 * 60;

    private const int STALE_SECONDS = 30 * 60 * 60;

    private const int FAILURE_SECONDS = 15 * 60;

    public function __construct(
        private GitHubClient $client,
        private ProjectRepository $projects,
    ) {}

    public function repositoryStats(string $fullName): ?RepositoryStats
    {
        $repository = $this->remember("github.repository.{$fullName}", function () use ($fullName): array {
            $repository = $this->client->repository($fullName);

            if ($repository['is_private']) {
                return [];
            }

            return [...$repository, 'latest_release' => $this->client->latestReleaseTag($fullName)];
        });

        if ($repository === null || $repository === []) {
            return null;
        }

        return new RepositoryStats(
            fullName: $repository['full_name'],
            url: $repository['html_url'],
            description: $repository['description'],
            stars: $repository['stars'],
            pushedAt: Carbon::parse($repository['pushed_at']),
            latestRelease: $repository['latest_release'],
        );
    }

    /**
     * Stats of every configured project that names a public GitHub repository, keyed by that repository.
     *
     * @return array<string, RepositoryStats>
     */
    public function projectStats(): array
    {
        return collect($this->projects->all())
            ->pluck('repository')
            ->filter()
            ->mapWithKeys(fn (string $repository): array => [$repository => $this->repositoryStats($repository)])
            ->filter()
            ->all();
    }

    /**
     * Public repositories of others, forks excluded, with my merged pull requests, the most recent merge first.
     *
     * @return list<Contribution>
     */
    public function contributions(): array
    {
        $contributions = $this->remember('github.contributions', fn (): array => $this->fetchContributions()) ?? [];

        return array_map(fn (array $contribution): Contribution => new Contribution(
            repository: $contribution['repository'],
            url: $contribution['url'],
            description: $contribution['description'],
            stars: $contribution['stars'],
            pullRequests: array_map(fn (array $pullRequest): MergedPullRequest => new MergedPullRequest(
                title: $pullRequest['title'],
                url: $pullRequest['url'],
                mergedAt: Carbon::parse($pullRequest['merged_at']),
            ), $contribution['pull_requests']),
        ), $contributions);
    }

    /**
     * The most recent activity over my own projects and my contributions.
     *
     * @return list<ActivityItem>
     */
    public function recent(int $limit = 3): array
    {
        $projectItems = collect($this->projects->all())
            ->filter(fn (array $project): bool => isset($project['repository']))
            ->map(function (array $project): ?ActivityItem {
                $stats = $this->repositoryStats($project['repository']);

                if ($stats === null) {
                    return null;
                }

                return new ActivityItem(
                    name: $project['name'],
                    description: $project['description'],
                    url: $stats->url,
                    stars: $stats->stars,
                    activityLabel: 'Updated',
                    activityAt: $stats->pushedAt,
                );
            })
            ->filter();

        $contributionItems = collect($this->contributions())
            ->map(fn (Contribution $contribution): ActivityItem => new ActivityItem(
                name: $contribution->repository,
                description: $contribution->pullRequests[0]->title,
                url: $contribution->pullRequests[0]->url,
                stars: $contribution->stars,
                activityLabel: 'Contributed',
                activityAt: $contribution->lastMergedAt(),
            ));

        return $projectItems
            ->concat($contributionItems)
            ->sortByDesc(fn (ActivityItem $item): int => $item->activityAt->getTimestamp())
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return list<array{repository: string, url: string, description: ?string, stars: int, pull_requests: list<array{title: string, url: string, merged_at: string}>}>
     */
    private function fetchContributions(): array
    {
        $excludedOwners = array_map(strtolower(...), config('site.github.excluded_contribution_owners', []));

        return collect($this->client->mergedPullRequests(config('site.github.username')))
            ->reject(fn (array $pullRequest): bool => in_array(
                strtolower(str($pullRequest['repository'])->before('/')->toString()),
                $excludedOwners,
                true,
            ))
            ->groupBy('repository')
            ->map(function ($pullRequests, string $fullName): ?array {
                $repository = $this->client->repository($fullName);

                // Pull requests into someone's fork are work on a branch of theirs, not a contribution to the project.
                if ($repository['is_private'] || $repository['is_fork']) {
                    return null;
                }

                return [
                    'repository' => $repository['full_name'],
                    'url' => $repository['html_url'],
                    'description' => $repository['description'],
                    'stars' => $repository['stars'],
                    'pull_requests' => $pullRequests
                        ->map(fn (array $pullRequest): array => [
                            'title' => $pullRequest['title'],
                            'url' => $pullRequest['url'],
                            'merged_at' => $pullRequest['merged_at'],
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->filter()
            ->sortByDesc(fn (array $contribution): string => $contribution['pull_requests'][0]['merged_at'])
            ->values()
            ->all();
    }

    /**
     * @template TValue of array
     *
     * @param  Closure(): TValue  $fetch
     * @return TValue|null
     */
    private function remember(string $key, Closure $fetch): ?array
    {
        if (Cache::has("{$key}.failed")) {
            return null;
        }

        try {
            return Cache::flexible($key, [self::FRESH_SECONDS, self::STALE_SECONDS], $fetch);
        } catch (ConnectionException|RequestException $exception) {
            report($exception);
            Cache::put("{$key}.failed", true, self::FAILURE_SECONDS);

            return null;
        }
    }
}
