<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-zinc-900">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>Home</flux:navbar.item>
                <flux:navbar.item :href="route('blog.index')" :current="request()->routeIs('blog.*')" wire:navigate>Blog</flux:navbar.item>
                <flux:navbar.item :href="route('projects')" :current="request()->routeIs('projects')" wire:navigate>Projects</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-2 max-lg:hidden">
                <flux:navbar.item
                    icon="github"
                    :href="config('site.social.github')"
                    label="GitHub"
                    target="_blank"
                    rel="noopener noreferrer"
                />
                <flux:navbar.item
                    icon="linkedin"
                    :href="config('site.social.linkedin')"
                    label="LinkedIn"
                    target="_blank"
                    rel="noopener noreferrer"
                />
            </flux:navbar>
        </flux:header>

        <flux:sidebar sticky collapsible="mobile" class="border-r border-zinc-200 bg-zinc-50 lg:hidden dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    :href="route('home')"
                    :name="config('app.name')"
                    wire:navigate
                />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>Home</flux:sidebar.item>
                <flux:sidebar.item :href="route('blog.index')" :current="request()->routeIs('blog.*')" wire:navigate>Blog</flux:sidebar.item>
                <flux:sidebar.item :href="route('projects')" :current="request()->routeIs('projects')" wire:navigate>Projects</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item
                    icon="github"
                    :href="config('site.social.github')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    GitHub
                </flux:sidebar.item>
                <flux:sidebar.item
                    icon="linkedin"
                    :href="config('site.social.linkedin')"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    LinkedIn
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast />
        @endpersist

        @fluxScripts
    </body>
</html>
