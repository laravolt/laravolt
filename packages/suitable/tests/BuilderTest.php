<?php

namespace Laravolt\Suitable\Tests;

use Illuminate\Database\Eloquent\Collection;
use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Components\Action;
use Laravolt\Suitable\Components\Checkall;

class BuilderTest extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            \Laravolt\Suitable\ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Suitable' => \Laravolt\Suitable\Facade::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mural.skin', 'semantic-ui');
    }

    /**
     * @test
     */
    public function it_can_be_created()
    {
        new Builder();
    }

    /**
     * @test
     */
    public function it_can_render_empty_collection()
    {
        $builder = new Builder();
        $collection = new Collection();

        $html = $builder->source($collection)->render();

    }


    /**
     * @test
     */
    public function it_can_render_checkall_column()
    {
        $builder = new Builder();
        $collection = new Collection();

        $builder->source($collection)
                ->columns([
                    new Checkall(),
                ])
                ->render();
    }

    /**
     * @test
     */
    public function it_can_render_rest_action_column()
    {
        $builder = new Builder();
        $collection = new Collection();

        $builder->source($collection)
                ->columns([
                    new Action(),
                ])
                ->render();
    }
}
