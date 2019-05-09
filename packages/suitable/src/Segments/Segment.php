<?php

namespace Laravolt\Suitable\Segments;

class Segment
{
    protected $key;

    protected $left = [];

    protected $right = [];

    /**
     * Segment constructor.
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    static public function make($key)
    {
        return new static($key);
    }

    public function left($toolbars)
    {
        $this->left = is_array($toolbars) ? $toolbars : func_get_args();

        return $this;
    }

    public function addLeft($toolbar)
    {
        $this->left[] = $toolbar;

        return $this;
    }

    public function right($toolbars)
    {
        $this->right = is_array($toolbars) ? $toolbars : func_get_args();

        return $this;
    }

    public function addRight($toolbar)
    {
        $this->right[] = $toolbar;

        return $this;
    }

    public function render()
    {
        return view('suitable::segments.segment', ['left' => $this->left, 'right' => $this->right])->render();
    }
}
