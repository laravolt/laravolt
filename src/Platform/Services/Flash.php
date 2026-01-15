<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\Store;
use Illuminate\Support\ViewErrorBag;

class Flash
{
    protected Store $session;

    protected Factory $view;

    protected string $sessionKey = 'laravolt_flash';

    protected bool $now = false;

    protected FlashAttributes $attributes;

    protected array $bags = [];

    protected mixed $except = [];

    /**
     * Flash constructor.
     */
    public function __construct(Store $session, Factory $view)
    {
        $this->session = $session;
        $this->view = $view;
        $this->attributes = new FlashAttributes;
        $this->except = config('laravolt.ui.flash.except');
    }

    public function message($message, $type = 'basic'): static
    {
        $this->attributes->setMessage($message, $type);
        $this->bags[] = $this->attributes;

        $method = $this->now ? 'now' : 'flash';

        $this->session->$method($this->sessionKey, $this->bags);
        $this->now = false;

        return $this;
    }

    public function info($message): static
    {
        return $this->message($message, 'info');
    }

    public function success($message): static
    {
        return $this->message($message, 'success');
    }

    public function warning($message): static
    {
        return $this->message($message, 'warning');
    }

    public function error($message): static
    {
        return $this->message($message, 'error');
    }

    public function injectScript(Response $response): void
    {
        $content = $response->getContent();
        $pos = mb_strripos($content, '</main>');

        $bags = $this->session->get($this->getSessionKey());

        usort($bags, static function ($item, $next) {
            if ($item['class'] === 'error') {
                return 1;
            }

            return 0;
        });

        $script = $this->view->make('laravolt::components.flash', compact('bags'))->render();

        if ($pos !== false) {
            $content = mb_substr($content, 0, $pos).$script.mb_substr($content, $pos);
        } else {
            $content .= $script;
        }

        $response->setContent($content);
    }

    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    public function now(): static
    {
        $this->now = true;

        return $this;
    }

    public function hasMessage(): bool
    {
        return ! empty($this->bags);
    }

    public function inExceptArray(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = mb_trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    public function getBags()
    {
        $session = session();

        if ($session->has('errors')) {
            $message = $session->get('errors');

            if ($message instanceof ViewErrorBag) {
                $errors = collect($message->unique());
                if ($errors->count() > 1) {
                    $message = '<ul class="list-disc list-inside space-y-1">'.
                              $errors->map(fn ($error) => '<li>'.$error.'</li>')->implode('').
                              '</ul>';
                } else {
                    $message = $errors->first();
                }
            }

            $this->error($message);
        }

        if ($message = $session->get('success')) {
            $this->success($message);
        }

        if ($message = $session->get('warning')) {
            $this->warning($message);
        }

        if ($message = $session->get('info')) {
            $this->info($message);
        }

        if ($message = $session->get('message')) {
            $this->message($message);
        }

        if ($message = $session->get('error')) {
            $this->error($message);
        }

        return $this->bags;
    }
}
