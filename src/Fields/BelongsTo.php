<?php

namespace Laravolt\Fields;

use Illuminate\Support\Str;

class BelongsTo implements Field
{
    protected string $type = Field::BELONGS_TO;

    protected string $belongsToClass;

    protected string $name;

    protected string $label;

    protected array $rules;

    protected string $query;

    protected \Closure $display;

    /**
     * BelongsTo constructor.
     */
    public function __construct(string $belongsToClass)
    {
        $this->belongsToClass = $belongsToClass;
        $this->label = Str::title((new \ReflectionClass($belongsToClass))->getShortName());
    }

    public static function make(string $class): self
    {
        return new static($class);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function rules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function query(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'query' => $this->query,
            'name' => $this->name,
            'label' => $this->label,
            'rules' => $this->rules,
            'display' => $this->display,
        ];
    }

    public function display(\Closure $callback): self
    {
        $this->display = $callback;

        return $this;
    }

    public function visibleFor(string $method): bool
    {
        // TODO
        return true;
    }
}
