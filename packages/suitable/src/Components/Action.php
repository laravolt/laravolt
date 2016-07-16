<?php
namespace Laravolt\Suitable\Components;

use Laravolt\Suitable\Components\Component as BaseComponent;
use Laravolt\Suitable\Contracts\Component as ComponentContract;

class Action extends BaseComponent implements ComponentContract
{

    protected $buttons = ['view', 'edit', 'delete'];


    public function header()
    {
        return 'Aksi';
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
            return $this->builder->getRoute('show', $data->id);
        }

        return false;
    }

    protected function getEditUrl($data)
    {
        if (in_array('edit', $this->buttons)) {
            return $this->builder->getRoute('edit', $data->id);
        }

        return false;
    }

    protected function getDeleteUrl($data)
    {
        if (in_array('delete', $this->buttons)) {
            return $this->builder->getRoute('destroy', $data->id);
        }

        return false;
    }

}
