<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Lavary\Menu\Item;

/**
 * A sidebar menu item. Activates itself by URL like a Lavary item, but asks
 * the builder for the request URLs (built once per menu) and resolves its own
 * URL once instead of twice.
 */
class SidebarItem extends Item
{
    private bool $laravoltNoUrlActivation;

    public function __construct($builder, $id, $title, $options)
    {
        // Lavary keeps this flag private, and its constructor already needs it.
        $this->laravoltNoUrlActivation = is_array($options)
            && isset($options['disableActivationByURL'])
            && $options['disableActivationByURL'] == true;

        parent::__construct($builder, $id, $title, $options);
    }

    public function checkActivationStatus()
    {
        if ($this->laravoltNoUrlActivation
            || $this->builder->conf('restful') == true
            || ! $this->builder instanceof SidebarBuilder
        ) {
            return parent::checkActivationStatus();
        }

        [$url, $fullUrl] = $this->builder->requestUrls();
        $own = $this->url();

        if ($own == $url || $own == $fullUrl) {
            $this->activate();
        }
    }
}
