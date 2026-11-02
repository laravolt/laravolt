<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Support\Facades\Request;
use Lavary\Menu\Builder;
use Lavary\Menu\Collection;
use Lavary\Menu\Item;

/**
 * The sidebar's Lavary builder, with cheaper lookups.
 *
 * Lavary answers $item->children() and $item->hasChildren() by scanning every
 * item of the menu, so rendering a sidebar costs O(n²) in the number of items.
 * This builder groups the items by parent once and reuses that until the item
 * list changes (an item is added, or the list is filtered, sorted or
 * replaced). Its items (SidebarItem) also share the request URLs they compare
 * themselves with, instead of rebuilding them item by item.
 *
 * Lavary's __get() resolves $builder->{name} to a property before a menu
 * nickname, so the properties here carry a prefix no menu title would.
 */
class SidebarBuilder extends Builder
{
    private ?Collection $laravoltIndexedItems = null;

    private int $laravoltIndexedCount = -1;

    /** @var array<string, list<Item>> */
    private array $laravoltChildIndex = [];

    /** @var array{0: string, 1: string}|null */
    private ?array $laravoltRequestUrls = null;

    public function add($title, $options = '')
    {
        $id = isset($options['id']) ? $options['id'] : $this->id();

        $item = new SidebarItem($this, $id, $title, $options);

        $this->items->push($item);

        return $item;
    }

    public function whereParent($parent = null, $recursive = false)
    {
        // Lavary compares parents loosely (==). For a non-numeric string, the
        // id Lavary generates for every item, that is the same as an exact
        // match, so the index gives the same answer. Anything else (roots,
        // numeric ids, recursive lookups) takes Lavary's own path.
        if ($recursive || ! is_string($parent) || $parent === '' || is_numeric($parent)) {
            return parent::__call('whereParent', [$parent, $recursive]);
        }

        return new Collection($this->childIndex()[$parent] ?? []);
    }

    /**
     * The current request's URL, with and without its query string.
     *
     * @return array{0: string, 1: string}
     */
    public function requestUrls(): array
    {
        return $this->laravoltRequestUrls ??= [Request::url(), Request::fullUrl()];
    }

    /**
     * @return array<string, list<Item>>
     */
    private function childIndex(): array
    {
        if ($this->laravoltIndexedItems === $this->items && $this->laravoltIndexedCount === $this->items->count()) {
            return $this->laravoltChildIndex;
        }

        $children = [];
        foreach ($this->items as $item) {
            $parent = $item->parent;
            if (is_string($parent) && $parent !== '' && ! is_numeric($parent)) {
                $children[$parent][] = $item;
            }
        }

        $this->laravoltChildIndex = $children;
        $this->laravoltIndexedItems = $this->items;
        $this->laravoltIndexedCount = $this->items->count();

        return $children;
    }
}
