<?php

namespace Laravolt\Comma\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravolt\Comma\Models\Post;

class MediaController extends Controller
{
    public function index(Request $request)
    {
    }

    public function store(Request $request)
    {
        try {
            $post = Post::findOrFail($request->get('post_id'));
            $media = $post->addMediaFromRequest('file')->toMediaLibrary();

            return response()->json(
                [
                    'url' => $media->getFullUrl(),
                    'id'  => $media->getKey(),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}
