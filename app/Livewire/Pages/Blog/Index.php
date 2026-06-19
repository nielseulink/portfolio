<?php

namespace App\Livewire\Pages\Blog;

use App\Services\Blog\PostRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Blog')]
class Index extends Component
{
    #[Url(as: 'tag')]
    public ?string $tag = null;

    public function mount(PostRepository $posts): void
    {
        if ($this->tag !== null && ! in_array($this->tag, $posts->tags(), true)) {
            $this->tag = null;
        }
    }

    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    public function clearTag(): void
    {
        $this->tag = null;
    }

    public function render(PostRepository $posts)
    {
        return view('livewire.pages.blog.index', [
            'posts' => $posts->filtered($this->tag),
            'tags' => $posts->tags(),
            'hasPosts' => $posts->all() !== [],
        ]);
    }
}
