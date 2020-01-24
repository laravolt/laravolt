<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class FormOpen extends Element
{
    protected $attributes = [
        'method' => 'POST',
        'action' => '',
        'class'  => 'ui form',
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

    protected function hasToken()
    {
        return (bool) $this->token;
    }

    protected function hasHiddenMethod()
    {
        return (bool) $this->hiddenMethod;
    }

    public function post()
    {
        $this->setMethod('POST');

        return $this;
    }

    public function get()
    {
        $this->setMethod('GET');
        $this->withoutToken();

        return $this;
    }

    public function put()
    {
        return $this->setHiddenMethod('PUT');
    }

    public function patch()
    {
        return $this->setHiddenMethod('PATCH');
    }

    public function delete()
    {
        return $this->setHiddenMethod('DELETE');
    }

    public function withoutToken()
    {
        $this->withToken = false;

        return $this;
    }

    public function setHiddenMethod($method)
    {
        $this->setMethod('POST');
        $this->hiddenMethod = $method;

        return $this;
    }

    public function setMethod($method)
    {
        $this->setAttribute('method', $method);

        return $this;
    }

    public function action($action)
    {
        $this->setAttribute('action', $action);

        return $this;
    }

    public function route($route, $parameters = [], $absolute = true)
    {
        return $this->action(URL::route($route, $parameters, $absolute));
    }

    public function url($url)
    {
        return $this->action($url);
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

    public function horizontal()
    {
        $this->addClass('horizontal');

        return $this;
    }
}
