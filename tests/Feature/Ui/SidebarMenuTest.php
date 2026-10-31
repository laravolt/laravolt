<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Laravolt\Tests\FeatureTest;

/**
 * The sidebar singletons live as long as the worker. Under Octane they
 * serve many requests, so rendering the sidebar must not grow them, and
 * the per-user menu must never be shared between users.
 */
class SidebarMenuTest extends FeatureTest
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['laravolt.menu.reports' => [
            'Reports' => [
                'order' => 5,
                'menu' => [
                    'Sales' => ['url' => '/reports/sales', 'permissions' => ['see-sales']],
                    'Stock' => ['url' => '/reports/stock'],
                ],
            ],
        ]]);
    }

    public function test_rendering_the_sidebar_repeatedly_runs_each_callback_once(): void
    {
        $calls = 0;
        app('laravolt.menu.builder')->register(function () use (&$calls): void {
            $calls++;
        });

        foreach (range(1, 3) as $request) {
            $this->composeSidebar();
        }

        app('laravolt.menu.sidebar')->all();

        $this->assertSame(1, $calls);
    }

    public function test_config_menus_are_loaded_once_however_often_the_sidebar_renders(): void
    {
        $this->actingAs($this->user(1));

        $this->composeSidebar();
        $first = $this->titles();

        foreach (range(1, 5) as $request) {
            $this->composeSidebar();
        }

        $this->assertSame($first, $this->titles());
        $this->assertSame(1, array_count_values($first)['Stock']);
    }

    public function test_each_user_gets_their_own_menu_in_production(): void
    {
        $this->app['env'] = 'production';
        Gate::define('see-sales', fn (User $user): bool => $user->getKey() === 1);
        $this->composeSidebar();

        $this->actingAs($this->user(1));
        $this->assertContains('Sales', $this->titles());

        $this->actingAs($this->user(2));
        $this->assertNotContains('Sales', $this->titles());
    }

    private function composeSidebar(): void
    {
        app('view')->callComposer(view('laravolt::menu.sidebar'));
    }

    /**
     * @return list<string>
     */
    private function titles(): array
    {
        $titles = [];
        foreach (app('laravolt.menu.sidebar')->allMenu() as $section) {
            foreach ($section->children() as $item) {
                $titles[] = $item->title;
            }
        }

        return $titles;
    }

    private function user(int $id): User
    {
        $user = new User;
        $user->forceFill(['id' => $id]);

        return $user;
    }
}
