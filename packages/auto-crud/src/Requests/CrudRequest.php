<?php

namespace Laravolt\AutoCrud\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $key = "laravolt.auto-crud.resources.$resource";

        if (! config()->has($key)) {
            abort(404);
        }

        // Module level authorization
        if ($this->user()->cannot(config('laravolt.auto-crud.permission'))) {
            return false;
        }

        // Collection level authorization
        if ($this->user()->cannot(config("laravolt.auto-crud.resources.$resource.permission"))) {
            return false;
        }

        $this->resourceConfig = config()->get($key) + ['key' => $resource];

        return true;
    }

    public function rules()
    {
        $method = $this->methodMap[$this->method()] ?? false;

        if (!$method) {
            return [];
        }

        return collect($this->resourceConfig['schema'])
            ->filter(
                function ($item) use ($method) {
                    return ($item['visibility'][$method] ?? true);
                }
            )->mapWithKeys(
                function ($item) {
                    return [$item['name'] => $item['rules'] ?? []];
                }
            )->toArray();
    }
}
