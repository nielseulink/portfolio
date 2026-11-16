<?php

use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['analytics.enabled' => true]);
});

test('home page view is tracked on full page load', function () {
    $this->get(route('home'))
        ->assertSuccessful();

    expect(PageView::query()->count())->toBe(1);

    $view = PageView::query()->first();

    expect($view->route_name)->toBe('home')
        ->and($view->path)->toBe('/')
        ->and($view->blog_slug)->toBeNull();
});

test('bot requests are not tracked', function () {
    $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')
        ->get(route('home'))
        ->assertSuccessful();

    expect(PageView::query()->count())->toBe(0);
});

test('same php session counts as one unique visitor', function () {
    $this->get(route('home'))->assertSuccessful();
    $this->get(route('projects'))->assertSuccessful();

    expect(PageView::query()->count())->toBe(2)
        ->and(PageView::query()->distinct('visitor_key')->count('visitor_key'))->toBe(1);
});

test('different php sessions count as unique visitors', function () {
    $this->get(route('home'))->assertSuccessful();

    $this->flushSession();

    $this->get(route('home'))->assertSuccessful();

    expect(PageView::query()->count())->toBe(2)
        ->and(PageView::query()->distinct('visitor_key')->count('visitor_key'))->toBe(2);
});

test('visitor key is kept within ttl', function () {
    $this->get(route('home'))->assertSuccessful();

    $this->travel(30)->minutes();

    $this->get(route('projects'))->assertSuccessful();

    expect(PageView::query()->count())->toBe(2)
        ->and(PageView::query()->distinct('visitor_key')->count('visitor_key'))->toBe(1);
});

test('visitor key expires after ttl', function () {
    $this->get(route('home'))->assertSuccessful();

    $this->travel(61)->minutes();

    $this->get(route('projects'))->assertSuccessful();

    expect(PageView::query()->count())->toBe(2)
        ->and(PageView::query()->distinct('visitor_key')->count('visitor_key'))->toBe(2);
});

test('blog show page stores blog slug', function () {
    $slug = 'buttonplus-nodered-evcc-energy-flow';

    $this->get(route('blog.show', $slug))
        ->assertSuccessful();

    $view = PageView::query()->first();

    expect($view)->not->toBeNull()
        ->and($view->route_name)->toBe('blog.show')
        ->and($view->blog_slug)->toBe($slug)
        ->and($view->path)->toBe('/blog/'.$slug);
});

test('analytics dashboard shows stats with the configured credentials', function () {
    config(['analytics.dashboard' => ['username' => 'niels', 'password' => 'secret']]);

    PageView::query()->create([
        'visitor_key' => (string) Str::uuid(),
        'route_name' => 'home',
        'path' => '/',
        'blog_slug' => null,
        'viewed_at' => now(),
    ]);

    $this->withBasicAuth('niels', 'secret')
        ->get(route('analytics'))
        ->assertSuccessful()
        ->assertSee('Analytics')
        ->assertSee('Page views (all time)')
        ->assertSee('1');
});

test('analytics dashboard asks for credentials', function (?string $username, ?string $password) {
    config(['analytics.dashboard' => ['username' => 'niels', 'password' => 'secret']]);

    $request = $username === null ? $this : $this->withBasicAuth($username, $password);

    $request->get(route('analytics'))
        ->assertUnauthorized()
        ->assertHeader('WWW-Authenticate')
        ->assertDontSee('Page views (all time)');
})->with([
    'no credentials' => [null, null],
    'wrong password' => ['niels', 'wrong'],
    'wrong username' => ['someone', 'secret'],
]);

test('analytics dashboard does not exist without configured credentials', function () {
    config(['analytics.dashboard' => ['username' => null, 'password' => null]]);

    $this->withBasicAuth('', '')
        ->get(route('analytics'))
        ->assertNotFound();
});

test('analytics dashboard is not tracked as a page view', function () {
    config(['analytics.dashboard' => ['username' => 'niels', 'password' => 'secret']]);

    $this->withBasicAuth('niels', 'secret')
        ->get(route('analytics'))
        ->assertSuccessful();

    expect(PageView::query()->count())->toBe(0);
});

test('public pages do not expose analytics stats', function () {
    PageView::query()->create([
        'visitor_key' => (string) Str::uuid(),
        'route_name' => 'home',
        'path' => '/',
        'blog_slug' => null,
        'viewed_at' => now(),
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('Unique visitors (all time)', false);
});
