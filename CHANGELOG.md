# Changelog Laravolt Versi 5
## Middleware
- Namespace middleware berubah dari `Laravolt\Platform\Http\Middleware` menjadi `Laravolt\Middleware`.
- Middleware perlu ditambahkan secara eksplisit ke app\Http\Kernel.php:
    - `Laravolt\Middleware\DetectFlashMessage`
    - `Laravolt\Middleware\CheckPassword`

## Migrations
- migrations script harus dipublish dulu dengan `php artisan vendor:publish --tag=laravolt-migrations` atau `php artisan laravolt:install`
