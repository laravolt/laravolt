<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Comma\Exceptions\CmsException;
use Laravolt\Comma\Http\Requests\StorePost;
use Laravolt\Comma\Http\Requests\UpdatePost;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, string $collection)
    {
        $this->validateCollection($collection);

        $posts = app('laravolt.comma.models.post')
            ->fromCollection($collection)
            ->autoSort()
            ->latest()
            ->search($request->get('search'))
            ->paginate();

        return view('comma::posts.index', compact('posts', 'collection'));
    }

    public function create(string $collection)
    {
        $this->validateCollection($collection);

        $tags = app('laravolt.comma.models.tag')->all()->pluck('name', 'name');

        return view('comma::posts.create', compact('tags', 'collection'));
    }

    public function store(StorePost $request, string $collection)
    {
        $this->validateCollection($collection);

        try {
            $post = app('laravolt.comma')
                ->create(
                    $request->get('title'),
                    $request->get('content'),
                    auth()->user(),
                    config("laravolt.comma.collections.$collection.filters.type"),
                    $request->get('tags')
                );

            return redirect()
                ->route('comma::posts.index', $collection)
                ->withSuccess(trans('comma::post.message.create_success'));
        } catch (CmsException $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit(string $collection, $id)
    {
        $this->validateCollection($collection);

        $post = app('laravolt.comma.models.post')->findOrFail($id);
        $tags = app('laravolt.comma.models.tag')->pluck('name', 'name');

        return view('comma::posts.edit', compact('post', 'tags', 'collection'));
    }

    public function update(UpdatePost $request, $id)
    {
        $post = app('laravolt.comma.models.post')->findOrFail($id);

        try {
            $post = app('laravolt.comma')
                ->update(
                    $post,
                    $request->get('title'),
                    $request->get('content'),
                    auth()->user(),
                    $request->get('tags')
                );

            return redirect()->back()->withSuccess(trans('comma::post.message.update_success'));
        } catch (CmsException $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            app('laravolt.comma.models.post')->findOrFail($id)->delete();

            return redirect()->back()->withSuccess(trans('comma::post.message.delete_success'));
        } catch (CmsException $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    protected function validateCollection(string $collection)
    {
        $key = "laravolt.comma.collections.$collection";

        if (!config()->has($key)) {
            abort(404);
        }

        $this->authorize(config("laravolt.comma.collections.$collection.data.permission"));
    }
}
