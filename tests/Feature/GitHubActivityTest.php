<?php

use App\Services\GitHub\GitHubActivity;
use Illuminate\Support\Facades\Http;

test('projects page shows github stats for public repositories', function () {
    fakeGitHub();

    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertSee('v1.0.6')
        ->assertSee('v0.1.4')
        ->assertSee('APsystems ECU for Home Assistant')
        ->assertSee('Updated');
});

test('projects that are not open source yet have no link', function () {
    fakeGitHub();

    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertSee('Deelwaarder')
        ->assertSee('Mealplanner')
        ->assertSee('Open source soon')
        ->assertDontSee('github.com/nielseulink/deelwaarder')
        ->assertDontSee('github.com/nielseulink/mealplanner');

    Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'deelwaarder')
        || str_contains($request->url(), 'mealplanner'));
});

test('projects page lists merged contributions grouped per repository', function () {
    fakeGitHub();

    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertSee('Open source contributions')
        ->assertSeeInOrder(['livewire/livewire', 'Koenkk/zigbee-herdsman-converters'])
        ->assertSee('Added -&gt;pull() and -&gt;resetExcept() support to the form object', false)
        ->assertSee('Add TS0601 blinds from _TZE200_hsgrhjpf')
        ->assertSee('2 merged pull requests')
        ->assertSee('1 merged pull request')
        ->assertDontSee('Eagle-SCM')
        ->assertDontSee('Business work that stays off the site');

    Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'repos/Eagle-SCM'));
});

test('contributions to forks are left out', function () {
    fakeGitHub();

    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertDontSee('someone/zigbee-herdsman-converters')
        ->assertDontSee('Work on the branch of someone else');
});

test('contributions to private repositories are left out', function () {
    fakeGitHub([
        'api.github.com/repos/Koenkk/zigbee-herdsman-converters' => Http::response([
            'full_name' => 'Koenkk/zigbee-herdsman-converters',
            'html_url' => 'https://github.com/Koenkk/zigbee-herdsman-converters',
            'description' => null,
            'stargazers_count' => 1,
            'pushed_at' => '2026-11-16T10:00:00Z',
            'private' => true,
        ]),
    ]);

    $repositories = array_column(app(GitHubActivity::class)->contributions(), 'repository');

    expect($repositories)->toBe(['livewire/livewire']);
});

test('home page shows the most recent github activity', function () {
    fakeGitHub();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Recent on GitHub')
        ->assertSeeInOrder(['HA PSA Car Controller', 'APsystems ECU for Home Assistant', 'Button+ Node-RED'])
        ->assertDontSee('livewire/livewire');
});

test('github results are cached between requests', function () {
    fakeGitHub();

    $this->get(route('projects'))->assertSuccessful();
    $sentAfterFirstRequest = count(Http::recorded());

    $this->get(route('projects'))->assertSuccessful();
    $this->get(route('home'))->assertSuccessful();

    expect($sentAfterFirstRequest)->toBeGreaterThan(0)
        ->and(Http::recorded())->toHaveCount($sentAfterFirstRequest);
});

test('pages still render when github is unavailable', function (string $failure) {
    Http::fake(['api.github.com/*' => $failure === 'connection' ? Http::failedConnection() : Http::response(null, 503)]);

    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertSee('Button+ Node-RED')
        ->assertDontSee('Open source contributions');

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('Recent on GitHub');
})->with(['connection', 'server error']);

test('a failed github request is not retried on every page view', function () {
    Http::fake(['api.github.com/*' => Http::response(null, 503)]);

    $this->get(route('projects'))->assertSuccessful();
    $sentAfterFirstRequest = count(Http::recorded());

    $this->get(route('projects'))->assertSuccessful();

    expect(Http::recorded())->toHaveCount($sentAfterFirstRequest);
});
