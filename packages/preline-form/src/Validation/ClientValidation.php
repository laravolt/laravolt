<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Validation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\In;

class ClientValidation
{
    protected static array $rules = [];

    protected static array $messages = [];

    public static function use(FormRequest|string|array $request): void
    {
        if (is_array($request)) {
            static::$rules = $request;
            static::$messages = [];

            return;
        }

        if (is_string($request)) {
            $request = static::makeFormRequest($request);
        }

        static::$rules = $request->rules();
        static::$messages = method_exists($request, 'messages') ? $request->messages() : [];
    }

    public static function clear(): void
    {
        static::$rules = [];
        static::$messages = [];
    }

    public static function apply(string $name, array $attributes): array
    {
        $rules = static::rulesFor($name);

        if ($rules === []) {
            return $attributes;
        }

        $compiled = static::compile($rules);

        foreach ($compiled['attributes'] as $attribute => $value) {
            if (! array_key_exists($attribute, $attributes) || ($attribute === 'type' && $attributes[$attribute] === 'text')) {
                $attributes[$attribute] = $value;
            }
        }

        $attributes['data-validation-rules'] = $compiled['rules'];

        if ($message = static::messageFor($name, $compiled['rules'])) {
            $attributes['data-validation-message'] = $message;
            $attributes['title'] ??= $message;
        }

        return $attributes;
    }

    public static function rulesFor(string $name): array
    {
        $rules = static::findRulesFor($name);

        if ($rules === []) {
            return [];
        }

        if (is_string($rules)) {
            return explode('|', $rules);
        }

        return is_array($rules) ? $rules : [$rules];
    }

    protected static function findRulesFor(string $name): mixed
    {
        $normalized = static::normalizeName($name);
        $rules = Arr::get(static::$rules, $normalized, Arr::get(static::$rules, $name, []));

        if ($rules !== []) {
            return $rules;
        }

        foreach (static::$rules as $key => $rules) {
            if (str_contains((string) $key, '*') && Str::is((string) $key, $normalized)) {
                return $rules;
            }
        }

        return [];
    }

    protected static function makeFormRequest(string $request): FormRequest
    {
        $formRequest = $request::createFrom(request(), new $request);
        $formRequest->setContainer(app());

        if (app()->bound('redirect')) {
            $formRequest->setRedirector(app('redirect'));
        }

        return $formRequest;
    }

    protected static function compile(array $rules): array
    {
        $attributes = [];
        $serializedRules = [];
        $normalizedRules = array_values(array_filter(array_map(static::normalizeRule(...), $rules), fn ($rule) => $rule[0] !== ''));
        $numeric = collect($normalizedRules)->contains(fn ($rule) => in_array($rule[0], ['integer', 'numeric', 'decimal'], true));

        if ($numeric) {
            $attributes['inputmode'] = 'numeric';
        }

        foreach ($normalizedRules as [$name, $parameters]) {
            $serializedRules[$name] = $parameters;

            match ($name) {
                'accepted', 'required' => $attributes['required'] = 'required',
                'email' => $attributes['type'] = 'email',
                'url', 'active_url' => $attributes['type'] = 'url',
                'min' => static::applyMin($attributes, $parameters),
                'max' => static::applyMax($attributes, $parameters),
                'between' => static::applyBetween($attributes, $parameters),
                'size' => static::applySize($attributes, $parameters),
                'digits' => static::applyDigits($attributes, $parameters),
                'digits_between' => static::applyDigitsBetween($attributes, $parameters),
                'regex' => $attributes['pattern'] = static::toHtmlPattern($parameters[0] ?? ''),
                'in' => $attributes['data-validation-accepted-values'] = implode(',', $parameters),
                default => null,
            };
        }

        return ['attributes' => $attributes, 'rules' => $serializedRules];
    }

    protected static function normalizeRule(mixed $rule): array
    {
        if (is_string($rule)) {
            [$name, $parameters] = array_pad(explode(':', $rule, 2), 2, '');
            $name = Str::snake(mb_strtolower($name));

            if (in_array($name, ['regex', 'not_regex'], true)) {
                return [$name, $parameters === '' ? [] : [$parameters]];
            }

            return [$name, $parameters === '' ? [] : str_getcsv($parameters)];
        }

        if ($rule instanceof In) {
            return static::normalizeRule((string) $rule);
        }

        if (is_object($rule) && method_exists($rule, '__toString')) {
            return static::normalizeRule((string) $rule);
        }

        if (is_object($rule)) {
            return [class_basename($rule), []];
        }

        return ['', []];
    }

    protected static function applyMin(array &$attributes, array $parameters): void
    {
        if (! isset($parameters[0])) {
            return;
        }

        if (($attributes['inputmode'] ?? null) === 'numeric') {
            $attributes['min'] = $parameters[0];
        } else {
            $attributes['minlength'] = $parameters[0];
        }
    }

    protected static function applyMax(array &$attributes, array $parameters): void
    {
        if (! isset($parameters[0])) {
            return;
        }

        if (($attributes['inputmode'] ?? null) === 'numeric') {
            $attributes['max'] = $parameters[0];
        } else {
            $attributes['maxlength'] = $parameters[0];
        }
    }

    protected static function applyBetween(array &$attributes, array $parameters): void
    {
        static::applyMin($attributes, [$parameters[0] ?? null]);
        static::applyMax($attributes, [$parameters[1] ?? null]);
    }

    protected static function applySize(array &$attributes, array $parameters): void
    {
        if (! isset($parameters[0])) {
            return;
        }

        if (($attributes['inputmode'] ?? null) === 'numeric') {
            $attributes['min'] = $parameters[0];
            $attributes['max'] = $parameters[0];
        } else {
            $attributes['minlength'] = $parameters[0];
            $attributes['maxlength'] = $parameters[0];
        }
    }

    protected static function applyDigits(array &$attributes, array $parameters): void
    {
        if (! isset($parameters[0])) {
            return;
        }

        $attributes['pattern'] = '\\d{'.$parameters[0].'}';
    }

    protected static function applyDigitsBetween(array &$attributes, array $parameters): void
    {
        if (! isset($parameters[0], $parameters[1])) {
            return;
        }

        $attributes['pattern'] = '\\d{'.$parameters[0].','.$parameters[1].'}';
    }

    protected static function toHtmlPattern(string $regex): string
    {
        if ($regex === '') {
            return '';
        }

        $delimiter = $regex[0];

        if (ctype_alnum($delimiter) || $delimiter === '\\') {
            return $regex;
        }

        $end = static::findRegexDelimiterEnd($regex, $delimiter);

        if ($end === null) {
            return $regex;
        }

        return substr($regex, 1, $end - 1);
    }

    protected static function findRegexDelimiterEnd(string $regex, string $delimiter): ?int
    {
        for ($position = strlen($regex) - 1; $position > 0; $position--) {
            if ($regex[$position] === $delimiter && ! static::isEscaped($regex, $position)) {
                return $position;
            }
        }

        return null;
    }

    protected static function isEscaped(string $value, int $position): bool
    {
        $slashes = 0;

        for ($index = $position - 1; $index >= 0 && $value[$index] === '\\'; $index--) {
            $slashes++;
        }

        return $slashes % 2 === 1;
    }

    protected static function messageFor(string $name, array $rules): ?string
    {
        $normalized = static::normalizeName($name);

        foreach (array_keys($rules) as $rule) {
            $message = static::findMessageFor($normalized, $name, $rule);

            if ($message) {
                return $message;
            }
        }

        return null;
    }

    protected static function findMessageFor(string $normalized, string $name, string $rule): ?string
    {
        $exact = static::$messages["{$normalized}.{$rule}"] ?? static::$messages["{$name}.{$rule}"] ?? null;

        if ($exact) {
            return $exact;
        }

        foreach (static::$messages as $key => $message) {
            if (str_contains((string) $key, '*') && Str::is((string) $key, "{$normalized}.{$rule}")) {
                return $message;
            }
        }

        return null;
    }

    protected static function normalizeName(string $name): string
    {
        return mb_trim(str_replace(']', '', str_replace('[', '.', $name)), '.');
    }
}
