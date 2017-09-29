<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class RestfulButton implements ColumnInterface
{

    protected $buttons = ['view', 'edit', 'delete'];

    protected $baseRoute;

    protected $deleteConfirmation;

    /**
     * RestfulButton constructor.
     * @param $baseRoute
     */
    public function __construct($baseRoute)
    {
        $this->baseRoute = $baseRoute;
        $this->deleteConfirmation = config('suitable.restul_button.delete_message');
    }


    public function header()
    {
        return '';
    }

    public function headerAttributes()
    {
        return null;
    }

    public function cell($data)
    {
        $view = $this->getViewUrl($data);
        $edit = $this->getEditUrl($data);
        $delete = $this->getDeleteUrl($data);
        $deleteConfirmation = $this->buildDeleteConfirmation($data);
        $buttons = $this->buttons;

        return View::make('suitable::columns.restful_button', compact('view', 'edit', 'delete', 'data', 'buttons', 'deleteConfirmation'))->render();
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

    protected function getViewUrl($data)
    {
        if (in_array('view', $this->buttons)) {
            return $this->getRoute('show', $data->id);
        }

        return false;
    }

    protected function getEditUrl($data)
    {
        if (in_array('edit', $this->buttons)) {
            return $this->getRoute('edit', $data->id);
        }

        return false;
    }

    protected function getDeleteUrl($data)
    {
        if (in_array('delete', $this->buttons)) {
            return $this->getRoute('destroy', $data->id);
        }

        return false;
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
}
