<?php
namespace Laravolt\Suitable;

class Header
{
    protected $sortable = false;

    protected $html;

    protected $attributes;

    /**
     * @return boolean
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @param boolean $sortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param mixed $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string HTML
     */
    public function renderAttributes()
    {
        $html = '';

        if (!is_array($this->attributes)) {
            return $html;
        }

        foreach ($this->attributes as $attribute => $value) {
            $html .= " {$attribute}=\"{$value}\"";
        }

        return $html;
    }

}
