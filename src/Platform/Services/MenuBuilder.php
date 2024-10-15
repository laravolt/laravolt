<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Support\Facades\Route;

class MenuBuilder
{
    protected $defaultIcon;

    protected $registeredCallbacks = [];

    public function __construct()
    {
        $this->defaultIcon = config('laravolt.ui.default_menu_icon');
    }

    public function register(\Closure $callback)
    {
        $this->registeredCallbacks[] = $callback;
    }

    public function runCallbacks()
    {
        foreach ($this->registeredCallbacks as $callback) {
            app('laravolt.menu.sidebar')->registerCore($callback);
        }
    }

    public function loadArray(array $menu)
    {
        $order = null;
        $filteredMenu = collect($menu)->reject(fn ($item) => ! ($item['menu'] ?? null));
        foreach ($filteredMenu as $title => $option) {
            if ($order === null) {
                $order = $option['order'] ?? 50;
            }

            app('laravolt.menu.sidebar')->registerCore(
                function ($sidebar) use ($title, $option, $order) {
                    /** @var \Lavary\Menu\Builder $section */
                    $section = $sidebar->get(strtolower(trim($title)));
                    if ($section === null) {
                        $url = $this->generateUrl($option);
                        $section = $sidebar->add($title, $url)->data('order', $order);
                    }

                    $section->data('permissions', $option['permissions'] ?? null);

                    if (isset($option['menu'])) {
                        $this->addMenu($section, $option['menu']);
                    }
                    if (isset($option['data'])) {
                        $this->setData($section, $option['data']);
                    }
                }
            );
            $order++;
        }
    }

    private function addMenu(&$parent, $menus)
    {
        foreach ($menus as $name => $option) {
            if (is_string($option)) {
                $parent->add($name, $option);

                continue;
            }

            if (! isset($option['menu'])) {
                $menu = $parent->add($name, $this->generateUrl($option));
                if (isset($option['active'])) {
                    $menu->active($option['active']);
                }
            } else {
                $menu = $parent->add($name, '#');
                $this->addMenu($menu, $option['menu']);
            }
            if (isset($option['data'])) {
                $this->setData($menu, $option['data']);
            }

            $menu->data('icon', $option['icon'] ?? $this->defaultIcon);
            $menu->data('permissions', $option['permissions'] ?? null);
        }
    }

    private function setData(&$menu, $data): void
    {
        foreach ($data as $key => $value) {
            $menu->data($key, $value);
        }
    }

    private function generateUrl($option): string
    {
        $route = $option['route'] ?? null;

        if ($route === null) {
            return url($option['url'] ?? '#');
        }

        if (is_string($route) && Route::has($route)) {
            return route($route);
        }

        if (is_array($route)) {
            [$routeName, $param] = $route;

            if (Route::has($routeName)) {
                return route($routeName, $param);
            }
        }

        return '#';
    }
}
