<?php

namespace App\Services\Blog;

use App\Data\Blog\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class PostRepository
{
    public function __construct(
        private string $contentPath = '',
    ) {
        $this->contentPath = $contentPath !== ''
            ? $contentPath
            : base_path('content/blog');
    }

    /**
     * @return list<Post>
     */
    public function all(): array
    {
        if (! File::isDirectory($this->contentPath)) {
            return [];
        }

        return collect(File::files($this->contentPath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $this->parseFile($file->getPathname()))
            ->filter()
            ->sortByDesc(fn (Post $post) => $post->publishedAt)
            ->values()
            ->all();
    }

    /**
     * @return list<Post>
     */
    public function filtered(?string $tag = null): array
    {
        if ($tag === null || $tag === '') {
            return $this->all();
        }

        return collect($this->all())
            ->filter(fn (Post $post) => in_array($tag, $post->tags, true))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function tags(): array
    {
        return collect($this->all())
            ->flatMap(fn (Post $post) => $post->tags)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function find(string $slug): ?Post
    {
        return collect($this->all())->first(fn (Post $post) => $post->slug === $slug);
    }

    /**
     * @return list<Post>
     */
    public function latest(int $limit = 3): array
    {
        return array_slice($this->all(), 0, $limit);
    }

    private function parseFile(string $path): ?Post
    {
        $contents = File::get($path);

        if (! preg_match('/^---\R(.*?)\R---\R(.*)$/s', $contents, $matches)) {
            return null;
        }

        /** @var array<string, mixed> $frontMatter */
        $frontMatter = Yaml::parse($matches[1]) ?? [];
        $body = trim($matches[2]);

        $title = (string) ($frontMatter['title'] ?? '');
        $slug = (string) ($frontMatter['slug'] ?? Str::slug($title));
        $excerpt = (string) ($frontMatter['excerpt'] ?? '');
        $publishedAt = $this->parsePublishedAt($frontMatter['published_at'] ?? now()->toDateString());

        /** @var list<string> $tags */
        $tags = collect($frontMatter['tags'] ?? [])
            ->map(fn ($tag) => (string) $tag)
            ->values()
            ->all();

        if ($title === '' || $slug === '') {
            return null;
        }

        $imageFolder = isset($frontMatter['id']) ? (string) $frontMatter['id'] : $slug;
        $body = $this->resolveImagePaths($body, $imageFolder);

        return new Post(
            title: $title,
            slug: $slug,
            excerpt: $excerpt,
            publishedAt: $publishedAt,
            tags: $tags,
            html: Str::markdown($body),
            path: $path,
        );
    }

    private function resolveImagePaths(string $body, string $folder): string
    {
        return preg_replace_callback(
            '/!\[([^\]]*)\]\((?!https?:\/\/|\/)(?:\.\/)?images\/([^)]+)\)(?:\{(width=(\d+)|size=(sm|md|lg|full))\})?/i',
            function (array $matches) use ($folder): string {
                $alt = $matches[1];
                $file = $matches[2];
                $src = '/images/blog/'.$folder.'/'.$file;

                if (! empty($matches[4])) {
                    $width = (int) $matches[4];

                    return sprintf(
                        '<img src="%s" alt="%s" style="max-width: %dpx; width: 100%%; height: auto;" />',
                        $src,
                        htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'),
                        $width,
                    );
                }

                if (! empty($matches[5])) {
                    return sprintf(
                        '<img src="%s" alt="%s" class="blog-image blog-image-%s" />',
                        $src,
                        htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'),
                        $matches[5],
                    );
                }

                return sprintf('![%s](%s)', $alt, $src);
            },
            $body,
        );
    }

    private function parsePublishedAt(mixed $value): Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_int($value)) {
            return Carbon::createFromTimestamp($value);
        }

        return Carbon::parse((string) $value);
    }
}
