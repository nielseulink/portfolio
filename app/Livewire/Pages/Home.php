<?php

namespace App\Livewire\Pages;

use App\Services\Blog\PostRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Home')]
class Home extends Component
{
    public function render(PostRepository $posts)
    {
        return view('livewire.pages.home', [
            'latestPosts' => $posts->latest(),
        ]);
    }
}
