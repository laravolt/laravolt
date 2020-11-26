<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Laravel\Ui\UiCommand;
use Laravolt\Platform\Commands\LinkCommand;

class LaravoltUiCommand extends UiCommand
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        Artisan::call(LinkCommand::class);
        Artisan::call('vendor:publish', ['--tag' => 'migrations']);
        static::replaceFiles();
    }

    protected static function replaceFiles()
    {
        $directories = [
            resource_path('lang/id') => platform_path('stubs/lang/id'),
        ];

        foreach ($directories as $destination => $source) {
            (new Filesystem())->copyDirectory($source, $destination);
        }

        $files = [
            app_path('Models/User.php') => platform_path('stubs/User.php'),
            app_path('Http/Middleware/Authenticate.php') => platform_path('stubs/Authenticate.php'),
            app_path('Http/Middleware/RedirectIfAuthenticated.php') => platform_path('stubs/RedirectIfAuthenticated.php'),
            app_path('Exceptions/Handler.php') => platform_path('stubs/Handler.php'),
            app_path('Http/Controllers/Home.php') => platform_path('stubs/Home.php'),
            app_path('Http/Controllers/Dashboard.php') => platform_path('stubs/Dashboard.php'),
            resource_path('views/dashboard.blade.php') => platform_path('stubs/dashboard.blade.php'),
            base_path('routes/web.php') => platform_path('stubs/routes.php'),
            app_path('Http/Controllers/Auth') => null,
            public_path('js/app.js') => platform_path('stubs/app.js'),
            public_path('css/app.css') => platform_path('stubs/app.css'),
            base_path('webpack.mix.js') => platform_path('stubs/webpack.mix.js'),
        ];

        foreach ($files as $original => $new) {
            if (is_file($original)) {
                (new Filesystem())->delete($original);
            } elseif (is_dir($original)) {
                \File::deleteDirectory($original);
            }

            if ($new !== null) {
                copy($new, $original);
            }
        }

        $entries = [
            base_path('.gitignore') => [
                '/public/laravolt',
            ],
        ];

        foreach ($entries as $file => $lines) {
            foreach ($lines as $line) {
                file_put_contents($file, $line."\n", FILE_APPEND);
            }
        }
    }
}
