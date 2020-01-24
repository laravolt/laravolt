# :zap: Laravolt 

Membuat aplikasi web 2 minggu jadi.

Dokumentasi bisa dibaca di http://laravolt.dev/.



## Kontribusi

1. Setup sebuah aplikasi Laravel baru di folder `/<workspace>/laravel`.

2. Untuk core contributor, clone https://github.com/laravolt/laravolt ke folder `/<workspace>/laravolt`.

3. Untuk contributor biasa, silakan fork repo `laravolt/laravolt` di atas, lalu clone repository kepunyaan Anda sendiri ke folder `/<workspace>/laravolt`.

4. Tambahkan potongan kode berikut ke `<workspace>/laravel/composer.json`:

        "repositories": [
            {
                "type": "path",
                "url": "../laravolt/",
                "options": {
                    "symlink": true
                }
            }
        ],

5. Ikuti petunjuk setup Laravolt di https://laravolt.dev/docs/installation/.
6. Silakan koding di folder `laravolt/packages/<package-name>`.
7. Selamat berkontribusi :)