<?php

namespace Laravolt\Thunderclap;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Illuminate\Support\Str;

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

    protected function text()
    {
        return "{!! form()->text('%s')->label('%s') !!}";
    }

    protected function textarea()
    {
        return "{!! form()->textarea('%s')->label('%s') !!}";
    }

    protected function date()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }

    protected function datetime()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }

    public function toTestFactoryAttributes()
    {
        $columns = $this->columns;
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        return $columns
            ->map(function ($item) {
                return $this->generateFactoryAttribute($item);
            })
            ->filter() // Remove null values
            ->implode("\n");
    }

    public function toTestUpdateAttributes()
    {
        $columns = $this->removeForeignKeys($this->columns);
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        // Get first few fillable columns for update test
        $selectedColumns = $columns->take(2);

        return $selectedColumns
            ->map(function ($item, $name) {
                return $this->generateUpdateAttribute($name, $item);
            })
            ->implode("\n        ");
    }

    protected function generateFactoryAttribute($column)
    {
        $name = $column['name'];
        $type = get_class($column['type']);

        switch ($type) {
            case \Doctrine\DBAL\Types\StringType::class:
                if (Str::contains($name, 'email')) {
                    return "            '{$name}' => \$this->faker->unique()->safeEmail(),";
                } elseif (Str::contains($name, 'name')) {
                    return "            '{$name}' => \$this->faker->name(),";
                } elseif (Str::contains($name, 'title')) {
                    return "            '{$name}' => \$this->faker->sentence(3),";
                } elseif (Str::contains($name, 'slug')) {
                    return "            '{$name}' => \$this->faker->slug(),";
                } elseif (Str::contains($name, 'phone')) {
                    return "            '{$name}' => \$this->faker->phoneNumber(),";
                } elseif (Str::contains($name, 'address')) {
                    return "            '{$name}' => \$this->faker->address(),";
                } elseif (Str::contains($name, 'url')) {
                    return "            '{$name}' => \$this->faker->url(),";
                } else {
                    return "            '{$name}' => \$this->faker->words(3, true),";
                }
            case \Doctrine\DBAL\Types\TextType::class:
                if (Str::contains($name, 'description')) {
                    return "            '{$name}' => \$this->faker->paragraph(),";
                } else {
                    return "            '{$name}' => \$this->faker->text(),";
                }
            case \Doctrine\DBAL\Types\DateType::class:
                return "            '{$name}' => \$this->faker->date(),";
            case \Doctrine\DBAL\Types\DateTimeType::class:
                return "            '{$name}' => \$this->faker->dateTime(),";
            default:
                if (Str::contains($name, '_id')) {
                    return null; // Skip foreign keys
                } elseif (Str::contains($name, 'price') || Str::contains($name, 'amount')) {
                    return "            '{$name}' => \$this->faker->randomFloat(2, 10, 1000),";
                } elseif (Str::contains($name, 'quantity') || Str::contains($name, 'count')) {
                    return "            '{$name}' => \$this->faker->numberBetween(1, 100),";
                } else {
                    return "            '{$name}' => \$this->faker->word(),";
                }
        }
    }

    protected function generateUpdateAttribute($name, $column)
    {
        $type = get_class($column['type']);

        switch ($type) {
            case \Doctrine\DBAL\Types\StringType::class:
                if (Str::contains($name, 'title')) {
                    return "\$attributes['{$name}'] = 'Updated Title';";
                } elseif (Str::contains($name, 'name')) {
                    return "\$attributes['{$name}'] = 'Updated Name';";
                } else {
                    return "\$attributes['{$name}'] = 'Updated " . Str::title(str_replace('_', ' ', $name)) . "';";
                }
            case \Doctrine\DBAL\Types\TextType::class:
                if (Str::contains($name, 'description')) {
                    return "\$attributes['{$name}'] = 'Updated Description';";
                } else {
                    return "\$attributes['{$name}'] = 'Updated Content';";
                }
            case \Doctrine\DBAL\Types\DateType::class:
                return "\$attributes['{$name}'] = now()->format('Y-m-d');";
            case \Doctrine\DBAL\Types\DateTimeType::class:
                return "\$attributes['{$name}'] = now();";
            default:
                return "\$attributes['{$name}'] = 'Updated Value';";
        }
    }
}
