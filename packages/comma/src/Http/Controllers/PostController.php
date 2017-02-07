<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Comma\Models\Category;
use Laravolt\Comma\Models\Post;
use Laravolt\Comma\Http\Requests\StorePost;
use Laravolt\Comma\Models\Tag;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate();

        return view('comma::posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all()->pluck('name');

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
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
