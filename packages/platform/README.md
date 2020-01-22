[![StyleCI](https://github.styleci.io/repos/195338100/shield?branch=master)](https://github.styleci.io/repos/195338100) [![Build Status](https://travis-ci.org/laravolt/platform.svg?branch=master)](https://travis-ci.org/laravolt/platform) [![Coverage Status](https://coveralls.io/repos/github/laravolt/platform/badge.svg?branch=master)](https://coveralls.io/github/laravolt/platform?branch=master)

# Laravolt Platform
Platform untuk mengembangkan sistem informasi dalam 2 minggu

## Instalasi
Buat sebuah proyek Laravel baru dengan menjalankan:

```bash
laravel new awesome-application
```

atau:

```bash
composer create-project --prefer-dist laravel/laravel awesome-application
```



Masuk ke folder aplikasi, lalu tambahkan laravolt/platform:

```bash
composer require laravolt/platform
```

Setup skeleton Laravolt dengan menjalankan:

```bash
php artisan preset laravolt
```

Untuk menambahkan admin, jalankan perintah:

```bash
php artisan laravolt:admin
```



Jalankan `php artisan serve` atau web server lain kesukaanmu, buka URL aplikasi di browser, dan Laravolt siap menebar pesona 😉
