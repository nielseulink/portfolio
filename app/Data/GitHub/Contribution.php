<?php

namespace App\Data\GitHub;

use Illuminate\Support\Carbon;

/**
 * An open source repository of someone else with the pull requests of mine that it merged.
 */
readonly class Contribution
{
    /**
     * @param  list<MergedPullRequest>  $pullRequests  newest first
     */
    public function __construct(
        public string $repository,
        public string $url,
        public ?string $description,
        public int $stars,
        public array $pullRequests,
    ) {}

    public function lastMergedAt(): Carbon
    {
        return $this->pullRequests[0]->mergedAt;
    }
}
