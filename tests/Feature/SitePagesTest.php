<?php

use App\Livewire\Pages\Blog\Index as BlogIndex;
use App\Livewire\Pages\Projects;
use Livewire\Livewire;

test('home page is accessible', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Hello')
        ->assertSee('Niels Eulink')
        ->assertSee('favicon.svg', false);
});

test('blog index page is accessible', function () {
    $this->get(route('blog.index'))
        ->assertSuccessful()
        ->assertSee('Blog');
});

test('blog index can filter posts by tag', function () {
    $this->get(route('blog.index', ['tag' => 'Node-RED']))
        ->assertSuccessful()
        ->assertSee('Building a Node-RED module for the Button+');
});

test('blog index shows all posts when tag filter is cleared', function () {
    Livewire::test(BlogIndex::class)
        ->call('setTag', 'Node-RED')
        ->assertSet('tag', 'Node-RED')
        ->call('clearTag')
        ->assertSet('tag', null);
});

test('blog show page displays a post', function () {
    $this->get(route('blog.show', 'buttonplus-nodered-evcc-energy-flow'))
        ->assertSuccessful()
        ->assertSee('Building a Node-RED module for the Button+')
        ->assertSee('Rendering larger SVG');
});

test('blog show page returns not found for unknown slug', function () {
    $this->get(route('blog.show', 'unknown-post'))
        ->assertNotFound();
});

test('projects page is accessible', function () {
    $this->get(route('projects'))
        ->assertSuccessful()
        ->assertSee('Button+ Node-RED')
        ->assertSee('HA PSA Car Controller')
        ->assertSee('View on GitHub')
        ->assertSee('Visit website')
        ->assertSee('github.com/nielseulink/buttonplus-nodered')
        ->assertSee('github.com/nielseulink/ha_psa_controller');
});

test('projects page can filter by tag', function () {
    $this->get(route('projects', ['tag' => 'Python']))
        ->assertSuccessful()
        ->assertSee('HA PSA Car Controller');
});

test('projects page clears tag filter', function () {
    Livewire::test(Projects::class)
        ->call('setTag', 'Python')
        ->assertSet('tag', 'Python')
        ->call('clearTag')
        ->assertSet('tag', null);
});

test('layout includes social links', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('https://github.com/nielseulink', false)
        ->assertSee('https://www.linkedin.com/in/nielseulink', false);
});

test('contact route returns not found', function () {
    $this->get('/contact')->assertNotFound();
});

test('removed smarthome route returns not found', function () {
    $this->get('/smarthome')->assertNotFound();
});

test('removed home server route returns not found', function () {
    $this->get('/home-server')->assertNotFound();
});
