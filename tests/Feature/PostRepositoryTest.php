<?php

use App\Services\Blog\PostRepository;

test('post repository loads markdown posts sorted by date', function () {
    $posts = (new PostRepository(base_path('content/blog')))->all();

    expect($posts)->toHaveCount(2)
        ->and($posts[0]->slug)->toBe('home-assistant-automations')
        ->and($posts[1]->slug)->toBe('my-first-homelab-post');
});

test('post repository finds post by slug', function () {
    $post = (new PostRepository(base_path('content/blog')))->find('my-first-homelab-post');

    expect($post)->not->toBeNull()
        ->and($post->title)->toBe('My first homelab post');
});

test('post repository returns null for unknown slug', function () {
    $post = (new PostRepository(base_path('content/blog')))->find('unknown-post');

    expect($post)->toBeNull();
});

test('post repository filters posts by tag', function () {
    $posts = (new PostRepository(base_path('content/blog')))->filtered('smarthome');

    expect($posts)->toHaveCount(2);
});

test('post repository returns unique sorted tags', function () {
    $tags = (new PostRepository(base_path('content/blog')))->tags();

    expect($tags)->toBe(['home-assistant', 'homelab', 'smarthome']);
});
