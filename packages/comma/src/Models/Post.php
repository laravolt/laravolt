<?php

namespace Laravolt\Comma\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravolt\Comma\Models\Traits\Taggable;
use Laravolt\Suitable\AutoSort;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasSlug;
    use Taggable;
    use HasMediaTrait;
    use AutoSort;
    protected $table = 'cms_posts';

    protected $with = ['tags', 'author'];

    protected $dates = ['published_at'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static $logAttributes = ['title', 'content', 'status', 'category_id'];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'author_id');
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->whereLike(['title', 'author.name'], $keyword);
        }
    }

    public function scopefromCollection(Builder $query, string $collection = null)
    {
        if ($collection) {
            $collection = config("laravolt.comma.collections.$collection");
            foreach (Arr::get($collection, 'filters', []) as $column => $value) {
                $query->where($column, $value);
            }
        }
    }

    public function featuredImageUrl()
    {
        if ($featuredImage = $this->getFirstMedia('featured')) {
            return $featuredImage->getUrl();
        }

        return false;
    }
}
