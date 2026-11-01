# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 13 HUSTOJ application. Core PHP code lives in `app/`: controllers in `app/Http/Controllers`, models in `app/Entities`, services in `app/Services`, queue payload logic in `app/Task`, and custom integrations in `app/Hustoj`. Routes are split across `routes/web.php`, `routes/admin.php`, `routes/judge.php`, and `routes/console.php`. Configuration is in `config/`, migrations and seeders in `database/`, Blade views in `resources/views`, translations in `resources/lang`, and Vue 2 / Sass assets in `resources/assets`. Tests are in `tests/Unit`, `tests/Feature`, and `tests/Api`.

## Build, Test, and Development Commands

- `composer install`: install PHP dependencies.
- `cp .env.example .env && php artisan key:generate`: create a local Laravel environment.
- `php artisan migrate --seed`: prepare schema and seed data.
- `php artisan serve`: run the application locally.
- `npm ci`: install frontend dependencies from `package-lock.json`.
- `npm run dev`: build the public frontend assets.
- `npm run admin-dev`: build backend/admin assets.
- `npm run prod` and `npm run admin-prod`: create production bundles.
- `vendor/bin/phpunit`: run tests.

## Coding Style & Naming Conventions

Use PSR-12 style PHP with four-space indentation. Keep classes in the `App\` namespace matching their path. Name controllers with a `Controller` suffix, form requests with `Request`, and Eloquent models as singular nouns. Prefer dependency injection or existing Laravel facades. Frontend code uses Vue 2 components in `resources/assets/js`; keep filenames consistent with nearby files.

## Testing Guidelines

PHPUnit is the test framework. Place helper tests in `tests/Unit`, HTTP workflow tests in `tests/Feature`, and judge API tests in `tests/Api`. Name files `*Test.php` and methods with descriptive `test...` names. Add regression tests for authentication, contest access, problem submission, and judge queue behavior when changing those areas.

## Commit & Pull Request Guidelines

Recent history uses short imperative messages such as `upgrade composer.lock`, plus scoped messages like `style: format code with PHP CS Fixer`. Keep commits focused and describe behavior changes. Pull requests should include a summary, test results, linked issues when available, and screenshots for UI changes. Note database, queue, mail, or environment-variable impact.

## Security & Configuration Tips

Never commit `.env`, credentials, judge secrets, Sentry DSNs, AWS keys, or RabbitMQ passwords. Update `.env.example` when adding required configuration. Treat judge submission, source display, authentication, and admin authorization as security-sensitive.

## Agent-Specific Instructions

When working in this repository, communicate with maintainers in Chinese. Keep generated changes small, preserve existing Laravel and Vue 2 patterns, and avoid unrelated framework or frontend migrations unless explicitly requested.
