<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Tabular extends Element
{
    protected $schema = [];

    protected $labels = [];

    protected $limit = 3;

    protected $allowAddition = false;

    protected $allowRemoval = false;

    public function __construct($schema)
    {
        $this->schema = $schema;
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
        $fields = collect(form()->make($this->schema)->all())
            ->transform(function ($item) {
                $this->labels[] = (string) $item->label;
                $item->label(null);
                $item->setAttribute('name', $item->getAttribute('name').'[]');

                return $item;
            });

        $data = [
            'fields' => $fields,
            'labels' => $this->labels,
            'limit' => $this->limit,
            'allowAddition' => $this->allowAddition,
            'allowRemoval' => $this->allowRemoval,
        ];

        return view('semantic-form::tabular', $data)->render();
    }
}
