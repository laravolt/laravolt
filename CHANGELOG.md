# Changelog Laravolt Versi 5
## Middleware
- Namespace middleware berubah dari `Laravolt\Platform\Http\Middleware` menjadi `Laravolt\Middleware`.
- Middleware perlu ditambahkan secara eksplisit ke app\Http\Kernel.php:
    - `Laravolt\Middleware\DetectFlashMessage`
    - `Laravolt\Middleware\CheckPassword`

## Migrations
- migrations script harus dipublish dulu dengan `php artisan vendor:publish --tag=laravolt-migrations` atau `php artisan laravolt:install`

## Installation
- Tidak perlu compile assets dulu
- Ganti `php artisan ui laravolt` menjadi `php artisan laravolt:install`

## Facade
- `SemanticForm` dihapus, pakai `Form`

## Layout
- ubah dari @extends menjadi component based

## New Feature
- font awesome 5 pro icon: regular, solid, light, duotone

## Configurations
- Config key `route` diubah menjadi `routes` supaya align dengan folder `routes` Laravel

- route('dashboard') dihilangkan, pindah ke route('home')
- registrasi menu di config `config/laravolt/menu/`, tidak boleh ada pemanggilan fungsi helper, semuanya harus static text
