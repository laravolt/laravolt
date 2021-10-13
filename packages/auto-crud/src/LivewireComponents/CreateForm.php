<?php

namespace Laravolt\AutoCrud\LivewireComponents;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravolt\Fields\Field;
use Laravolt\Ui\Modal;

class CreateForm extends Modal
{
    public string $resource;

    protected array $methodMap = [
        Request::METHOD_POST => 'create',
        Request::METHOD_PUT => 'edit',
    ];

    public function rules()
    {
        $method = $this->methodMap[request()->method()] ?? false;

        if (! $method) {
            return [];
        }

        $config = config()->get("laravolt.auto-crud.resources.{$this->resource}");

        return collect($config['schema'])
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
                    if (Arr::get($item, 'type') === 'uploader' && request()->get('_'.$key) !== '[]') {
                        $key = '_'.$key;
                    }

                    return [$key => $item['rules'] ?? []];
                }
            )->toArray();
    }

    public function render()
    {
        $config = config()->get("laravolt.auto-crud.resources.{$this->resource}");
        $fields = collect($config['schema'])
            ->filter(
                function ($item) {
                    if ($item instanceof Field) {
                        return $item->visibleFor('create');
                    }

                    return ($item['visibility']['create'] ?? true);
                }
            );

        return view('laravolt::auto-crud.create', compact('config', 'fields'));
    }

    public function submit()
    {
        dump($this->rules());
        try {
            Validator::make(request()->all(), $this->rules())->validate();
        } catch (ValidationException $e) {
            dump(request()->all(), $e->validator->errors());
        }
    }
}
