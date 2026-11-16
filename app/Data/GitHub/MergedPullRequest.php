<?php

namespace App\Data\GitHub;

use Illuminate\Support\Carbon;

readonly class MergedPullRequest
{
    public function __construct(
        public string $title,
        public string $url,
        public Carbon $mergedAt,
    ) {}
}
