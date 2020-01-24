<?php

namespace Laravolt\SemanticForm\Elements;

abstract class FormControl extends Element
{
    protected $hasError = false;

    protected $value;

    public function __construct($name)
    {
        $this->setName($name);
    }

    protected function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function readonly($readonly = true)
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
        } else {
            $this->removeAttribute('readonly');
        }

        return $this;
    }

    public function required($required = true)
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }

        return $this;
    }

    public function optional()
    {
        $this->removeAttribute('required');

        return $this;
    }

    public function disable($disable = true)
    {
        if ($disable) {
            $this->setAttribute('disabled', 'disabled');
        } else {
            $this->removeAttribute('disabled');
        }

        return $this;
    }

    public function enable($enable = true)
    {
        $this->disable(!$enable);

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

    public function getValue()
    {
        return $this->value ?? $this->getAttribute('value');
    }
}
