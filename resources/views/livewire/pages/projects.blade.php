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
                    <flux:card class="flex h-full flex-col gap-4" wire:key="project-{{ $project['url'] }}">
                        <flux:heading size="lg" level="3">{{ $project['name'] }}</flux:heading>

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
                    </flux:card>
                @endforeach
            </div>
        @endif
    @endif
</div>
