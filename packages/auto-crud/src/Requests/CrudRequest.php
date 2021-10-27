<?php

namespace Laravolt\AutoCrud\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Laravolt\AutoCrud\SchemaTransformer;
use Laravolt\Fields\Field;

class CrudRequest extends FormRequest
{
    protected array $resourceConfig;

    protected string $visibility;

    protected array $methodMap = [
        self::METHOD_POST => 'create',
        self::METHOD_PUT => 'edit',
    ];

    public function getConfig()
    {
        return $this->resourceConfig;
    }

    public function authorize()
    {
        $resource = $this->resource;
        $key = "laravolt.auto-crud-resources.$resource";

        if (! config()->has($key)) {
            abort(404);
        }

        // Module level authorization
        if ($this->user()->cannot(config('laravolt.auto-crud.permission'))) {
            return false;
        }

        // Collection level authorization
        if ($this->user()->cannot(config("laravolt.auto-crud-resources.$resource.permission"))) {
            return false;
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
        return collect($transformer->transform())
            ->filter(
                function ($item) use ($method) {
                    if ($item instanceof Field) {
                        return $item->visibleFor($method);
                    }
                    return ($item['visibility'][$method] ?? true);
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

                    return [$key => $item['rules'] ?? []];
                }
            )->toArray();
    }

    public function validated()
    {
        $data = parent::validated();

        //Special case for file uploader
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
