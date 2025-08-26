<?php

namespace Laravolt\PrelineForm\Elements;

use Illuminate\Support\Facades\Session;

class FormOpen extends Element
{
    protected $attributes = [
        'method' => 'POST',
        'action' => '',
        'class' => 'space-y-6',
    ];

    protected $withToken = true;

    protected $hiddenMethod;

    /**
     * FormOpen constructor.
     */
    public function __construct($action = null)
    {
        $this->action($action);

        return $this;
    }

    public function render()
    {
        $result = '<form';
        $result .= $this->renderAttributes();
        $result .= '>';

        if ($this->withToken) {
            $tokenField = new Hidden('_token');
            $tokenField->value(Session::token());

            $result .= $tokenField->render();
        }

        if ($this->hasHiddenMethod()) {
            $hiddenField = new Hidden('_method');
            $hiddenField->value($this->hiddenMethod);

            $result .= $hiddenField->render();
        }

        return $result;
    }

    public function withoutToken()
    {
        $this->withToken = false;

        return $this;
    }

    public function action($action)
    {
        if ($action) {
            $this->setAttribute('action', $action);
        }

        return $this;
    }

    public function url($url)
    {
        return $this->action($url);
    }

    public function route($route, $parameters = [])
    {
        return $this->action(route($route, $parameters));
    }

    public function get()
    {
        return $this->method('GET');
    }

    public function post()
    {
        return $this->method('POST');
    }

    public function put()
    {
        return $this->method('PUT');
    }

    public function patch()
    {
        return $this->method('PATCH');
    }

    public function delete()
    {
        return $this->method('DELETE');
    }

    protected function method($method)
    {
        $allowedMethods = ['GET', 'POST'];

        if (in_array($method, $allowedMethods)) {
            $this->setAttribute('method', $method);
        } else {
            $this->setAttribute('method', 'POST');
            $this->hiddenMethod = $method;
        }

        return $this;
    }

    protected function hasHiddenMethod()
    {
        return ! is_null($this->hiddenMethod);
    }
<<<<<<< Current (Your changes)
=======

    public function horizontal()
    {
        // For Preline UI, we can add specific classes for horizontal form layout
        $this->addClass('horizontal');

        return $this;
    }

    public function encodingType($type)
    {
        $this->setAttribute('enctype', $type);

        return $this;
    }

    public function multipart()
    {
        return $this->encodingType('multipart/form-data');
    }
>>>>>>> Incoming (Background Agent changes)
}
