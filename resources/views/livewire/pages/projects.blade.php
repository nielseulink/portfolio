<div class="space-y-8">
    <section class="space-y-3">
        <flux:heading size="xl" level="1">Projects</flux:heading>
        <flux:text class="max-w-3xl text-base text-zinc-600 dark:text-zinc-400">
            Open source and side projects I have worked on.
        </flux:text>
    </section>

    @if (! $hasProjects)
        <flux:callout icon="information-circle" variant="secondary">
            <flux:callout.heading>No projects yet</flux:callout.heading>
            <flux:callout.text>Add projects to config/site.php to show them here.</flux:callout.text>
        </flux:callout>
    @else
        @if ($tags !== [])
            <div class="flex flex-wrap items-center gap-2">
                <flux:button
                    wire:click="clearTag"
                    :variant="$tag === null ? 'primary' : 'ghost'"
                    size="sm"
                >
                    All
                </flux:button>

                @foreach ($tags as $availableTag)
                    <flux:button
                        wire:click="setTag({{ \Illuminate\Support\Js::from($availableTag) }})"
                        :variant="$tag === $availableTag ? 'primary' : 'ghost'"
                        size="sm"
                    >
                        {{ $availableTag }}
                    </flux:button>
                @endforeach
            </div>
        @endif

        @if ($projects === [])
            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.heading>No projects found</flux:callout.heading>
                <flux:callout.text>Try another tag or view all projects.</flux:callout.text>
            </flux:callout>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    @php($stats = isset($project['repository']) ? ($projectStats[$project['repository']] ?? null) : null)

                    <flux:card class="flex h-full flex-col gap-4" wire:key="project-{{ $project['name'] }}">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <flux:heading size="lg" level="3">{{ $project['name'] }}</flux:heading>

                            @if (($project['status'] ?? null) === 'coming-soon')
                                <flux:badge size="sm" color="amber">Open source soon</flux:badge>
                            @endif
                        </div>

                        @if (! empty($project['tags']))
                            <div class="flex flex-wrap gap-2">
                                @foreach ($project['tags'] as $projectTag)
                                    <flux:badge
                                        as="button"
                                        wire:click="setTag({{ \Illuminate\Support\Js::from($projectTag) }})"
                                        size="sm"
                                        :color="$tag === $projectTag ? 'zinc' : null"
                                    >
                                        {{ $projectTag }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        @endif

                        <flux:text class="grow text-base leading-relaxed text-zinc-800 dark:text-zinc-200">
                            {{ $project['description'] }}
                        </flux:text>

                        @if ($stats !== null)
                            <x-github-stats :stars="$stats->stars" :release="$stats->latestRelease" label="Updated" :at="$stats->pushedAt" />
                        @endif

                        @isset($project['url'])
                            <flux:button
                                :href="$project['url']"
                                target="_blank"
                                rel="noopener noreferrer"
                                variant="primary"
                                size="sm"
                                class="self-start"
                            >
                                {{ $project['link_label'] ?? 'View project' }}
                            </flux:button>
                        @endisset
                    </flux:card>
                @endforeach
            </div>
        @endif
    @endif

    @if ($contributions !== [])
        <section class="space-y-6">
            <div class="space-y-3">
                <flux:heading size="lg" level="2">Open source contributions</flux:heading>
                <flux:text class="max-w-3xl text-base text-zinc-600 dark:text-zinc-400">
                    Pull requests of mine that were merged into other open source projects.
                </flux:text>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($contributions as $contribution)
                    <flux:card class="flex h-full flex-col gap-4" wire:key="contribution-{{ $contribution->repository }}">
                        <flux:heading size="lg" level="3">
                            <flux:link :href="$contribution->url" target="_blank" rel="noopener noreferrer" variant="ghost">
                                {{ $contribution->repository }}
                            </flux:link>
                        </flux:heading>

                        @if ($contribution->description)
                            <flux:text class="text-base leading-relaxed text-zinc-800 dark:text-zinc-200">
                                {{ $contribution->description }}
                            </flux:text>
                        @endif

                        <ul class="grow space-y-2">
                            @foreach (array_slice($contribution->pullRequests, 0, 3) as $pullRequest)
                                <li class="text-sm">
                                    <flux:link :href="$pullRequest->url" target="_blank" rel="noopener noreferrer">
                                        {{ $pullRequest->title }}
                                    </flux:link>
                                    <span class="text-zinc-500 dark:text-zinc-400">· {{ $pullRequest->mergedAt->format('M Y') }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <x-github-stats :stars="$contribution->stars" />

                            <flux:link
                                :href="$contribution->url.'/pulls?q='.urlencode('is:pr is:merged author:'.$githubUsername)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm"
                            >
                                {{ trans_choice(':count merged pull request|:count merged pull requests', count($contribution->pullRequests)) }}
                            </flux:link>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </section>
    @endif
</div>
