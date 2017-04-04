<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Comma\Http\Requests\UpdatePost;
use Laravolt\Comma\Models\Scopes\VisibleScope;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = app('laravolt.comma.models.post')->latest()->search($request->get('search'))->paginate();

        return view('comma::posts.index', compact('posts'));
    }

    public function create()
    {
        try {
            $post = app('laravolt.comma')->getDefaultPost(auth()->user());

            return redirect()->route('comma::posts.edit', $post->id);
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $post = app('laravolt.comma.models.post')->withoutGlobalScope(VisibleScope::class)->findOrFail($id);
        $categories = app('laravolt.comma.models.category')->all()->pluck('name', 'id');
        $tags = app('laravolt.comma.models.tag')->all()->pluck('name', 'name');

        return view('comma::posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePost $request, $id)
    {
        $post = app('laravolt.comma.models.post')->withoutGlobalScope(VisibleScope::class)->findOrFail($id);

        try {
            $post = app('laravolt.comma')
                ->updatePost(
                    $post,
                    auth()->user(),
                    $request->get('title'),
                    $request->get('content'),
                    $request->get('category_id'),
                    $request->get('tags')
                );

            switch ($request->get('action')) {
                case 'publish':
                    $post->publish();
                    break;
                case 'unpublish':
                    $post->unpublish();
                    break;
                case 'draft':
                    $post->saveAsDraft();
                    break;
                case 'save':
                default:
                    break;
            }

            return redirect()->route('comma::posts.index')->withSuccess(trans('comma::post.message.update_success'));
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            app('laravolt.comma.models.post')->find($id)->delete();

            return redirect()->route('comma::posts.index')->withSuccess(trans('comma::post.message.delete_success'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
