<div class="space-y-10">
    <section class="flex flex-col items-center gap-8 sm:flex-row sm:items-center">
        <img
            src="{{ asset('images/niels_final_2.jpg') }}"
            alt="Niels Eulink"
            class="size-32 shrink-0 rounded-full border border-zinc-200 object-cover shadow-sm sm:size-40 dark:border-zinc-700"
        />

        <div class="space-y-4 text-center sm:text-left">
            <flux:heading size="xl" level="1">Hello 👋</flux:heading>
            <flux:text class="max-w-3xl text-base text-zinc-600 dark:text-zinc-400">
                I'm Niels Eulink Eulink, a fullstack Laravel developer with a passion for smarthome, homelab, energy management and other hobby projects in and around the house.
            </flux:text>
        </div>
    </section>

    <section class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <flux:heading size="lg" level="2">Recent posts</flux:heading>
            <flux:button :href="route('blog.index')" wire:navigate variant="ghost" size="sm">
                All posts
            </flux:button>
        </div>

        @if ($latestPosts === [])
            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.heading>No posts yet</flux:callout.heading>
                <flux:callout.text>Add Markdown files to content/blog to publish your first post.</flux:callout.text>
            </flux:callout>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($latestPosts as $post)
                    <flux:card class="flex h-full flex-col gap-4">
                        <flux:heading size="lg" level="3">{{ $post->title }}</flux:heading>

                        @if ($post->tags !== [])
                            <div class="flex flex-wrap gap-2">
                                @foreach ($post->tags as $tag)
                                    <flux:badge size="sm">{{ $tag }}</flux:badge>
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
    </section>
</div>
