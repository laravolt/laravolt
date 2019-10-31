<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\Presets\Preset;

class LaravoltPreset extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::linkAssets();
        static::replaceFiles();
    }

    protected static function linkAssets()
    {
        if (!file_exists(public_path('laravolt'))) {
            (new Filesystem())->link(
                platform_path('public'), public_path('laravolt')
            );
        }
    }

    protected static function replaceFiles()
    {
        $files = [
            app_path('Http/Middleware/Authenticate.php') => platform_path('stubs/Authenticate.php'),
            app_path('Http/Middleware/RedirectIfAuthenticated.php') => platform_path('stubs/RedirectIfAuthenticated.php'),
            app_path('Exceptions/Handler.php') => platform_path('stubs/Handler.php'),
            app_path('Http/Controllers/Home.php') => platform_path('stubs/Home.php'),
            app_path('Http/Controllers/Dashboard.php') => platform_path('stubs/Dashboard.php'),
            resource_path('views/dashboard.blade.php') => platform_path('stubs/dashboard.blade.php'),
        ];

        foreach ($files as $original => $new) {
            (new Filesystem)->delete($original);
            copy($new, $original);
        }

        // Add Auth route in 'routes/web.php'
        $entries = [
            "Route::get('/', 'Home')->name('home');",
            "Route::get('/dashboard', 'Dashboard')->name('dashboard');",
        ];

        foreach ($entries as $entry) {
            file_put_contents(base_path('routes/web.php'), $entry."\n", FILE_APPEND);
        }
    }
}
