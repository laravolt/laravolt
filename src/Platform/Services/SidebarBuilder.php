<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Lavary\Menu\Builder;
use Lavary\Menu\Collection;
use Lavary\Menu\Item;

/**
 * The sidebar's Lavary builder, with children looked up through an index.
 *
 * Lavary answers $item->children() and $item->hasChildren() by scanning every
 * item of the menu, so rendering a sidebar costs O(n²) in the number of items.
 * This builder groups the items by parent once and reuses that until the item
 * list changes (an item is added, or the list is filtered, sorted or
 * replaced).
 */
class SidebarBuilder extends Builder
{
    private ?Collection $indexedItems = null;

    private int $indexedCount = -1;

    /** @var array<string, list<Item>> */
    private array $children = [];

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
     * @return array<string, list<Item>>
     */
    private function childIndex(): array
    {
        if ($this->indexedItems === $this->items && $this->indexedCount === $this->items->count()) {
            return $this->children;
        }

        $children = [];
        foreach ($this->items as $item) {
            $parent = $item->parent;
            if (is_string($parent) && $parent !== '' && ! is_numeric($parent)) {
                $children[$parent][] = $item;
            }
        }

        $this->children = $children;
        $this->indexedItems = $this->items;
        $this->indexedCount = $this->items->count();

        return $children;
    }
}
