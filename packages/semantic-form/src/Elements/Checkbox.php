<?php namespace Laravolt\SemanticForm\Elements;

class Checkbox extends Input
{
    protected $attributes = array(
        'type' => 'checkbox',
    );

    private $checked;

    public function __construct($name, $value = 1)
    {
        parent::__construct($name);
        $this->setValue($value);
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;
            return (new Field(new CheckboxWrapper($element, $this->label)))->render();
        }

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= '>';

        return $result;
    }

    public function defaultToChecked()
    {
        if (! isset($this->checked)) {
            $this->check();
        }

        return $this;
    }

    public function defaultToUnchecked()
    {
        if (! isset($this->checked)) {
            $this->uncheck();
        }

        return $this;
    }

    public function defaultCheckedState($state)
    {
        $state ? $this->defaultToChecked() : $this->defaultToUnchecked();
        return $this;
    }

    public function check()
    {
        $this->setChecked(true);
        return $this;
    }

    public function uncheck()
    {
        $this->setChecked(false);
        return $this;
    }

    protected function setChecked($checked = true)
    {
        $this->checked = $checked;
        $this->removeAttribute('checked');

        if ($checked) {
            $this->setAttribute('checked', 'checked');
        }
    }
}
