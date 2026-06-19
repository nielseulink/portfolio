<div class="space-y-8">
    <section class="space-y-3">
        <flux:heading size="xl" level="1">Blog</flux:heading>
        <flux:text class="max-w-3xl text-base text-zinc-600 dark:text-zinc-400">
            Writings about topics I'm working on, learning about or just finding interesting.
        </flux:text>
    </section>

    @if (! $hasPosts)
        <flux:callout icon="information-circle" variant="secondary">
            <flux:callout.heading>No posts yet</flux:callout.heading>
            <flux:callout.text>Add .md files to content/blog to show posts here.</flux:callout.text>
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
                        wire:click="setTag('{{ $availableTag }}')"
                        :variant="$tag === $availableTag ? 'primary' : 'ghost'"
                        size="sm"
                    >
                        {{ $availableTag }}
                    </flux:button>
                @endforeach
            </div>
        @endif

        @if ($posts === [])
            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.heading>No posts found</flux:callout.heading>
                <flux:callout.text>Try another tag or view all posts.</flux:callout.text>
            </flux:callout>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($posts as $post)
                    <flux:card class="flex h-full flex-col gap-4" wire:key="post-{{ $post->slug }}">
                        <flux:heading size="lg" level="3">{{ $post->title }}</flux:heading>

                        @if ($post->tags !== [])
                            <div class="flex flex-wrap gap-2">
                                @foreach ($post->tags as $postTag)
                                    <flux:badge
                                        as="button"
                                        wire:click="setTag('{{ $postTag }}')"
                                        size="sm"
                                        :color="$tag === $postTag ? 'zinc' : null"
                                    >
                                        {{ $postTag }}
                                    </flux:badge>
                                @endforeach
                            </div>
                        @endif

                        <flux:text class="grow text-base leading-relaxed text-zinc-800 dark:text-zinc-200">
                            {{ $post->excerpt }}
                        </flux:text>

                        <flux:button
                            :href="route('blog.show', $post->slug)"
                            wire:navigate
                            variant="primary"
                            size="sm"
                            class="self-start"
                        >
                            Read more
                        </flux:button>
                    </flux:card>
                @endforeach
            </div>
        @endif
    @endif
</div>
