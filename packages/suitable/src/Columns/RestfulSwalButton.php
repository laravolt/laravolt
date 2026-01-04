<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RestfulSwalButton extends RestfulButton
{
    protected $header = 'Action';

    protected $additionalButton;

    public function getHeader()
    {
        return $this->header ?: 'Action';
    }

    public function cell($data, $collection, $loop)
    {
        $actions = $this->buildActions($data);
        $deleteConfirmation = $this->buildDeleteConfirmation($data);
        $key = Str::kebab(get_class($data)).'-'.$data->getKey();
        $additionalButton = $this->buildAdditionalButton($data);

        return View::make('suitable::columns.restful_swal_button', compact('data', 'actions', 'deleteConfirmation', 'key', 'additionalButton'))
            ->render();
    }

    public function additionalButton($label, $target)
    {
        $this->additionalButton = [
            'label' => $label,
            'target' => $target,
        ];

        return $this;
    }

    protected function buildAdditionalButton($data)
    {
        return $this->additionalButton;
    }
}
