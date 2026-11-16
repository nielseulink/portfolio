<?php

namespace App\Livewire\Pages;

use App\Services\GitHub\GitHubActivity;
use App\Services\Projects\ProjectRepository;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projects')]
class Projects extends Component
{
    #[Url(as: 'tag')]
    public ?string $tag = null;

    public function mount(ProjectRepository $projects): void
    {
        if ($this->tag !== null && ! in_array($this->tag, $projects->tags(), true)) {
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

    public function render(ProjectRepository $projects, GitHubActivity $github)
    {
        return view('livewire.pages.projects', [
            'projects' => $projects->filtered($this->tag),
            'tags' => $projects->tags(),
            'hasProjects' => $projects->all() !== [],
            'projectStats' => $github->projectStats(),
            'contributions' => $github->contributions(),
            'githubUsername' => config('site.github.username'),
        ]);
    }
}
