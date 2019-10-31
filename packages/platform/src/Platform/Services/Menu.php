<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Lavary\Menu\Item;
use Lavary\Menu\Menu as BaseMenu;

class Menu extends BaseMenu
{
    public static function setVisible($children, $visible = 'visible')
    {
        foreach ($children as $child) {
            if ($child->link->isActive) {
                return $visible;
            }
        }

        return $visible = '';
    }

    public static function setActiveParent($children, $isActive, $active = 'active current')
    {
        if ($isActive) {
            return $active;
        } else {
            foreach ($children as $child) {
                if ($child->link->isActive) {
                    return $active;
                }
            }
        }

        return $active = '';
    }

    public function all()
    {
        $sidebar = $this->get('sidebar');

        $items = $sidebar->all()->map(function (Item $item) {
            $item->data('is-parent', $item->hasChildren() || (!$item->hasChildren() && !$item->link->path['url']));

            return $item;
        });
        $sidebar->takeCollection($items);
        $sidebar->filter(function (Item $item) {
            return $this->filterByVisibility($item);
        })->filter(function (Item $item) {
            if ($item->data('is-parent') && !$item->hasChildren()) {
                return false;
            }

            return true;
        });

        return $this->get('sidebar')->topMenu()->all();
    }

    protected function filterByVisibility(Item $item)
    {
        // If menu doesn't define permission, we assume this menu visible to everyone
        // Otherwise, check if current user has access
        return $item->data('permission') === null || auth()->user()->can($item->data('permission'));
    }
}
