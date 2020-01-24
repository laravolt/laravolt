<?php

namespace Laravolt\Suitable\Segments;

class Segment
{
    protected $key;

    protected $left = [];

    protected $right = [];

    /**
     * Segment constructor.
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public static function make($key)
    {
        return new static($key);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function left($toolbars)
    {
        $this->left = is_array($toolbars) ? $toolbars : func_get_args();

        return $this;
    }

    /**
     * @param $toolbar
     *
     * @return $this
     *
     * @deprecated
     */
    public function addLeft($toolbar)
    {
        return $this->appendLeft($toolbar);
    }

    public function appendLeft($toolbar)
    {
        array_push($this->left, $toolbar);

        return $this;
    }

    public function prependLeft($toolbar)
    {
        array_unshift($this->left, $toolbar);

        return $this;
    }

    public function right($toolbars)
    {
        $this->right = is_array($toolbars) ? $toolbars : func_get_args();

        return $this;
    }

    /**
     * @param $toolbar
     *
     * @return $this
     *
     * @deprecated
     */
    public function addRight($toolbar)
    {
        return $this->appendRight($toolbar);
    }

    public function appendRight($toolbar)
    {
        array_push($this->right, $toolbar);

        return $this;
    }

    public function prependRight($toolbar)
    {
        array_unshift($this->right, $toolbar);

        return $this;
    }

    public function isEmpty()
    {
        return empty($this->left) && empty($this->right);
    }

    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    public function render()
    {
        return view('suitable::segments.segment', ['left' => $this->left, 'right' => $this->right])->render();
    }
}
