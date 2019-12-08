<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DeepCopy\DeepCopy;
use Illuminate\Support\Str;

class Tabular extends Element
{
    protected $schema = [];

    protected $labels = [];

    protected $rows = 3;

    protected $allowAddition = false;

    protected $allowRemoval = false;

    protected $name;

    public function __construct($name, $schema)
    {
        $this->name = $name;

        $this->schema = collect($schema)->transform(function ($item) {
            $item['name'] = Str::endsWith($item['name'], '[]') ? $item['name'] : $item['name'].'[%s]';

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

    public function render()
    {
        if ($this->label) {
            $copier = new DeepCopy();
            $element = $copier->copy($this);
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $this->beforeRender();

        $rowCount = old("$this->name.rows", $this->rows);
        $fields = collect(form()->make($this->schema)->all())
            ->transform(function ($item) {
                $this->labels[] = (string) $item->label;
                $item->label(null);

                return $item;
            });

        // reset old values row index
        $oldValues = old();
        if (!empty($oldValues)) {
            foreach ($fields as $field) {
                $oldValues[$field->basename()] = array_values($oldValues[$field->basename()]);
            }
        }

        $rows = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $rows[] = $fields->map(function ($field) use ($oldValues, $i) {
                $copier = new DeepCopy();
                $newField = $copier->copy($field);
                $newField->bindAttribute('name', $i);
                $newField->populateValue($oldValues);

                return $newField;
            });
        }

        $data = [
            'fields' => $fields,
            'name' => $this->name,
            'rows' => $rows,
            'labels' => $this->labels,
            'limit' => $rowCount,
            'allowAddition' => $this->allowAddition,
            'allowRemoval' => $this->allowRemoval,
        ];

        return view('semantic-form::tabular', $data)->render();
    }
}
