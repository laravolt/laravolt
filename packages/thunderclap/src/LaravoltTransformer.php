<?php

declare(strict_types=1);

namespace Laravolt\Thunderclap;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
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

        $result = $columns
            ->keys()->map(function ($item) {
                return '"'.$item.'"';
            })
            ->implode(', ');

        return $result ? $result : '';
    }

    public function toValidationRules()
    {
        $columns = $this->columns;
        $columns = $columns->except(config('laravolt.thunderclap.columns.except'));

        $template =
            <<<'TEMPLATE'
            '%s' => ['%s']
TEMPLATE;

        $result = $columns
            ->values()
            ->map(function ($item) use ($template) {
                return sprintf($template, $item['name'], $item['required'] ? 'required' : '');
            })
            ->implode(",\n");

        return $result ? $result : '';
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

                return sprintf($template, $item['name'], Str::humanize($item['name']));
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
        $template = <<<'TEMPLATE'
                    <x-volt-data-display :label="__('%s')" :value="$%s->%s" />
        TEMPLATE;

        return $columns
            ->keys()
            ->map(function ($item) use ($template, $objectName) {
                return sprintf($template, Str::humanize($item), $objectName, $item);
            })
            ->implode("\n");
    }

    public function toField(array $column)
    {
        $class = get_class($column['type']);
        switch ($class) {
            case TextType::class:
                $field = $this->textarea();
                break;
            case DateType::class:
                $field = $this->date();
                break;
            case DateTimeType::class:
                $field = $this->datetime();
                break;
            default:
                $field = $this->text();
        }

        if ($column['required']) {
            $field .= '->required()';
        }

        return "    {!! $field !!}";
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

    protected function removeForeignKeys($columns)
    {
        return $columns->filter(function ($item) {
            if (Str::endsWith($item['name'], '_id')) {
                return false;
            }

            return true;
        });
    }

    protected function text()
    {
        return "form()->text('%s')->label('%s')";
    }

    protected function textarea()
    {
        return "form()->textarea('%s')->label('%s')";
    }

    protected function date()
    {
        return "form()->date('%s')->label('%s')";
    }

    protected function datetime()
    {
        return "form()->datepicker('%s')->label('%s')";
    }

    protected function generateFactoryAttribute($column)
    {
        $name = $column['name'];
        $type = get_class($column['type']);

        switch ($type) {
            case \Doctrine\DBAL\Types\StringType::class:
                if (Str::contains($name, 'email')) {
                    return "            '{$name}' => \$this->faker->unique()->safeEmail(),";
                }
                if (Str::contains($name, 'name')) {
                    return "            '{$name}' => \$this->faker->name(),";
                }
                if (Str::contains($name, 'title')) {
                    return "            '{$name}' => \$this->faker->sentence(3),";
                }
                if (Str::contains($name, 'slug')) {
                    return "            '{$name}' => \$this->faker->slug(),";
                }
                if (Str::contains($name, 'phone')) {
                    return "            '{$name}' => \$this->faker->phoneNumber(),";
                }
                if (Str::contains($name, 'address')) {
                    return "            '{$name}' => \$this->faker->address(),";
                }
                if (Str::contains($name, 'url')) {
                    return "            '{$name}' => \$this->faker->url(),";
                }

                return "            '{$name}' => \$this->faker->words(3, true),";

            case TextType::class:
                if (Str::contains($name, 'description')) {
                    return "            '{$name}' => \$this->faker->paragraph(),";
                }

                return "            '{$name}' => \$this->faker->text(),";

            case DateType::class:
                return "            '{$name}' => \$this->faker->date(),";
            case DateTimeType::class:
                return "            '{$name}' => \$this->faker->dateTime(),";
            default:
                if (Str::contains($name, '_id')) {
                    return null; // Skip foreign keys
                }
                if (Str::contains($name, 'price') || Str::contains($name, 'amount')) {
                    return "            '{$name}' => \$this->faker->randomFloat(2, 10, 1000),";
                }
                if (Str::contains($name, 'quantity') || Str::contains($name, 'count')) {
                    return "            '{$name}' => \$this->faker->numberBetween(1, 100),";
                }

                return "            '{$name}' => \$this->faker->word(),";

        }
    }

    protected function generateUpdateAttribute($name, $column)
    {
        $type = get_class($column['type']);

        switch ($type) {
            case \Doctrine\DBAL\Types\StringType::class:
                if (Str::contains($name, 'title')) {
                    return "\$attributes['{$name}'] = 'Updated Title';";
                }
                if (Str::contains($name, 'name')) {
                    return "\$attributes['{$name}'] = 'Updated Name';";
                }

                return "\$attributes['{$name}'] = 'Updated ".Str::title(str_replace('_', ' ', $name))."';";

            case TextType::class:
                if (Str::contains($name, 'description')) {
                    return "\$attributes['{$name}'] = 'Updated Description';";
                }

                return "\$attributes['{$name}'] = 'Updated Content';";

            case DateType::class:
                return "\$attributes['{$name}'] = now()->format('Y-m-d');";
            case DateTimeType::class:
                return "\$attributes['{$name}'] = now();";
            default:
                return "\$attributes['{$name}'] = 'Updated Value';";
        }
    }
}
