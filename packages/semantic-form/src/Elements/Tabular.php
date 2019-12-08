<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Str;

class Tabular extends Element
{
    protected $schema = [];

    protected $labels = [];

    protected $limit = 3;

    protected $allowAddition = false;

    protected $allowRemoval = false;

    public function __construct($schema)
    {
        $this->schema = collect($schema)->transform(function ($item) {
            $item['name'] = Str::endsWith($item['name'], '[]') ? $item['name'] : $item['name'].'[%s]';

            return $item;
        })->toArray();
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function allowAddition(bool $flag)
    {
        $this->allowAddition = $flag;

        return $this;
    }

    public function allowRemoval(bool $flag)
    {
        $this->allowRemoval = $flag;

        return $this;
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $this->beforeRender();

        $fields = collect(form()->make($this->schema)->all())
            ->transform(function ($item) {
                $this->labels[] = (string) $item->label;
                $item->label(null);

                return $item;
            });

        $rows = [];
        for ($i = 0; $i < $this->limit; $i++) {
            $rows[] = $fields->map(function ($field) use ($i) {
                $newField = clone $field;
                $newField->bindAttribute('name', $i);
                $newField->populateValue(old());

                return $newField;
            });
        }

        $data = [
            'rows' => $rows,
            'labels' => $this->labels,
            'limit' => $this->limit,
            'allowAddition' => $this->allowAddition,
            'allowRemoval' => $this->allowRemoval,
        ];

        return view('semantic-form::tabular', $data)->render();
    }
}
