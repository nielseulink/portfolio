<?php

namespace App\Services\Projects;

class ProjectRepository
{
    /**
     * @return list<array{name: string, description: string, url?: string, repository?: string, status?: 'coming-soon', link_label?: string, tags: list<string>}>
     */
    public function all(): array
    {
        /** @var list<array{name: string, description: string, url?: string, repository?: string, status?: 'coming-soon', link_label?: string, tags: list<string>}> $projects */
        $projects = config('site.projects', []);

        return $projects;
    }

    /**
     * @return list<array{name: string, description: string, url?: string, repository?: string, status?: 'coming-soon', link_label?: string, tags: list<string>}>
     */
    public function filtered(?string $tag = null): array
    {
        if ($tag === null || $tag === '') {
            return $this->all();
        }

        return collect($this->all())
            ->filter(fn (array $project) => in_array($tag, $project['tags'] ?? [], true))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return collect($this->all())
            ->flatMap(fn (array $project) => $project['tags'] ?? [])
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
