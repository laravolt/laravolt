<?php

namespace Laravolt\Thunderclap;

use Illuminate\Support\Str;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;

class LaravoltTransformer
{
    protected $columns;

    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    public function toSearchableColumns()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        return $columns
            ->keys()->map(function ($item) {
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
                $template = $this->toField($item);

                return sprintf("\t".$template, $item['name'], Str::humanize($item['name']));
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
                return sprintf($template, Str::humanize($item));
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
                return sprintf($template, Str::humanize($item), $objectName, $item);
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

    public function toField(array $column)
    {
        $class = get_class($column['type']);
        switch ($class) {
            case StringType::class:
                return $this->text();
            case TextType::class:
                return $this->textarea();
            case DateType::class:
                return $this->date();
            case DateTimeType::class:
                return $this->datetime();
            default:
                return $this->text();
        }
    }

    private function text()
    {
        return "{!! form()->text('%s')->label('%s') !!}";
    }

    private function textarea()
    {
        return "{!! form()->textarea('%s')->label('%s') !!}";
    }

    private function date()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }

    private function datetime()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }
}
