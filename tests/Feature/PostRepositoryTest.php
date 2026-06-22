<?php

use App\Services\Blog\PostRepository;

test('post repository loads markdown posts sorted by date', function () {
    $posts = (new PostRepository(base_path('content/blog')))->all();

    expect($posts)->toHaveCount(1)
        ->and($posts[0]->slug)->toBe('buttonplus-nodered-evcc-energy-flow');
});

test('post repository finds post by slug', function () {
    $post = (new PostRepository(base_path('content/blog')))->find('buttonplus-nodered-evcc-energy-flow');

    expect($post)->not->toBeNull()
        ->and($post->title)->toBe('Building a Node-RED module for the Button+');
});

test('post repository returns null for unknown slug', function () {
    $post = (new PostRepository(base_path('content/blog')))->find('unknown-post');

    expect($post)->toBeNull();
});

test('post repository filters posts by tag', function () {
    $posts = (new PostRepository(base_path('content/blog')))->filtered('Node-RED');

    expect($posts)->toHaveCount(1);
});

test('post repository returns unique sorted tags', function () {
    $tags = (new PostRepository(base_path('content/blog')))->tags();

    expect($tags)->toBe(['Button+', 'EVCC', 'MQTT', 'Node-RED', 'Smarthome']);
});
