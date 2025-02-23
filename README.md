# Laravolt

Platform untuk mengembangkan sistem informasi dalam 2 minggu

## Dokumentasi

https://laravolt.dev/

## Version Compatibility

| Laravolt | PHP       | Laravel        | Livewire |
| -------- | --------- | -------------- | -------- |
| 4.x      | ^7.3      | 6.x            | -        |
| 5.x      | 8.0 - 8.3 | 8.x, 9.x, 10.x | 2.x      |
| 6.x      | 8.2 - 8.4 | 11.x, 12.x     | 3.x      |

## Requirements for Laravel 11.x and 12.x

1. Tambahkan pada berkas `bootstrap/providers.php`:

   ```php
   <?php

   return [
       App\Providers\AppServiceProvider::class,   // existing providers
       App\Providers\AuthServiceProvider::class,  // new providers
       App\Providers\EventServiceProvider::class, // new providers
       App\Providers\RouteServiceProvider::class, // new providers
   ];
   ```

## TODO for L12 Compatibility

### 1. **no-captcha**

Current development using this repository: git@github.com:laravel-shift/no-captcha.git

- [ ] Ensure compatibility with Laravel 12.x.
- [ ] Update dependencies in [composer.json](https://github.com/anhskohbo/no-captcha).
- [ ] Run tests and fix any issues.
- [ ] Update documentation if necessary.

### 2. **laravel-enum**

Current development using this repository: git@github.com:laravel-shift/laravel-enum.git

- [ ] Ensure compatibility with Laravel 12.x.
- [ ] Update dependencies in [composer.json](https://github.com/bensampo/laravel-enum).
- [ ] Run tests and fix any issues.
- [ ] Update documentation if necessary.

### 3. **laravel-nestedset**

Current development using this repository: git@github.com:jonnott/laravel-nestedset.git

- [ ] Ensure compatibility with Laravel 12.x.
- [ ] Update dependencies in [composer.json](https://github.com/kalnoy/nestedset).
- [ ] Run tests and fix any issues.
- [ ] Update documentation if necessary.

### 4. **laravel-avatar**

- [ ] Ensure compatibility with Laravel 12.x.
- [ ] Update dependencies in [composer.json](https://github.com/laravolt/avatar).
- [ ] Run tests and fix any issues.
- [ ] Update documentation if necessary.
