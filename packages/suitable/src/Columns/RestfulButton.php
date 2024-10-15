<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RestfulButton extends Column implements ColumnInterface
{
    protected $buttons = ['show', 'edit', 'destroy'];

    protected $baseRoute;

    protected $header;

    protected $routeParameters = [];

    protected $deleteConfirmation;

    protected $cellAttributes = ['class' => 'right aligned'];

    /**
     * RestfulButton constructor.
     */
    public function __construct($baseRoute, $header = null)
    {
        $this->baseRoute = $baseRoute;
        $this->header = $header;
        $this->deleteConfirmation = config('suitable.restul_button.delete_message');
    }

    public static function make($baseRoute, $header = null)
    {
        $column = new static($baseRoute, $header);
        $column->id = Str::snake($header);

        return $column;
    }

    public function cell($data, $collection, $loop)
    {
        $actions = $this->buildActions($data);
        $deleteConfirmation = $this->buildDeleteConfirmation($data);
        $key = Str::kebab(get_class($data)).'-'.$data->getKey();

        return View::make('suitable::columns.restful_button', compact('data', 'actions', 'deleteConfirmation', 'key'))
            ->render();
    }

    public function only($buttons)
    {
        $buttons = is_array($buttons) ? $buttons : func_get_args();
        $this->buttons = array_intersect($buttons, $this->buttons);

        return $this;
    }

    public function except($buttons)
    {
        $buttons = is_array($buttons) ? $buttons : func_get_args();
        $this->buttons = array_diff($this->buttons, $buttons);

        return $this;
    }

    public function deleteConfirmation($message)
    {
        $this->deleteConfirmation = $message;

        return $this;
    }

    public function routeParameters($param)
    {
        $this->routeParameters = $param;

        return $this;
    }

    protected function getRoute($action, $param = null)
    {
        if ($this->baseRoute) {
            return route($this->baseRoute.'.'.$action, $param);
        }

        return false;
    }

    protected function buildDeleteConfirmation($data)
    {
        if (is_string($this->deleteConfirmation)) {
            return $this->deleteConfirmation;
        }

        if ($this->deleteConfirmation instanceof \Closure) {
            return call_user_func($this->deleteConfirmation, $data);
        }

        if ($message = trans('suitable::suitable.delete_confirmation_auto')) {
            $fields = config('suitable.restful_button.delete_confirmation_fields');

            foreach ($fields as $field) {
                if ($value = Arr::get($data, $field)) {
                    return str_replace(':item', $value, $message);
                }
            }
        }

        return trans('suitable::suitable.delete_confirmation');
    }

    protected function buildActions($data)
    {
        $actions = ['show', 'edit', 'destroy'];

        $actions = collect($actions)
            ->reject(
                function ($action) {
                    return ! in_array($action, $this->buttons);
                }
            )->reject(function ($action) use ($data) {
                if (Auth::user() && Auth::user()->hasPermission('*')) {
                    return false;
                }

                $policyEnabled = Gate::getPolicyFor(get_class($data)) !== null;

                return $policyEnabled && Auth::user()->cannot($action, $data);
            })
            ->mapWithKeys(function ($action) use ($data) {

                return [$action => $this->getRoute($action, $this->routeParameters + [$data->getKey()])];
            });

        return $actions;
    }
}
