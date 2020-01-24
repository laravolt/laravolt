<?php

namespace Laravolt\Suitable\Toolbars;

class Action extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    protected $label = '';

    protected $icon = '';

    protected $href = '';

    /**
     * Title constructor.
     *
     * @param string $icon
     * @param string $label
     * @param string $href
     */
    public function __construct(?string $icon, ?string $label, string $href)
    {
        $this->icon = $icon;
        $this->label = $label;
        $this->href = $href;
    }

    public static function make(?string $icon, ?string $label, string $href)
    {
        $toolbar = new static($icon, $label, $href);

        return $toolbar;
    }

    public function render()
    {
        return sprintf(
            '<a href="%s" class="ui button %s %s">%s%s</a>',
            $this->href,
            collect($this->class)->implode(' '),
            $this->icon ? 'icon' : '',
            $this->icon ? "<i class='icon {$this->icon}'></i> " : '',
            $this->label
        );
    }
}
