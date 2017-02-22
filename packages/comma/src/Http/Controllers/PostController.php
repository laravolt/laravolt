<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Comma\Http\Requests\UpdatePost;
use Laravolt\Comma\Models\Category;
use Laravolt\Comma\Models\Post;
use Laravolt\Comma\Http\Requests\StorePost;
use Laravolt\Comma\Models\Tag;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::latest()->search($request->get('search'))->paginate();

        return view('comma::posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all()->pluck('name', 'id');

        $tags = Tag::all()->pluck('name', 'name');

        return view('comma::posts.create', compact('categories', 'tags'));
    }

    public function store(StorePost $request)
    {
        try {
            app('laravolt.comma')
                ->makePost(
                    auth()->user(),
                    $request->get('title'),
                    $request->get('content'),
                    $request->get('category_id'),
                    $request->get('tags')
                );

            return redirect()->route('comma::posts.index')->withSuccess(trans('comma::post.message.create_success'));
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all()->pluck('name', 'id');
        $tags = Tag::all()->pluck('name', 'name');

        return view('comma::posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePost $request, $id)
    {
        $post = Post::findOrFail($id);

        try {
            app('laravolt.comma')
                ->updatePost(
                    $post,
                    auth()->user(),
                    $request->get('title'),
                    $request->get('content'),
                    $request->get('category_id'),
                    $request->get('tags')
                );

            return redirect()->route('comma::posts.index')->withSuccess(trans('comma::post.message.update_success'));
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            Post::find($id)->delete();

            return redirect()->route('comma::posts.index')->withSuccess(trans('comma::post.message.delete_success'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
