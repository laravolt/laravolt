<p>
    <a href="https://github.com/laravolt/laravolt/actions/workflows/laravel.yml"><img src="https://github.com/laravolt/laravolt/actions/workflows/laravel.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/laravolt/laravolt"><img src="https://img.shields.io/packagist/v/laravolt/laravolt" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/laravolt/laravolt"><img src="https://img.shields.io/packagist/dt/laravolt/laravolt" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/laravolt/laravolt"><img src="https://img.shields.io/packagist/l/laravolt/laravolt" alt="License"></a>
</p>

# Laravolt

Platform untuk mengembangkan sistem informasi dalam 2 minggu.

Laravolt is a Laravel platform package for building admin-heavy information systems: users, roles and permissions (ACL), Preline UI / Tailwind components, data tables, form builders, a CRUD generator (Thunderclap), media handling, lookup data and Camunda workflow integration.

Documentation: https://laravolt.dev/

## Requirements

- PHP 8.3, 8.4 or 8.5
- Laravel 12.68+ or 13.29+

## Getting Started

### New application (recommended)

Start from the [Laravolt starter kit](https://github.com/laravolt/laravel-starter-kit), which ships with Laravolt preinstalled and configured:

```bash
composer create-project laravolt/laravel-starter-kit example-app
```

### Existing Laravel application

```bash
composer require laravolt/laravolt
php artisan laravolt:install
php artisan migrate
php artisan laravolt:admin
```

`laravolt:install` publishes the skeleton files, migrations and assets. `laravolt:admin` creates the first administrator account.

## Optional packages

Laravolt v8 no longer installs these packages. Require them in your application if you use them:

| Package | Use |
| ------- | --- |
| `laravel/octane` | Serve the app with Octane |
| `barryvdh/laravel-dompdf` | PDF rendering via `Pdf::` |
| `barryvdh/laravel-debugbar` | Debug toolbar (dev) |
| `spatie/laravel-web-tinker` | Browser-based Tinker |
| `anhskohbo/no-captcha` | reCAPTCHA on auth forms |

## Version Compatibility

| Laravolt | PHP       | Laravel                | Livewire |
| -------- | --------- | ---------------------- | -------- |
| 8.x      | 8.3 - 8.5 | 12.68+, 13.29+         | 4.x      |
| 7.x      | 8.2 - 8.4 | 12.x, 13.x             | 4.x      |
| 6.x      | 8.2 - 8.4 | 11.x, 12.x             | 3.x      |
| 5.x      | 8.0 - 8.3 | 8.x, 9.x, 10.x         | 2.x      |
| 4.x      | ^7.3      | 6.x                    | -        |

Only the latest major receives fixes. Laravel 12.68 / 13.29 is the minimum because earlier releases are affected by CVE-2026-114509.

## Upgrading

See [CHANGELOG.md](CHANGELOG.md) for breaking changes and upgrade steps (v7 → v8).

## Security

Please report vulnerabilities privately through [GitHub Security Advisories](https://github.com/laravolt/laravolt/security/advisories/new) rather than public issues.

## License

Laravolt is open-sourced software licensed under the [MIT license](LICENSE).
