<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

class InputWrapper extends Wrapper
{
    protected $attributes = [
        'class' => 'relative',
    ];

    protected $controlsLeft = [];

    protected $controlsRight = [];

    public function __construct()
    {
        $this->controls = func_get_args();

        if (empty($this->controls)) {
            $this->controls[] = new Text('');
        }
    }

    protected function beforeRender()
    {
        $this->controls = array_merge(array_merge($this->controlsLeft, $this->controls), $this->controlsRight);

        // Error state can be reflected via Field wrapper; keep container neutral
    }

    public function prependIcon($icon, $class = null)
    {
        $this->clearRightIcon();

        $icon = (new Icon($icon))
            ->addClass(trim('absolute inset-y-0 start-0 ps-3 flex items-center pointer-events-none text-gray-400 '.$class));

        $this->controlsLeft = Arr::prepend($this->controlsLeft, $icon);

        return $this;
    }

    public function appendIcon($icon, $class = null)
    {
        $this->clearLeftIcon();

        $icon = (new Icon($icon))
            ->addClass(trim('absolute inset-y-0 end-0 pe-3 flex items-center pointer-events-none text-gray-400 '.$class));

        $this->controlsRight = Arr::prepend($this->controlsRight, $icon);

        return $this;
    }

    public function prependLabel($text, $class = null)
    {
        $this->controlsLeft = Arr::prepend($this->controlsLeft, (new UiLabel($text))->addClass(trim('inline-flex items-center rounded-s-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 '.$class)));

        return $this;
    }

    public function appendLabel($text, $class = null)
    {
        $this->controlsRight = Arr::prepend($this->controlsRight, (new UiLabel($text))->addClass(trim('inline-flex items-center rounded-e-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 '.$class)));

        return $this;
    }

    public function appendButton($text, $class = null)
    {
        $this->controlsRight = Arr::prepend($this->controlsRight, (new Button($text, null))->addClass(trim('inline-flex items-center rounded-e-lg border border-transparent bg-gray-800 text-white px-3 py-2 text-sm hover:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-gray-600 '.$class)));

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->getPrimaryControl()->placeholder($placeholder);

        return $this;
    }

    public function type($type)
    {
        $this->getPrimaryControl()->type($type);

        return $this;
    }

    public function required($required = true)
    {
        $this->getPrimaryControl()->required($required);

        return $this;
    }

    public function optional()
    {
        $this->getPrimaryControl()->optional();

        return $this;
    }

    public function disable($disable = true)
    {
        $this->getPrimaryControl()->disable($disable);

        return $this;
    }

    public function enable($enable = true)
    {
        $this->getPrimaryControl()->enable($enable);

        return $this;
    }

    public function autofocus()
    {
        $this->getPrimaryControl()->autofocus();

        return $this;
    }

    public function unfocus()
    {
        $this->getPrimaryControl()->unfocus();

        return $this;
    }

    public function isRequired()
    {
        return $this->getPrimaryControl()->getAttribute('required');
    }

    public function hasError()
    {
        return $this->getPrimaryControl()->hasError();
    }

    protected function clearLeftIcon()
    {
        // no-op, Preline uses absolute positioned icons

        foreach ($this->controlsLeft as $key => $control) {
            if ($control instanceof Icon) {
                unset($this->controlsLeft[$key]);
            }
        }
    }

    protected function clearRightIcon()
    {
        // no-op, Preline uses absolute positioned icons

        foreach ($this->controlsRight as $key => $control) {
            if ($control instanceof Icon) {
                unset($this->controlsRight[$key]);
            }
        }
    }
}
