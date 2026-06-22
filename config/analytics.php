<?php

return [

    'enabled' => env('ANALYTICS_ENABLED', true),

    'session_key' => 'analytics.visitor_key',

    'visitor_key_ttl_minutes' => (int) env('ANALYTICS_VISITOR_KEY_TTL_MINUTES', 60),

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
