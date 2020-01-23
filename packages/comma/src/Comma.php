<?php

namespace Laravolt\Comma;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravolt\Comma\Models\Post;

class Comma
{
    public function create($title, $content, Model $author, $type = null, $tags = null)
    {
        $type = $type ?? config('laravolt.comma.default_type');

        return DB::transaction(function () use ($author, $title, $content, $tags, $type) {
            $post = new Post();
            $post->title = $title;
            $post->content = $content;
            $post->type = $type;
            $post->status = 'session';
            $post->author()->associate($author);

            $post->save();

            $post->tag($tags);

            return $post;
        });
    }

    public function update(Post $post, $title, $content, Model $author, $tags = null)
    {
        return DB::transaction(function () use ($post, $author, $title, $content, $tags) {
            $post->title = $title;
            $post->content = $content;
            $post->author()->associate($author);
            $post->retag($tags);

            $post->save();

            return $post;
        });
    }
}
