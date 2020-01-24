<?php

namespace Laravolt\Comma\Models\Traits;

use Illuminate\Support\Str;
use Laravolt\Comma\Models\Tag;

trait Taggable
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'cms_posts_tags', 'post_id');
    }

    public function tag($tags)
    {
        $existingTagIds = $this->tags->pluck('id', 'id');

        foreach ((array) $tags as $tag) {
            $tag = $this->normalizeTag($tag);

            if (!$existingTagIds->has($tag->getKey())) {
                $this->tags()->attach($tag);
            }
        }

        $this->load('tags');

        return $this;
    }

    public function retag($tags = null)
    {
        $ids = [];

        foreach ((array) $tags as $tag) {
            $tag = $this->normalizeTag($tag);
            if ($tag) {
                $ids[] = $tag->getKey();
            }
        }

        $this->tags()->sync($ids);
        $this->load('tags');

        return $this;
    }

    public function untag($tags = null)
    {
        foreach ((array) $tags as $tag) {
            $tag = $this->normalizeTag($tag);
            if ($tag) {
                $this->tags()->detach($tag);
            }
        }

        $this->load('tags');

        return $this;
    }

    public function getTagListAttribute()
    {
        return $this->tags->implode('name', ', ');
    }

    public function getTagArrayAttribute()
    {
        return $this->tags->pluck('name', 'name')->toArray();
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
