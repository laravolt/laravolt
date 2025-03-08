<?php

namespace Laravolt\AutoCrud\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Unique;
use Laravolt\AutoCrud\SchemaTransformer;
use Laravolt\Fields\Field;

class CrudRequest extends FormRequest
{
    protected array $defaultConfig = [
        'restful_button' => true,
    ];

    protected array $resourceConfig;

    protected string $visibility;

    protected array $methodMap = [
        self::METHOD_POST => 'create',
        self::METHOD_PUT => 'edit',
    ];

    public function getConfig()
    {
        return $this->resourceConfig + $this->defaultConfig;
    }

    public function authorize()
    {
        $resource = $this->resource;
        $key = "laravolt.auto-crud-resources.$resource";

        if (! config()->has($key)) {
            abort(404);
        }

        // Module level authorization
        if ($permission = config('laravolt.auto-crud.permission')) {
            if (! $this->user()->canAny($permission)) {
                return false;
            }
        }

        // Collection level authorization
        if ($permission = config("laravolt.auto-crud-resources.$resource.permission")) {
            if (! $this->user()->canAny($permission)) {
                return false;
            }
        }

        $this->resourceConfig = config()->get($key) + ['key' => $resource];

        return true;
    }

    public function rules()
    {
        $method = $this->methodMap[$this->method()] ?? false;

        if (! $method) {
            return [];
        }

        $transformer = new SchemaTransformer($this->resourceConfig);

        match ($method) {
            'create' => $items = $transformer->getFieldsForCreate(),
            'edit' => $items = $transformer->getFieldsForEdit(),
            default => $items = collect($transformer->transform())
        };

        return $items->filter(
            function ($item) use ($method) {
                if ($item instanceof Field) {
                    return $item->visibleFor($method);
                }

                if (in_array($item['type'], [Field::BUTTON, Field::ACTION, Field::HTML], true)) {
                    return false;
                }

                return $item['visibility'][$method] ?? true;
            }
        )->mapWithKeys(
            function ($item) {
                if ($item instanceof Field) {
                    $item = $item->toArray();
                }
                $key = $item['name'];
                if (Arr::get($item, 'type') === 'uploader' && $this->get('_'.$key) !== '[]') {
                    $key = '_'.$key;
                }

                $rules = collect($item['rules'] ?? []);

                // ignore current ID for unique rules when updating
                if ($this->method() === 'PUT') {
                    collect($rules)->transform(function ($rule) {
                        if ($rule instanceof Unique) {
                            $rule = $rule->ignore($this->route('id'));
                        }

                        return $rule;
                    });
                }

                return [$key => $rules->toArray()];
            }
        )->toArray();
    }

    protected function data($key = null, $default = null)
    {
        $data = parent::validated();

        // Special case for file uploader
        collect($this->resourceConfig['schema'])->each(
            function ($item) use (&$data) {
                $key = Arr::get($item, 'name');
                if (Arr::get($item, 'type') === 'uploader') {
                    if (Arr::get($item, 'limit') === 1 && Arr::get($item, 'as_array') === false) {
                        $data[$key] = request()->media($key)->first();
                    } else {
                        $data[$key] = request()->media($key)->toJson();
                    }
                }
            }
        );

        return $data;
    }
}
