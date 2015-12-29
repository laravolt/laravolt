<?php
namespace Laravolt\Thunderclap;

use Illuminate\Support\Str;
use Stringy\Stringy;

class ColumnsTransformer
{

    protected $columns;

    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    public function toFillableFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('thunderclap.columns.except'));

        return $columns
            ->keys()
            ->map(function ($item) {
                return '"' . $item . '"';
            })
            ->implode(", ") . ",";
    }

    public function toTransformerFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $template =
            <<<TEMPLATE
            '%s' => \$model->%s
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, $item, $item);
            })
            ->implode(",\n") . ",";
    }

    public function toValidationRules()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('thunderclap.columns.except'));

        $template =
            <<<TEMPLATE
            '%s' => 'required'
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, $item, $item);
            })
            ->implode(",\n") . ",";

    }

    public function toLangFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $template =
            <<<TEMPLATE
    '%s' => '%s'
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, $item, ucwords(str_replace('_', ' ', $item)));
            })
            ->implode(",\n") . ",";
    }

    public function toFormCreateFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('thunderclap.columns.except'));

        $template =
            <<<TEMPLATE
                    {!! SemanticForm::text('%s', '%s') !!}
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, $item, Stringy::create($item)->humanize());
            })
            ->implode("\n");
    }

    public function toFormUpdateFields()
    {
        return $this->toFormCreateFields();
    }

    public function toTableHeaders()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('thunderclap.columns.except'));

        $template =
            <<<TEMPLATE
                    <th>%s</th>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, Stringy::create($item)->humanize());
            })
            ->implode("\n");
    }

    public function toTableFields()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('thunderclap.columns.except'));

        $template =
            <<<TEMPLATE
                    <td>{{ \$item->present('%s') }}</td>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, $item);
            })
            ->implode("\n");

    }

    public function toDetailFields()
    {
        $columns = $this->columns;
        $template =
            <<<TEMPLATE
                <tr><td>%s</td><td>{{ \$item->present('%s') }}</td></tr>
TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template){
                return sprintf($template, Stringy::create($item)->humanize(), $item);
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
