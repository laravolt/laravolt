<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DropdownDB extends Select
{
    protected $query;

    protected $keyColumn = 'id';

    protected $displayColumn = 'name';

    protected $dependency;

    protected $dependencyValue;

    protected function beforeRender()
    {
        $this->setupDependency();
        $this->data('class', $this->getAttribute('class'));

        if ($this->dependency) {
            $dependencyValue = old($this->dependency) ?? $this->dependencyValue;

            if ($dependencyValue) {
                $query = sprintf($this->query, $dependencyValue);
                $this->query($query);
            }
        }

        if (!Str::contains($this->query, ['%s', '%1$s'])) {
            $this->populateOptions();
        }
    }

    public function dependency(?string $dependency, $value = null)
    {
        if ($dependency) {
            $this->dependency = $dependency;
            $this->dependencyValue = $value;
        }

        return $this;
    }

    public function query(?string $query)
    {
        if ($query) {
            $this->query = $query;
        }

        return $this;
    }

    public function keyColumn(?string $keyColumn)
    {
        if ($keyColumn) {
            $this->keyColumn = $keyColumn;
        }

        return $this;
    }

    public function displayColumn(?string $displayColumn)
    {
        if ($displayColumn) {
            $this->displayColumn = $displayColumn;
        }

        return $this;
    }

    public function displayValue()
    {
        if (is_string($this->value)) {
            if (strlen(trim($this->value)) === 0) {
                return null;
            }

            $this->beforeRender();

            return Arr::get($this->options, $this->value);
        }

        return $this->value;
    }

    private function populateOptions()
    {
        $keyColumn = $this->keyColumn;
        $valueColumn = $this->displayColumn;

        $options = [];

        if ($this->query) {
            $options = collect(DB::select($this->query))->mapWithKeys(function ($item) use ($keyColumn, $valueColumn) {
                $item = (array) $item;

                return [Arr::get($item, $keyColumn) => Arr::get($item, $valueColumn)];
            });
        }

        foreach ($options as $value => $label) {
            $this->appendOption($value, $label);
        }
    }

    private function setupDependency()
    {
        if (!empty($this->dependency)) {
            $payload = [
                'query_key_column' => $this->keyColumn,
                'query_display_column' => $this->displayColumn,
                'query' => $this->query,
            ];
            $payload = encrypt($payload);

            $this->data('depend-on', $this->dependency);
            $this->data('api', route('laravolt::proxy').'?parent={parent}&payload={payload}');
            $this->data('payload', $payload);
        }
    }
}
