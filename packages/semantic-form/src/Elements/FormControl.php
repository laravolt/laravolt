<?php namespace Laravolt\SemanticForm\Elements;

abstract class FormControl extends Element
{

    protected $hasError = false;

    public function __construct($name)
    {
        $this->setName($name);
    }

    protected function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function required()
    {
        $this->setAttribute('required', 'required');

        return $this;
    }

    public function optional()
    {
        $this->removeAttribute('required');

        return $this;
    }

    public function disable()
    {
        $this->setAttribute('disabled', 'disabled');

        return $this;
    }

    public function enable()
    {
        $this->removeAttribute('disabled');

        return $this;
    }

    public function autofocus()
    {
        $this->setAttribute('autofocus', 'autofocus');

        return $this;
    }

    public function unfocus()
    {
        $this->removeAttribute('autofocus');

        return $this;
    }

    public function setError($error = true)
    {
        $this->hasError = $error;

        return $this;
    }

    public function hasError()
    {
        return $this->hasError;
    }

}
