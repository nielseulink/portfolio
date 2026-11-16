# nielseu.link

Personal site and blog of Niels Eulink, live at [nielseu.link](https://nielseu.link).

Stack: Laravel 13, Livewire 4, Flux UI (Pro), Tailwind CSS 4 and Pest. Blog posts are Markdown files in
`content/blog`; projects are listed in `config/site.php`.

## Development

Flux Pro needs credentials for `composer.fluxui.dev` in `auth.json` or the global Composer config.

```sh
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan dev
```

Run the tests with `php artisan test --compact` and format PHP with `vendor/bin/pint`.

## Workflow

- `main` only takes pull requests that are up to date with it and pass Changes, Pint and Pest (`.github/workflows/tests.yml`).
- Dependabot opens grouped updates weekly. Patch and minor updates merge on their own once the checks pass; majors wait
  for review.
- Every push to `main` deploys to the server through a Ploi webhook.
