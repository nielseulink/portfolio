<?php

return [

    'enabled' => env('ANALYTICS_ENABLED', true),

    'session_key' => 'analytics.visitor_key',

    'visitor_key_ttl_minutes' => (int) env('ANALYTICS_VISITOR_KEY_TTL_MINUTES', 60),

    /*
     * HTTP basic auth for /analytics. The dashboard returns 404 while either value is empty.
     */
    'dashboard' => [
        'username' => env('ANALYTICS_DASHBOARD_USERNAME'),
        'password' => env('ANALYTICS_DASHBOARD_PASSWORD'),
    ],

    'tracked_routes' => [
        'home',
        'projects',
        'blog.index',
        'blog.show',
    ],

    'bot_user_agents' => [
        'bot',
        'spider',
        'crawl',
        'slurp',
        'mediapartners',
        'facebookexternalhit',
        'pingdom',
        'uptimerobot',
        'headless',
        'lighthouse',
    ],

];
