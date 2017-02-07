<?php
namespace Laravolt\Comma\Models\Traits;

use Illuminate\Support\Str;
use Laravolt\Comma\Models\Tag;

trait Taggable
{
    public function tag($tags)
    {
        $existingTagIds = $this->tags->pluck('id', 'id');

        foreach ((array)$tags as $tag) {
            $tag = $this->normalizeTag($tag);

            if (!$existingTagIds->has($tag->getKey())) {
                $this->tags()->attach($tag);
            }
        }

        return $this;
    }

    public function retag($tags = null)
    {
        $ids = [];

        foreach ((array)$tags as $tag) {
            $tag = $this->normalizeTag($tag);
            if ($tag) {
                $ids[] = $tag->getKey();
            }
        }

        $this->tags()->sync($ids);

        return $this;
    }

    public function untag($tags = null)
    {
        foreach ((array)$tags as $tag) {
            $tag = $this->normalizeTag($tag);
            if ($tag) {
                $this->tags()->detach($tag);
            }
        }

        return $this;
    }

    protected function normalizeTag($tag)
    {
        if ($tag instanceof Tag) {
            return $tag;
        }

        if (is_string($tag)) {
            $slug = Str::slug($tag);
            $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $tag]);

            return $tag;
        }

        return null;
    }
}
