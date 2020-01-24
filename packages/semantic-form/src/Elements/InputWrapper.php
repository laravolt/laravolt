<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

class InputWrapper extends Wrapper
{
    protected $attributes = [
        'class' => 'ui input',
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

        if ($this->getPrimaryControl()->hasError()) {
            $this->addClass('error');
        }
    }

    public function prependIcon($icon, $class = null)
    {
        $this->clearRightIcon();

        $icon = (new Icon($icon))->addClass($class);

        $this->addClass('left icon');
        $this->controlsLeft = Arr::prepend($this->controlsLeft, $icon);

        return $this;
    }

    public function appendIcon($icon, $class = null)
    {
        $this->clearLeftIcon();

        $icon = (new Icon($icon))->addClass($class);

        $this->addClass('icon');
        $this->controlsRight = Arr::prepend($this->controlsRight, $icon);

        return $this;
    }

    public function prependLabel($text, $class = null)
    {
        $this->addClass('labeled');
        $this->controlsLeft = Arr::prepend($this->controlsLeft, (new UiLabel($text))->addClass($class));

        return $this;
    }

    public function appendLabel($text, $class = null)
    {
        $this->removeClass('labeled')->addClass('right labeled');
        $this->controlsRight = Arr::prepend($this->controlsRight, (new UiLabel($text))->addClass($class));

        return $this;
    }

    public function appendButton($text, $class = null)
    {
        $this->removeClass('labeled')->addClass('action');
        $this->controlsRight = Arr::prepend($this->controlsRight, (new Button($text, null))->addClass($class));

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
        $this->removeClass('left icon');

        foreach ($this->controlsLeft as $key => $control) {
            if ($control instanceof Icon) {
                unset($this->controlsLeft[$key]);
            }
        }
    }

    protected function clearRightIcon()
    {
        $this->removeClass('icon');

        foreach ($this->controlsRight as $key => $control) {
            if ($control instanceof Icon) {
                unset($this->controlsRight[$key]);
            }
        }
    }
}
