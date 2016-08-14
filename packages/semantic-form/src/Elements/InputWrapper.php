<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

class InputWrapper extends Wrapper
{
    protected $attributes = [
        'class' => 'ui input'
    ];

    protected $controlsLeft = [];

    protected $controlsRight = [];

    public function __construct()
    {
        parent::__construct();
        if (empty($this->controls)) {
            $this->controls[] = new Text('');
        }
    }

    protected function beforeRender()
    {
        $this->controls = array_merge(array_merge($this->controlsLeft, $this->controls), $this->controlsRight);
    }

    public function prependIcon($icon)
    {
        $this->clearRightIcon();

        $this->addClass('left icon');
        $this->controlsLeft = array_prepend($this->controlsLeft, new Icon($icon));

        return $this;
    }

    public function appendIcon($icon)
    {
        $this->clearLeftIcon();

        $this->addClass('icon');
        $this->controlsRight = array_prepend($this->controlsRight, new Icon($icon));

        return $this;
    }

    protected function clearLeftIcon()
    {
        $this->removeClass('left icon');

        foreach($this->controlsLeft as $key => $control) {
            if ($control instanceof  Icon) {
                unset($this->controlsLeft[$key]);
            }
        }
    }

    protected function clearRightIcon()
    {
        $this->removeClass('icon');

        foreach($this->controlsRight as $key => $control) {
            if ($control instanceof  Icon) {
                unset($this->controlsRight[$key]);
            }
        }
    }
}
