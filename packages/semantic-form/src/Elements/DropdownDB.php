<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DropdownDB extends Select
{
    protected $query;

    protected $keyColumn = 'id';

    protected $displayColumn = 'name';

    protected $dependency;

    protected function beforeRender()
    {
        $this->setupDependency();

        if ($this->dependency && $this->hasOldInput()) {
            $query = sprintf($this->query, old($this->dependency));
            $this->query($query);
        }

        if (!Str::contains($this->query, '%s')) {
            $this->populateOptions();
        }
    }

    public function dependency(?string $dependency)
    {
        if ($dependency) {
            $this->dependency = $dependency;
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

        $this->options($options);
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

    private function hasOldInput()
    {
        if ($this->dependency && request()->old($this->dependency)) {
            return true;
        }

        return false;
    }
}
