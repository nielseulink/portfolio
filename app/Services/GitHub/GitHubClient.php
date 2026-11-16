<?php

namespace App\Services\GitHub;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the GitHub REST API. It returns plain arrays so the results can be cached without
 * unserializing objects; failed requests throw.
 */
class GitHubClient
{
    /**
     * @return array{full_name: string, html_url: string, description: ?string, stars: int, pushed_at: string, is_private: bool, is_fork: bool}
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function repository(string $fullName): array
    {
        $repository = $this->request()->get("repos/{$fullName}")->throw()->json();

        return [
            'full_name' => $repository['full_name'],
            'html_url' => $repository['html_url'],
            'description' => $repository['description'] ?? null,
            'stars' => (int) $repository['stargazers_count'],
            'pushed_at' => $repository['pushed_at'],
            'is_private' => (bool) $repository['private'],
            'is_fork' => (bool) ($repository['fork'] ?? false),
        ];
    }

    /**
     * The tag of the latest release, or null when the repository has no releases.
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function latestReleaseTag(string $fullName): ?string
    {
        $response = $this->request()->get("repos/{$fullName}/releases/latest");

        if ($response->notFound()) {
            return null;
        }

        return $response->throw()->json('tag_name');
    }

    /**
     * Merged pull requests by the author in repositories owned by someone else, newest first.
     *
     * @return list<array{repository: string, title: string, url: string, merged_at: string}>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function mergedPullRequests(string $author): array
    {
        $items = $this->request()
            ->get('search/issues', [
                'q' => "is:pr is:merged author:{$author} -user:{$author}",
                'sort' => 'updated',
                'order' => 'desc',
                'per_page' => 100,
            ])
            ->throw()
            ->json('items', []);

        return collect($items)
            ->filter(fn (array $item): bool => isset($item['pull_request']['merged_at']))
            ->map(fn (array $item): array => [
                'repository' => str($item['repository_url'])->after('/repos/')->toString(),
                'title' => $item['title'],
                'url' => $item['html_url'],
                'merged_at' => $item['pull_request']['merged_at'],
            ])
            ->sortByDesc('merged_at')
            ->values()
            ->all();
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl('https://api.github.com')
            ->acceptJson()
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => 'nielseu.link',
            ])
            ->when(
                config('services.github.token'),
                fn (PendingRequest $request, string $token): PendingRequest => $request->withToken($token),
            )
            ->connectTimeout(3)
            ->timeout(5);
    }
}
