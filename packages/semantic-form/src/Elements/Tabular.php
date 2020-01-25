<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DeepCopy\DeepCopy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Tabular extends Element
{
    protected $schema = [];

    protected $labels = [];

    protected $rows = 3;

    protected $allowAddition = false;

    protected $allowRemoval = false;

    protected $name;

    protected $source = [];

    public function __construct($name, $schema)
    {
        $this->name = $name;

        $this->schema = collect($schema)->transform(function ($item) {

            // produce something like "person[data][$index][name]", to make it easier to handling by Laravel Request
            if (Str::contains($item['name'], '[]')) {
                $name = Str::before($item['name'], '[]');
                $item['name'] = "{$this->name}[%s][{$name}][]";
            } else {
                $item['name'] = "{$this->name}[%s][{$item['name']}]";
            }

            return $item;
        })->toArray();
    }

    public function rows(int $rows)
    {
        $this->rows = $rows;

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

    public function source($source)
    {
        $this->source = $source;

        return $this;
    }

    public function displayValue()
    {
        $data = $this->source;
        $headers = collect(Arr::first($data))->keys();

        return view('semantic-form::tabular.table', compact('data', 'headers'));
    }

    public function render()
    {
        if ($this->label) {
            $copier = new DeepCopy();
            $element = $copier->copy($this);
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $this->beforeRender();

        $rowCount = old($this->name) ? count(old($this->name)) : $this->rows;
        $fields = collect(form()->make($this->schema)->all())
            ->transform(function ($item) {
                $this->labels[] = (string) $item->label;
                $item->label(null);

                return $item;
            });

        // reset old values row index
        $data = [$this->name => array_values(old($this->name, $this->source))];

        $rows = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $rows[] = $fields->map(function ($field) use ($data, $i) {
                $copier = new DeepCopy();
                $newField = $copier->copy($field);
                $newField->bindAttribute('name', $i);
                $newField->populateValue($data);

                return $newField;
            });
        }

        $payload = [
            'fields' => $fields,
            'name' => $this->name,
            'rows' => $rows,
            'labels' => $this->labels,
            'limit' => $rowCount,
            'allowAddition' => $this->allowAddition,
            'allowRemoval' => $this->allowRemoval,
        ];

        return view('semantic-form::tabular.form', $payload)->render();
    }
}
