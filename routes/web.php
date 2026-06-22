<?php

use App\Livewire\Pages\Analytics\Dashboard;
use App\Livewire\Pages\Blog\Index as BlogIndex;
use App\Livewire\Pages\Blog\Show as BlogShow;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Projects;
use Illuminate\Support\Facades\Route;

Route::livewire('/', Home::class)->name('home');
Route::livewire('/blog', BlogIndex::class)->name('blog.index');
Route::livewire('/blog/{slug}', BlogShow::class)->name('blog.show');
Route::livewire('/projects', Projects::class)->name('projects');

Route::livewire('/analytics', Dashboard::class)->name('analytics');
