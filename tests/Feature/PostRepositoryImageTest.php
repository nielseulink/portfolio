<?php

use App\Services\Blog\PostRepository;
use Illuminate\Support\Facades\File;

test('post repository resolves relative blog image paths from post id folder', function () {
    $contentPath = sys_get_temp_dir().'/blog-images-'.uniqid();

    File::ensureDirectoryExists($contentPath);

    File::put($contentPath.'/with-image.md', <<<'MD'
---
title: Image post
slug: image-post
id: test-post-id
published_at: "2026-06-18"
---

![Diagram](images/diagram.png)
MD);

    $post = (new PostRepository($contentPath))->find('image-post');

    expect($post)->not->toBeNull()
        ->and($post->html)->toContain('/images/blog/test-post-id/diagram.png')
        ->and($post->html)->toContain('alt="Diagram"');

    File::deleteDirectory($contentPath);
});

test('post repository resolves relative blog image paths from slug when id is missing', function () {
    $contentPath = sys_get_temp_dir().'/blog-images-'.uniqid();

    File::ensureDirectoryExists($contentPath);

    File::put($contentPath.'/with-image.md', <<<'MD'
---
title: Image post
slug: my-cool-post
published_at: "2026-06-18"
---

![](./images/photo.jpg)
MD);

    $post = (new PostRepository($contentPath))->find('my-cool-post');

    expect($post)->not->toBeNull()
        ->and($post->html)->toContain('/images/blog/my-cool-post/photo.jpg');

    File::deleteDirectory($contentPath);
});

test('post repository leaves absolute image urls unchanged', function () {
    $contentPath = sys_get_temp_dir().'/blog-images-'.uniqid();

    File::ensureDirectoryExists($contentPath);

    File::put($contentPath.'/with-image.md', <<<'MD'
---
title: Image post
slug: external-image
published_at: "2026-06-18"
---

![Remote](https://example.com/photo.png)
![Local](/images/blog/shared/photo.png)
MD);

    $post = (new PostRepository($contentPath))->find('external-image');

    expect($post)->not->toBeNull()
        ->and($post->html)->toContain('https://example.com/photo.png')
        ->and($post->html)->toContain('/images/blog/shared/photo.png');

    File::deleteDirectory($contentPath);
});

test('post repository supports custom image width in markdown', function () {
    $contentPath = sys_get_temp_dir().'/blog-images-'.uniqid();

    File::ensureDirectoryExists($contentPath);

    File::put($contentPath.'/with-image.md', <<<'MD'
---
title: Image post
slug: sized-image
id: 99
published_at: "2026-06-18"
---

![Photo](images/photo.jpg){width=400}
MD);

    $post = (new PostRepository($contentPath))->find('sized-image');

    expect($post)->not->toBeNull()
        ->and($post->html)->toContain('max-width: 400px')
        ->and($post->html)->toContain('/images/blog/99/photo.jpg');

    File::deleteDirectory($contentPath);
});

test('post repository supports preset image sizes in markdown', function () {
    $contentPath = sys_get_temp_dir().'/blog-images-'.uniqid();

    File::ensureDirectoryExists($contentPath);

    File::put($contentPath.'/with-image.md', <<<'MD'
---
title: Image post
slug: preset-size
published_at: "2026-06-18"
---

![Photo](images/photo.jpg){size=md}
MD);

    $post = (new PostRepository($contentPath))->find('preset-size');

    expect($post)->not->toBeNull()
        ->and($post->html)->toContain('class="blog-image blog-image-md"')
        ->and($post->html)->toContain('/images/blog/preset-size/photo.jpg');

    File::deleteDirectory($contentPath);
});
