<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

class RestfulButton extends Column implements ColumnInterface
{

    protected $buttons = ['view', 'edit', 'delete'];

    protected $baseRoute;

    protected $header;

    protected $deleteConfirmation;

    /**
     * RestfulButton constructor.
     * @param $baseRoute
     */
    public function __construct($baseRoute, $header = null)
    {
        $this->baseRoute = $baseRoute;
        $this->header = $header;
        $this->deleteConfirmation = config('suitable.restul_button.delete_message');
    }

    static public function make($baseRoute, $header = null)
    {
        $column = new static($baseRoute, $header);
        $column->id = snake_case($header);

        return $column;
    }

    public function header()
    {
        return sprintf('<th %s>%s</th>', $this->generateAttributes($this->headerAttributes), $this->header);
    }

    public function headerAttributes()
    {
        return null;
    }

    public function cell($data, $collection, $loop)
    {
        $actions = $this->buildActions($data);
        $deleteConfirmation = $this->buildDeleteConfirmation($data);

        return View::make('suitable::columns.restful_button', compact('data', 'actions', 'deleteConfirmation'))->render();
    }

    public function cellAttributes($cell)
    {
        return null;
    }

    public function only($buttons)
    {
        $buttons = is_array($buttons) ? $buttons : func_get_args();
        $this->buttons = array_intersect($buttons, $this->buttons);

        return $this;
    }

    public function deleteConfirmation($message)
    {
        $this->deleteConfirmation = $message;

        return $this;
    }

    protected function getRoute($verb, $param = null)
    {
        if ($this->baseRoute) {
            return route($this->baseRoute.'.'.$verb, $param);
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

        if ($message = config('suitable.restful_button.delete_confirmation_auto')) {
            $fields = config('suitable.restful_button.delete_confirmation_fields');

            foreach ($fields as $field) {
                if($value = array_get($data, $field)) {
                    return str_replace(':item', $value, $message);
                }
            }
        }

        return config('suitable.restful_button.delete_confirmation');
    }

    protected function buildActions($data)
    {
        $actions = [
            'view'   => $this->getRoute('show', $data->getKey()),
            'edit'   => $this->getRoute('edit', $data->getKey()),
            'delete' => $this->getRoute('destroy', $data->getKey()),
        ];

        $class = get_class($data);
        $policyEnabled = Gate::getPolicyFor($class) !== null;

        $actions = collect($actions)
            ->reject(
                function ($url, $action) use ($policyEnabled, $data) {
                    return ($policyEnabled && Auth::user()->cannot($action, $data)) || !in_array($action, $this->buttons);
                }
            );

        return $actions;
    }
}
