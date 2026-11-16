<?php

namespace App\Data\GitHub;

use Illuminate\Support\Carbon;

readonly class RepositoryStats
{
    public function __construct(
        public string $fullName,
        public string $url,
        public ?string $description,
        public int $stars,
        public Carbon $pushedAt,
        public ?string $latestRelease,
    ) {}
}
