<?php
namespace Laravolt\Suitable\Columns;

class RestfulButton implements ColumnInterface
{

    protected $buttons = ['view', 'edit', 'delete'];

    protected $baseRoute;

    /**
     * RestfulButton constructor.
     * @param $baseRoute
     */
    public function __construct($baseRoute)
    {
        $this->baseRoute = $baseRoute;
    }


    public function header()
    {
        return '';
    }

    public function cell($data)
    {
        $view = $this->getViewUrl($data);
        $edit = $this->getEditUrl($data);
        $delete = $this->getDeleteUrl($data);
        $buttons = $this->buttons;

        return render('suitable::buttons.action', compact('view', 'edit', 'delete', 'data', 'buttons'));
    }

    public function only($buttons)
    {
        $buttons = is_array($buttons) ? $buttons : func_get_args();
        $this->buttons = array_intersect($buttons, $this->buttons);

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

}
