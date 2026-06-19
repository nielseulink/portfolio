<?php

use App\Services\Projects\ProjectRepository;

test('project repository returns configured projects', function () {
    $projects = (new ProjectRepository)->all();

    expect($projects)->not->toBeEmpty()
        ->and($projects[0]['name'])->toBe('Button+ Node-RED');
});

test('project repository filters projects by tag', function () {
    $projects = (new ProjectRepository)->filtered('Python');

    expect($projects)->toHaveCount(1)
        ->and($projects[0]['name'])->toBe('HA PSA Car Controller');
});

test('project repository returns unique sorted tags', function () {
    $tags = (new ProjectRepository)->tags();

    expect($tags)->toBe(['Button+', 'Flux UI', 'HACS', 'Home Assistant', 'JavaScript', 'Laravel', 'Livewire', 'MQTT', 'Node-RED', 'Python', 'Smarthome']);
});
