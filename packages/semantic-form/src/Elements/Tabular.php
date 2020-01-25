<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DeepCopy\DeepCopy;
use Illuminate\Support\Arr;

class Tabular extends Element
{
    protected $schema = [];

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
            $item['name'] = "{$this->name}[%s][{$item['name']}]";

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

        $payload = [
            'schema' => $this->schema,
            'source' => $this->source,
            'name' => $this->name,
            'limit' => $rowCount,
            'allowAddition' => $this->allowAddition,
            'allowRemoval' => $this->allowRemoval,
        ];

        return view('semantic-form::tabular.form', $payload)->render();
    }
}
