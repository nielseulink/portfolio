<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the analytics dashboard with HTTP basic auth against the credentials in config/analytics.php. Without
 * credentials configured the dashboard does not exist (404), so a missing env var never leaves it open.
 */
class AuthenticateAnalyticsDashboard
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = (string) config('analytics.dashboard.username');
        $password = (string) config('analytics.dashboard.password');

        if ($username === '' || $password === '') {
            abort(404);
        }

        $isAuthenticated = hash_equals($username, (string) $request->getUser())
            && hash_equals($password, (string) $request->getPassword());

        if (! $isAuthenticated) {
            return response('Unauthorized.', 401, ['WWW-Authenticate' => 'Basic realm="Analytics", charset="UTF-8"']);
        }

        return $next($request);
    }
}
