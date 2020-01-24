<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Comma\Http\Requests\Category\Store;
use Laravolt\Comma\Http\Requests\Category\Update;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = app('laravolt.comma.models.category')->latest()->search($request->get('search'))->paginate();

        return view('comma::categories.index', compact('categories'));
    }

    public function create()
    {
        return view('comma::categories.create');
    }

    public function store(Store $request)
    {
        try {
            app('laravolt.comma.models.category')->create($request->only(['name']));

            return redirect()->route('comma::categories.index')->withSuccess(
                trans('comma::category.message.create_success')
            );
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        return view('comma::categories.create');
    }

    public function edit($id)
    {
        $category = app('laravolt.comma.models.category')->findOrFail($id);

        return view('comma::categories.edit', compact('category'));
    }

    public function update(Update $request, $id)
    {
        try {
            $category = app('laravolt.comma.models.category')->findOrFail($id);
            $category->update($request->only(['name']));

            return redirect()->route('comma::categories.index')->withSuccess(
                trans('comma::category.message.update_success')
            );
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $category = app('laravolt.comma.models.category')->findOrFail($id);

            if ($category->name === config('laravolt.comma.default_category')) {
                return redirect()->route('comma::categories.index')->withError(
                    trans('comma::category.message.cannot_delete_default_category')
                );
            }

            $category->delete();

            return redirect()->route('comma::categories.index')->withSuccess(
                trans('comma::category.message.delete_success')
            );
        } catch (\Exception $e) {
            return redirect()->back()->withError($e->getMessage())->withInput();
        }
    }
}
