<?php

use App\Services\Projects\ProjectRepository;

test('project repository returns configured projects', function () {
    $projects = (new ProjectRepository)->all();

    expect($projects)->not->toBeEmpty()
        ->and($projects[0]['name'])->toBe('Button+ Node-RED');
});

test('project repository filters projects by tag', function () {
    $projects = (new ProjectRepository)->filtered('Python');

    expect(array_column($projects, 'name'))->toBe(['HA PSA Car Controller', 'APsystems ECU for Home Assistant']);
});

test('project repository returns unique sorted tags', function () {
    $tags = (new ProjectRepository)->tags();

    expect($tags)->toBe(['Button+', 'Energy', 'Flux UI', 'HACS', 'Home Assistant', 'JavaScript', 'Laravel', 'Livewire', 'MCP', 'MQTT', 'Modbus', 'Node-RED', 'Python', 'Smarthome']);
});
