<?php

namespace Laravolt\AutoCrud;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Validation\Rule as BaseRule;
use JsonSerializable;

/**
 * A serializable wrapper for Laravel validation Rule objects.
 * This allows Rule objects to be used in config files that are cached with php artisan config:cache.
 */
class SerializableRule implements Arrayable, JsonSerializable
{
    protected $ruleType;

    protected $parameters = [];

    /**
     * Create a new serializable unique rule.
     *
     * @param  string  $table  The table to check for uniqueness
     * @param  string|null  $column  The column to check (defaults to the field name)
     * @param  mixed  ...$parameters  Additional parameters for the unique rule
     * @return static
     */
    public static function unique(string $table, $column = null, ...$parameters): self
    {
        $instance = new self;
        $instance->ruleType = 'unique';
        $instance->parameters = array_filter(compact('table', 'column', 'parameters'), function ($value) {
            return $value !== null;
        });

        return $instance;
    }

    /**
     * Create a serializable exists rule.
     *
     * @param  string  $table  The table to check for existence
     * @param  string|null  $column  The column to check (defaults to the field name)
     * @return static
     */
    public static function exists(string $table, $column = null): self
    {
        $instance = new self;
        $instance->ruleType = 'exists';
        $instance->parameters = array_filter(compact('table', 'column'), function ($value) {
            return $value !== null;
        });

        return $instance;
    }

    /**
     * Create serializable in rule.
     *
     * @param  array  $values  The allowed values
     * @return static
     */
    public static function in(array $values): self
    {
        $instance = new self;
        $instance->ruleType = 'in';
        $instance->parameters = compact('values');

        return $instance;
    }

    /**
     * Convert the serializable rule to its string representation.
     */
    public function toString(): string
    {
        if ($this->ruleType === 'unique') {
            $rule = 'unique:'.$this->parameters['table'];
            if (isset($this->parameters['column']) && $this->parameters['column'] !== null) {
                $rule .= ','.$this->parameters['column'];
            }

            return $rule;
        }

        if ($this->ruleType === 'exists') {
            $rule = 'exists:'.$this->parameters['table'];
            if (isset($this->parameters['column']) && $this->parameters['column'] !== null) {
                $rule .= ','.$this->parameters['column'];
            }

            return $rule;
        }

        if ($this->ruleType === 'in') {
            return 'in:'.implode(',', $this->parameters['values']);
        }

        return '';
    }

    /**
     * Convert the serializable rule to an actual Laravel validation rule at runtime.
     *
     * @return mixed
     */
    public function toRule()
    {
        if ($this->ruleType === 'unique') {
            $rule = BaseRule::unique($this->parameters['table'], $this->parameters['column'] ?? null);

            // Add any additional parameters
            if (isset($this->parameters['parameters'])) {
                foreach ($this->parameters['parameters'] as $param) {
                    if (is_array($param) && count($param) === 2) {
                        $method = $param[0];
                        $args = $param[1];
                        $rule = $rule->$method($args);
                    }
                }
            }

            return $rule;
        }

        if ($this->ruleType === 'exists') {
            return BaseRule::exists($this->parameters['table'], $this->parameters['column'] ?? null);
        }

        if ($this->ruleType === 'in') {
            return BaseRule::in($this->parameters['values']);
        }

        return $this->toString();
    }

    /**
     * Convert the rule to a string when cast to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Convert the rule to a string when JSON encoded.
     */
    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Convert the rule to an array.
     *
     * @return string
     */
    public function toArray()
    {
        return $this->toString();
    }
}
