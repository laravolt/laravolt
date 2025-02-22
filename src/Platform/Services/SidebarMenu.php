<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Lavary\Menu\Builder;
use Lavary\Menu\Item;
use Lavary\Menu\Menu as BaseMenu;

class SidebarMenu extends BaseMenu
{
    protected $callbacksCore = [];

    protected $callbacks = [];

    public function registerCore(\Closure $callback)
    {
        $this->callbacksCore[] = $callback;
    }

    public function register(\Closure $callback)
    {
        $this->callbacks[] = $callback;
    }

    public static function setVisible($children, $visible = 'visible')
    {
        foreach ($children as $child) {
            if ($child->link->isActive) {
                return $visible;
            }
        }

        return '';
    }

    public static function setActiveParent($children, $isActive, $active = 'active selected')
    {
        // TODO: return '' if frankenphp worker mode, but still dunno how to detect it

        if ($isActive) {
            return $active;
        } else {
            foreach ($children as $child) {
                if ($child->link->isActive) {
                    return $active;
                }
            }
        }

        return '';
    }

    public function all()
    {
        /** @var \Lavary\Menu\Builder $sidebar */
        $sidebar = app('laravolt.menu.sidebar')->make(
            'sidebar',
            function (Builder $menu) {
                return $menu;
            }
        );

        foreach ($this->callbacksCore as $callback) {
            call_user_func($callback, $sidebar);
        }

        foreach ($this->callbacks as $callback) {
            call_user_func($callback, $sidebar);
        }

        /** @var \Lavary\Menu\Collection */
        $sidebarAll = $sidebar->all();
        $items = $sidebarAll->map(function (Item $item) {
            $item->data('is-parent', $item->hasChildren() || (! $item->hasChildren() && ! ($item->link->path['url'] ?? true)));

            if ($item->data('icon') === null) {
                return $item->data('icon', config('laravolt.ui.default_menu_icon'));
            }

            return $item;
        });
        $sidebar->takeCollection($items);
        $sidebar
            ->filter(function (Item $item) {
                return $this->filterByVisibility($item);
            })
            ->filter(function (Item $item) {
                if ($item->data('is-parent') && ! $item->hasChildren()) {
                    return false;
                }

                return true;
            });

        /** @var \Lavary\Menu\Collection */
        $result = $this->get('sidebar')->topMenu()->all();
        $result = $result->sortBy(function (Item $item) {
            if ($item->title === 'System') {
                $item->data('order', 99);
            }

            return $item->data('order');
        });

        return $result;
    }

    protected function filterByVisibility(Item $item)
    {
        $permission = $item->data('permissions') ?? $item->data('permission');
        $roles = $item->data('roles');
        $permissionCheck = $rolesCheck = true;

        if ($roles) {
            $rolesCheck = auth()->user()->hasRole($roles);
        }

        // If permission defined, we assume User doesn't allowed to access Menu,
        // until she proved that she has the access
        if ($permission) {
            $permissionCheck = false;
            // If it was multiple permissions, we check using OR conditions.
            // It means, user only need to have one of the permissions
            if (is_array($permission)) {
                foreach ($permission as $perm) {
                    if (auth()->check() && auth()->user()->can($perm)) {
                        $permissionCheck = true;
                        break;
                    }
                }
            } else {
                $permissionCheck = auth()->check() && auth()->user()->can($permission);
            }
        }

        return $permissionCheck && $rolesCheck;
    }
}
