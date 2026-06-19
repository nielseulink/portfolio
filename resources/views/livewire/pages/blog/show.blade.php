<x-slot:title>{{ $post->title }}</x-slot>

<article class="space-y-8">
    <header class="space-y-4">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item :href="route('blog.index')" wire:navigate>Blog</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $post->title }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="xl" level="1">{{ $post->title }}</flux:heading>

        <div class="flex flex-wrap items-center gap-3">
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $post->publishedAt->format('F j, Y') }}
            </flux:text>

            @foreach ($post->tags as $tag)
                <flux:badge size="sm">{{ $tag }}</flux:badge>
            @endforeach
        </div>
    </header>

    <div class="prose-blog">
        {!! $post->html !!}
    </div>
</article>
