<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Ui;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request as RequestFacade;
use Laravolt\Platform\Services\SidebarBuilder;
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

    public function test_each_render_runs_each_callback_once(): void
    {
        $calls = 0;
        app('laravolt.menu.builder')->register(function () use (&$calls): void {
            $calls++;
        });

        foreach (range(1, 3) as $request) {
            $this->composeSidebar();
        }

        // Once per render. Before the fix, every render re-registered the
        // callback, so the count grew 1 + 2 + 3 + ... instead.
        $this->assertSame(3, $calls);
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

    /**
     * What the sidebar view does on every render.
     */
    public function test_the_menu_is_built_without_rendering_the_sidebar_view(): void
    {
        $this->actingAs($this->user(1));

        $this->assertContains('Stock', $this->titles());
    }

    public function test_children_lookups_follow_the_menu_as_it_changes(): void
    {
        $this->actingAs($this->user(1));
        config(['laravolt.menu.reports.Reports.menu.Archive' => ['menu' => [
            'This year' => ['url' => '/reports/archive/2025'],
            'Last year' => ['url' => '/reports/archive/2024', 'permissions' => ['see-old']],
        ]]]);
        Gate::define('see-old', fn (): bool => false);

        $reports = app('laravolt.menu.sidebar')->allMenu()->first(fn ($section) => $section->title === 'Reports');
        $sidebar = app('laravolt.menu.sidebar')->get('sidebar');
        $this->assertInstanceOf(SidebarBuilder::class, $sidebar);

        $archive = $reports->children()->first(fn ($item) => $item->title === 'Archive');
        $this->assertSame(['This year'], $archive->children()->pluck('title')->all());
        $this->assertFalse($archive->children()->first()->hasChildren());

        // Same answers as Lavary's own lookup, which scans every item.
        foreach ($sidebar->all() as $item) {
            $this->assertSame(
                $sidebar->all()->filter(fn ($other) => $other->parent == $item->id)->pluck('title')->values()->all(),
                $item->children()->pluck('title')->all(),
            );
        }

        // Items added after a lookup show up in the next one.
        $archive->add('Older', '/reports/archive/2023');
        $this->assertSame(['This year', 'Older'], $archive->children()->pluck('title')->all());
        $sidebar->filter(fn ($item) => $item->title !== 'This year');
        $this->assertSame(['Older'], $archive->children()->pluck('title')->all());
    }

    public function test_items_are_activated_by_the_request_url(): void
    {
        $this->actingAs($this->user(1));
        $this->app->instance('request', Request::create('http://localhost/reports/stock?page=2'));
        RequestFacade::clearResolvedInstance('request');
        app('laravolt.menu.builder')->register(function ($menu): void {
            $menu->add('Exact', 'reports/stock?page=2');
            $menu->add('Pinned', ['url' => 'reports/stock', 'disableActivationByURL' => true]);
        });

        app('laravolt.menu.sidebar')->allMenu();
        $active = app('laravolt.menu.sidebar')->get('sidebar')->all()
            ->filter(fn ($item) => $item->isActive)->pluck('title')->sort()->values()->all();

        // Stock matches the URL, Exact the URL with its query string, and
        // Reports is Stock's parent. Pinned opted out of URL activation.
        $this->assertSame(['Exact', 'Reports', 'Stock'], $active);
    }

    private function composeSidebar(): void
    {
        app('laravolt.menu.sidebar')->allMenu();
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
