<?php

require 'vendor/autoload.php';

use Orchestra\Testbench\TestCase;
use Laravolt\Platform\Models\User;

class DummyTest extends TestCase {
    protected function getPackageProviders($app) {
        return [
            \Laravolt\Platform\Providers\PlatformServiceProvider::class,
            \Laravolt\Platform\Providers\UiServiceProvider::class,
            \Laravolt\Avatar\ServiceProvider::class,
        ];
    }
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('laravolt.avatar', [
            'driver'    => 'gd',
            'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,
            'ascii'    => false,
            'shape' => 'circle',
            'width'    => 100,
            'height'   => 100,
            'chars'    => 2,
            'fontSize' => 48,
            'fonts'    => [__DIR__.'/vendor/laravolt/avatar/fonts/OpenSans-Bold.ttf'],
            'fontFile' => __DIR__.'/vendor/laravolt/avatar/fonts/OpenSans-Bold.ttf',
            'fontDisplay' => 'fallback',
            'fontDir' => __DIR__.'/vendor/laravolt/avatar/fonts/',
        ]);
    }
    public function runTest() {
        $this->setUp();
        $user = new User();
        $user->name = 'Test User';
        echo $user->avatar . "\n";
    }
}

$t = new DummyTest('test_avatar');
$t->runTest();
