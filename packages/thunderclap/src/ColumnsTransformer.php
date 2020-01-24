<?php

namespace Laravolt\Thunderclap;

use Illuminate\Support\Str;
use Stringy\Stringy;

class ColumnsTransformer
{
    protected $columns;

    protected $fieldTypeTransformer;

    public function __construct(FieldTypeTransformer $fieldTypeTransformer)
    {
        $this->fieldTypeTransformer = $fieldTypeTransformer;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    public function toSearchableColumns()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        return $columns
                ->keys()
                ->map(function ($item) {
                    return '"'.$item.'"';
                })
                ->implode(', ').',';
    }

    public function toValidationRules()
    {
        $columns = $this->columns;
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        $template =
            <<<'TEMPLATE'
            '%s' => ['%s']
TEMPLATE;

        return $columns
            ->values()
            ->map(function ($item) use ($template) {
                return sprintf($template, $item['name'], $item['required'] ? 'required' : '');
            })
            ->implode(",\n").',';
    }

    public function toLangFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $template =
            <<<'TEMPLATE'
    '%s' => '%s'
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template) {
                return sprintf($template, $item, ucwords(str_replace('_', ' ', $item)));
            })
            ->implode(",\n").',';
    }

    public function toFormCreateFields()
    {
        $columns = $this->columns;
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        return $columns
            ->map(function ($item) {
                $template = $this->fieldTypeTransformer->generate($item);

                return sprintf("\t".$template, $item['name'], Stringy::create($item['name'])->humanize());
            })
            ->implode("\n");
    }

    public function toFormEditFields()
    {
        return $this->toFormCreateFields();
    }

    public function toTableHeaders()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        $template =
            <<<'TEMPLATE'
                    <th>%s</th>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template) {
                return sprintf($template, Stringy::create($item)->humanize());
            })
            ->implode("\n");
    }

    public function toTableFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        $template =
            <<<'TEMPLATE'
                    <td>{{ $item->present('%s') }}</td>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template) {
                return sprintf($template, $item);
            })
            ->implode("\n");
    }

    public function toTableViewFields()
    {
        $columns = $this->columns;
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        $template =
            <<<'TEMPLATE'
            Text::make('%s')->sortable(),
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template) {
                return sprintf($template, $item);
            })
            ->implode("\n");
    }

    public function toDetailFields($objectName)
    {
        $columns = $this->columns;
        $template =
            <<<'TEMPLATE'
        <tr><td>%s</td><td>{{ $%s->%s }}</td></tr>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template, $objectName) {
                return sprintf($template, Stringy::create($item)->humanize(), $objectName, $item);
            })
            ->implode("\n");
    }

    protected function removeForeignKeys($columns)
    {
        return $columns->filter(function ($item) {
            if (Str::endsWith($item['name'], '_id')) {
                return false;
            }

            return true;
        });
    }
}
