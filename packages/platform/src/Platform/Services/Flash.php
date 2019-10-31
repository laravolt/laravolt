<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Support\Str;

class Flash
{
    protected $session;

    protected $view;

    protected $sessionKey = 'laravolt_flash';

    protected $now = false;

    protected $attributes = [
        'message' => null,
        'class' => 'info',
        'closeIcon' => false,
        'displayTime' => 5000,
        'opacity' => 0.9,
        'position' => 'top center',
        'compact' => false,
        'newestOnTop' => true,
    ];

    protected $bags = [];

    protected $except = [];

    /**
     * Flash constructor.
     *
     * @param Store $session
     * @param View  $view
     */
    public function __construct(Store $session, Factory $view)
    {
        $this->session = $session;
        $this->view = $view;
        $this->attributes = $this->defaultAttributes() + $this->attributes;
        $this->except = config('laravolt.ui.flash.except');
    }

    public function message($message, $type = 'basic')
    {
        $this->attributes['message'] = $message;
        $this->attributes['class'] = $type;

        $this->bags[] = $this->attributes;

        $method = $this->now ? 'now' : 'flash';
        $this->session->$method($this->sessionKey, $this->bags);
        $this->now = false;

        return $this;
    }

    public function info($message)
    {
        return $this->message($message, 'info');
    }

    public function success($message)
    {
        return $this->message($message, 'success');
    }

    public function warning($message)
    {
        return $this->message($message, 'warning');
    }

    public function error($message)
    {
        return $this->message($message, 'error');
    }

    public function injectScript(Response $response)
    {
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        $bags = $this->session->get($this->getSessionKey());

        usort($bags, function ($item, $next) {
            if ($item['class'] == 'error') {
                return 1;
            }

            return 0;
        });

        $script = $this->view->make('ui::flash', compact('bags'))->render();

        if (false !== $pos) {
            $content = substr($content, 0, $pos).$script.substr($content, $pos);
        } else {
            $content = $content.$script;
        }

        $response->setContent($content);
    }

    public function getSessionKey()
    {
        return $this->sessionKey;
    }

    public function now()
    {
        $this->now = true;

        return $this;
    }

    public function hasMessage()
    {
        return !empty($this->bags);
    }

    public function inExceptArray(Request $request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    private function defaultAttributes()
    {
        return collect(config('laravolt.ui.flash.attributes'))->mapWithKeys(function ($item, $key) {
            return [Str::camel($key) => $item];
        })->toArray();
    }
}
