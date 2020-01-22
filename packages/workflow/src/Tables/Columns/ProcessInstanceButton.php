<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Tables\Columns;

use Laravolt\Suitable\Columns\RestfulButton;

class ProcessInstanceButton extends RestfulButton
{
    protected $module;

    protected $headerAttributes = ['style' => 'width:100px', 'class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'right aligned'];

    public static function make($header = null, $dummy = null)
    {
        return parent::make('workflow::process', $header);
    }

    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    public function cell($data, $collection, $loop)
    {
        return view('workflow::components.process-actions', ['module' => $this->module, 'data' => $data])->render();
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        return $this->tagAttributes((array) $this->cellAttributes);
    }
}
