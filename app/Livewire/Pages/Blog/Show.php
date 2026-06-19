<?php

namespace App\Livewire\Pages\Blog;

use App\Services\Blog\PostRepository;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(PostRepository $posts)
    {
        $post = $posts->find($this->slug);

        if ($post === null) {
            abort(404);
        }

        return view('livewire.pages.blog.show', [
            'post' => $post,
        ]);
    }
}
