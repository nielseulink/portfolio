<?php

namespace App\Data\Blog;

use Illuminate\Support\Carbon;

readonly class Post
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public string $title,
        public string $slug,
        public string $excerpt,
        public Carbon $publishedAt,
        public array $tags,
        public string $html,
        public string $path,
    ) {}
}
