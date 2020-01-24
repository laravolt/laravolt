<?php

declare(strict_types=1);

namespace Laravolt\Lookup\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Laravolt\Lookup\Models\Lookup;
use Laravolt\Lookup\Requests\Lookup\Store;
use Laravolt\Lookup\Requests\Lookup\Update;
use Laravolt\Lookup\TableView\LookupTableView;

class LookupController extends Controller
{
    use AuthorizesRequests;

    public function index(string $collection)
    {
        $lookup = $this->validateCollection($collection);

        $source = Lookup::query()
            ->with('parent')
            ->fromCollection($collection)
            ->autoSort()
            ->latest()
            ->search(request('search'))
            ->paginate();

        return LookupTableView::make($source)->config($lookup)->title($lookup['label'] ?? $collection)->view('lookup::lookup.index', compact('collection'));
    }

    public function create(string $collection)
    {
        $config = $this->validateCollection($collection);

        return view('lookup::lookup.create', compact('collection', 'config'));
    }

    public function store(string $collection, Store $request)
    {
        $this->validateCollection($collection);

        Lookup::create($request->validated() + ['category' => $collection]);

        return redirect()->route('lookup::lookup.index', $collection)->withSuccess('Lookup berhasil disimpan');
    }

    public function edit(Lookup $lookup)
    {
        $config = $this->validateCollection($lookup->category);
        $collection = $lookup->category;

        return view('lookup::lookup.edit', compact('collection', 'lookup', 'config'));
    }

    public function update(Lookup $lookup, Update $request)
    {
        $this->validateCollection($lookup->category);

        $lookup->update($request->validated());

        return redirect()->route('lookup::lookup.index', $lookup->category)->withSuccess('Lookup berhasil disimpan');
    }

    public function destroy(Lookup $lookup)
    {
        $this->validateCollection($lookup->category);
    }

    protected function validateCollection(string $collection)
    {
        $key = "laravolt.lookup.collections.$collection";
        if (!config()->has($key)) {
            abort(404);
        }

        // Module level authorization
        $this->authorize(config('laravolt.lookup.permission'));

        // Collection level authorization
        $this->authorize(config("laravolt.lookup.collections.$collection.data.permission"));

        return config()->get($key);
    }
}
