<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Laravolt\AutoCrud\Requests\CrudRequest;
use Laravolt\AutoCrud\SchemaTransformer;
use Laravolt\Fields\Field;

class ResourceController extends Controller
{
    use AuthorizesRequests;

    public function index(CrudRequest $request, string $resource)
    {
        $config = $request->getConfig();

        return view('laravolt::auto-crud.index', compact('config'));
    }

    public function create(CrudRequest $request, string $resource)
    {
        $config = $request->getConfig();
        $fields = (new SchemaTransformer($config))->getFieldsForCreate();

        return view('laravolt::auto-crud.create', compact('config', 'fields'));
    }

    public function store(CrudRequest $request, string $resource)
    {
        $config = $request->getConfig();
        app($config['model'])->create($request->validated());

        return redirect()
            ->route('auto-crud::resource.index', $resource)
            ->withSuccess(sprintf('%s saved', $config['label']));
    }

    public function show(CrudRequest $request, string $resource, $id)
    {
        $config = $request->getConfig();
        $model = app($config['model'])->findOrFail($id);
        $fields = (new SchemaTransformer($config))->getFieldsForDetail();

        return view('laravolt::auto-crud.show', compact('config', 'fields', 'model'));
    }

    public function edit(CrudRequest $request, string $resource, $id)
    {
        $config = $request->getConfig();
        $model = app($config['model'])->findOrFail($id);
        $fields = (new SchemaTransformer($config))->getFieldsForEdit();

        return view('laravolt::auto-crud.edit', compact('config', 'model', 'fields'));
    }

    public function update(CrudRequest $request, string $resource, $id)
    {
        $config = $request->getConfig();
        $model = app($config['model'])->findOrFail($id);

        $model->update($request->validated());

        return redirect()
            ->back()
            ->withSuccess(sprintf('%s updated', $config['label']));
    }

    public function destroy(CrudRequest $request, string $resource, $id)
    {
        $config = $request->getConfig();
        $model = app($config['model'])->findOrFail($id);

        $model->delete();

        return redirect()
            ->back()
            ->withSuccess(sprintf('%s deleted', $config['label']));
    }
}
