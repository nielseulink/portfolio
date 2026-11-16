@props([
    'stars',
    'release' => null,
    'label' => null,
    'at' => null,
])

<div {{ $attributes->class('flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400') }}>
    <span class="inline-flex items-center gap-1" title="GitHub stars">
        <flux:icon.star variant="micro" />
        {{ $stars }}
    </span>

    @if ($release)
        <span class="inline-flex items-center gap-1" title="Latest release">
            <flux:icon.tag variant="micro" />
            {{ $release }}
        </span>
    @endif

    @if ($label && $at)
        <span title="{{ $at->toDateString() }}">{{ $label }} {{ $at->diffForHumans() }}</span>
    @endif
</div>
