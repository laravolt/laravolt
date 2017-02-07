<?php
namespace Laravolt\Comma;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravolt\Comma\Models\Category;
use Laravolt\Comma\Models\Post;

class Comma
{

    public function makePost(Model $author, $title, $content, $category, $tags = null, $type = null)
    {
        $type = $type ?? config('laravolt.comma.default_type');

        return DB::transaction(function () use ($author, $title, $content, $category, $tags, $type) {

            $category = $this->normalizeCategory($category);

            $post = $this->savePost($title, $content, $type, $category, $author);

            $post->tag($tags);

            return $post;
        });
    }

    protected function normalizeCategory($category)
    {
        if ($category instanceof Category) {
            return $category;
        }

        if ($model = Category::find($category)) {
            return $model;
        }

        return Category::firstOrCreate(['name' => $category]);
    }

    protected function savePost($title, $content, $type, $category, $author)
    {
        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->type = $type;
        $post->category()->associate($category);
        $post->author()->associate($author);

        $post->save();

        return $post;
    }
}
