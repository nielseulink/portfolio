<?php

namespace App\Services\Analytics;

use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PageViewTracker
{
    public function resolveVisitor(Request $request): string
    {
        $sessionKey = (string) config('analytics.session_key');
        $ttlMinutes = (int) config('analytics.visitor_key_ttl_minutes', 60);
        $stored = $request->session()->get($sessionKey);
        $visitorKey = $this->extractVisitorKey($stored);
        $expiresAt = $this->extractExpiresAt($stored);

        if (
            is_string($visitorKey)
            && Str::isUuid($visitorKey)
            && $expiresAt instanceof Carbon
            && $expiresAt->isFuture()
        ) {
            $this->storeVisitorKey($request, $sessionKey, $visitorKey, $ttlMinutes);

            return $visitorKey;
        }

        $visitorKey = (string) Str::uuid();
        $this->storeVisitorKey($request, $sessionKey, $visitorKey, $ttlMinutes);

        return $visitorKey;
    }

    public function shouldTrack(Request $request, ?string $routeName = null): bool
    {
        if (! config('analytics.enabled')) {
            return false;
        }

        $routeName ??= $request->route()?->getName();

        if ($routeName === null || $routeName === '') {
            return false;
        }

        if (! in_array($routeName, config('analytics.tracked_routes', []), true)) {
            return false;
        }

        return ! $this->isBot($request);
    }

    public function record(
        Request $request,
        string $visitorKey,
        ?string $routeName = null,
        ?string $path = null,
        ?string $blogSlug = null,
    ): void {
        if (! $this->shouldTrack($request, $routeName)) {
            return;
        }

        $routeName ??= (string) $request->route()?->getName();
        $path = $this->normalizePath($path ?? '/'.$request->path());

        if ($routeName === 'blog.show') {
            $blogSlug ??= $request->route('slug');

            if (! is_string($blogSlug) || $blogSlug === '') {
                return;
            }
        } else {
            $blogSlug = null;
        }

        PageView::query()->create([
            'visitor_key' => $visitorKey,
            'route_name' => $routeName,
            'path' => $path,
            'blog_slug' => $blogSlug,
            'viewed_at' => Carbon::now(),
        ]);
    }

    public function isBot(Request $request): bool
    {
        $userAgent = Str::lower((string) $request->userAgent());

        if ($userAgent === '') {
            return false;
        }

        foreach (config('analytics.bot_user_agents', []) as $pattern) {
            if (Str::contains($userAgent, Str::lower((string) $pattern))) {
                return true;
            }
        }

        return false;
    }

    private function normalizePath(string $path): string
    {
        $path = '/'.trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function extractVisitorKey(mixed $stored): ?string
    {
        if (is_array($stored)) {
            $key = $stored['key'] ?? null;

            return is_string($key) && Str::isUuid($key) ? $key : null;
        }

        if (is_string($stored) && Str::isUuid($stored)) {
            return $stored;
        }

        return null;
    }

    private function extractExpiresAt(mixed $stored): ?Carbon
    {
        if (! is_array($stored)) {
            return null;
        }

        $expiresAt = $stored['expires_at'] ?? null;

        if (! is_string($expiresAt) && ! $expiresAt instanceof Carbon) {
            return null;
        }

        return Carbon::parse($expiresAt);
    }

    private function storeVisitorKey(Request $request, string $sessionKey, string $visitorKey, int $ttlMinutes): void
    {
        $request->session()->put($sessionKey, [
            'key' => $visitorKey,
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes)->toIso8601String(),
        ]);
    }
}
