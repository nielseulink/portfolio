<?php

namespace App\Data\GitHub;

use Illuminate\Support\Carbon;

/**
 * One entry of the "Recent on GitHub" list: an own project or a repository I contributed to.
 */
readonly class ActivityItem
{
    public function __construct(
        public string $name,
        public ?string $description,
        public string $url,
        public int $stars,
        public string $activityLabel,
        public Carbon $activityAt,
    ) {}
}
