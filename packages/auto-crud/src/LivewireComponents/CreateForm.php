<?php

namespace Laravolt\AutoCrud\LivewireComponents;

use Laravolt\Fields\Field;
use Laravolt\Ui\Modal;

class CreateForm extends Modal
{
    public string $resource;

    public function render()
    {
        $config = config()->get("laravolt.auto-crud.resources.{$this->resource}");
        $fields = collect($config['schema'])
            ->filter(
                function ($item) {
                    if ($item instanceof Field) {
                        return $item->visibleFor('create');
                    }

                    return ($item['visibility']['create'] ?? true);
                }
            );

        return view('laravolt::auto-crud.create', compact('config', 'fields'));
    }
}
