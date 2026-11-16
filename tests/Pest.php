<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->beforeEach(fn () => Http::preventStrayRequests())
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Fakes the GitHub API with the public repositories in config/site.php and merged pull requests in livewire/livewire,
 * Koenkk/zigbee-herdsman-converters, the fork someone/zigbee-herdsman-converters and Eagle-SCM/Website. Entries in $responses replace or add URL patterns.
 *
 * @param  array<string, mixed>  $responses
 */
function fakeGitHub(array $responses = []): void
{
    $repository = fn (string $fullName, int $stars, string $pushedAt, bool $isPrivate = false, ?string $description = null, bool $isFork = false): array => [
        'full_name' => $fullName,
        'html_url' => "https://github.com/{$fullName}",
        'description' => $description,
        'stargazers_count' => $stars,
        'pushed_at' => $pushedAt,
        'private' => $isPrivate,
        'fork' => $isFork,
    ];

    $pullRequest = fn (string $repository, int $number, string $title, string $mergedAt): array => [
        'title' => $title,
        'html_url' => "https://github.com/{$repository}/pull/{$number}",
        'repository_url' => "https://api.github.com/repos/{$repository}",
        'pull_request' => ['merged_at' => $mergedAt],
    ];

    // Same keys replace the defaults in place; new keys are added at the end.
    Http::fake([
        'api.github.com/repos/nielseulink/buttonplus-nodered/releases/latest' => Http::response(['tag_name' => 'v1.0.6']),
        'api.github.com/repos/nielseulink/ha_psa_controller/releases/latest' => Http::response(['tag_name' => 'v0.1.4']),
        'api.github.com/repos/nielseulink/ha_apsystems_ecu_modbus/releases/latest' => Http::response(['message' => 'Not Found'], 404),
        'api.github.com/repos/nielseulink/buttonplus-nodered' => Http::response($repository('nielseulink/buttonplus-nodered', 4, '2026-06-19T12:51:51Z')),
        'api.github.com/repos/nielseulink/ha_psa_controller' => Http::response($repository('nielseulink/ha_psa_controller', 7, '2026-08-26T15:17:07Z')),
        'api.github.com/repos/nielseulink/ha_apsystems_ecu_modbus' => Http::response($repository('nielseulink/ha_apsystems_ecu_modbus', 0, '2026-06-24T12:56:17Z')),
        'api.github.com/repos/livewire/livewire' => Http::response($repository('livewire/livewire', 23000, '2026-11-16T10:00:00Z', description: 'A full-stack framework for Laravel.')),
        'api.github.com/repos/Koenkk/zigbee-herdsman-converters' => Http::response($repository('Koenkk/zigbee-herdsman-converters', 1000, '2026-11-16T10:00:00Z', description: 'Device converters for zigbee-herdsman.')),
        'api.github.com/repos/someone/zigbee-herdsman-converters' => Http::response($repository('someone/zigbee-herdsman-converters', 0, '2026-11-16T10:00:00Z', isFork: true)),
        'api.github.com/search/issues*' => Http::response(['items' => [
            $pullRequest('someone/zigbee-herdsman-converters', 1, 'Work on the branch of someone else', '2026-11-14T10:00:00Z'),
            $pullRequest('Eagle-SCM/Website', 20, 'Business work that stays off the site', '2026-11-13T14:30:53Z'),
            $pullRequest('livewire/livewire', 8490, 'Cleanup render methods in new form object tests', '2024-05-28T10:00:00Z'),
            $pullRequest('livewire/livewire', 8481, 'Added ->pull() and ->resetExcept() support to the form object', '2024-05-23T10:00:00Z'),
            $pullRequest('Koenkk/zigbee-herdsman-converters', 3700, 'Add TS0601 blinds from _TZE200_hsgrhjpf', '2022-01-19T10:00:00Z'),
        ]]),
        ...$responses,
    ]);
}
