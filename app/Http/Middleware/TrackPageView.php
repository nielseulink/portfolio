<?php

namespace App\Http\Middleware;

use App\Services\Analytics\PageViewTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function __construct(
        private PageViewTracker $tracker,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->tracker->shouldTrack($request)) {
            return $response;
        }

        $this->tracker->record($request, $this->tracker->resolveVisitor($request));

        return $response;
    }
}
