<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Laravolt\AutoCrud\Requests\CrudRequest;
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

        return view('laravolt::auto-crud.show', compact('config', 'model'));
    }

    public function edit(CrudRequest $request, string $resource, $id)
    {
        $config = $request->getConfig();
        $model = app($config['model'])->findOrFail($id);
        $fields = collect($config['schema'])
            ->filter(
                function ($item) {
                    if ($item instanceof Field) {
                        return $item->visibleFor('edit');
                    }

                    return ($item['visibility']['edit'] ?? true);
                }
            );

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
