<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravolt\SemanticForm\Traits\CanGenerateOptionsFromDb;

class DropdownDB extends Select
{
    use CanGenerateOptionsFromDb;

    protected $dependency;

    protected $dependencyValue;

    protected $ajax = false;

    public function dependency(?string $dependency, $value = null)
    {
        if ($dependency) {
            $this->dependency = $dependency;
            $this->dependencyValue = $value;
        }

        return $this;
    }

    public function ajax(bool $ajax = true)
    {
        $this->ajax = $ajax;

        return $this;
    }

    public function displayValue()
    {
        if ($this->hasAttribute('multiple')) {
            return 'TODO ';
            $values = json_decode($this->value);

            $this->beforeRender();

            $output = [];
            foreach ($values as $value) {
                $output[] = Arr::get($this->options, $value);
            }

            return collect($output)->implode('<br>');
        }

        if (is_string($this->value) || is_numeric($this->value)) {
            if (mb_trim($this->value) === '') {
                return null;
            }

            $this->beforeRender();

            return Arr::get($this->options, $this->value);
        }

        return $this->value;
    }

    protected function beforeRender()
    {
        $this->setupDependency();
        $this->data('class', $this->getAttribute('class'));

        if (! $this->ajax && ! Str::contains($this->query, ['%s', '%1$s'])) {
            $this->options = $this->getOptionsFromDb();
        }
    }

    private function setupDependency()
    {
        if ($this->ajax || ! empty($this->dependency)) {
            $payload = [
                'query_key_column' => $this->keyColumn,
                'query_display_column' => $this->displayColumn,
                'query' => $this->query,
                'connection' => $this->getConnection(),
            ];
            $payload = encrypt($payload);

            $this->data('depend-on', $this->dependency);
            $this->data('api', route('laravolt::api.dropdown'));
            $this->data('payload', $payload);
            $this->data('token', Session::token());

            if ($this->ajax) {
                $this->data('ajax', true);
            }

            // Jika parent dropdown sudah diketahui valuenya,
            // maka child dropdown otomatis di-populate juga options-nya
            $dependencyValue = old($this->dependency) ?? $this->dependencyValue;

            if ($dependencyValue) {
                $query = sprintf($this->query, $dependencyValue);
                $this->query($query);
            }
        }
    }
}
