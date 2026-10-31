<?php

declare(strict_types=1);

namespace Laravolt\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

/**
 * Base for Pest feature tests. FeatureTest extends the BrowserKit TestCase,
 * whose visit() clashes with the Pest browser plugin.
 */
abstract class PackageTest extends TestCase
{
    use Bootstrap {
        getPackageProviders as bootstrapPackageProviders;
        setUp as bootstrapSetUp;
    }

    protected function setUp(): void
    {
        $this->bootstrapSetUp();

        // Apps publish this stub; the package tests run it directly.
        if (! Schema::hasTable('media')) {
            (include __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub')->up();
        }
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            ...$this->bootstrapPackageProviders($app),
            MediaLibraryServiceProvider::class,
            \Laravolt\Media\ServiceProvider::class,
        ];
    }
}
