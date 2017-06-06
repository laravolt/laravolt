<?php

namespace Laravolt\Comma\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Comma\Models\Scopes\VisibleScope;
use Laravolt\Comma\Models\Traits\Taggable;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasSlug, Taggable, HasMediaTrait;

    protected $table = 'cms_posts';

    protected $with = ['category', 'tags', 'author'];

    protected $dates = ['published_at'];

    protected $casts = [
        'meta' => 'array',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VisibleScope);
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug')->doNotGenerateSlugsOnCreate();
    }

    public function author()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where(
                function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%")->orWhereHas(
                        'category', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%$keyword%");
                    }
                    )->orWhereHas(
                        'author', function ($q2) use ($keyword) {
                        $q2->where('name', 'like', "%$keyword%");
                    }
                    );
                }
            );
        }
    }

    public function publish()
    {
        if (!$this->published_at) {
            $this->published_at = new Carbon();
        }
        $this->status = 'published';

        return $this->save();
    }

    public function unpublish()
    {
        $this->status = 'unpublished';

        return $this->save();
    }

    public function saveAsDraft()
    {
        $this->status = 'draft';

        return $this->save();
    }

    public function isSession()
    {
        return $this->status === 'session';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isUnpublished()
    {
        return $this->status === 'unpublished';
    }

    public function displayDate()
    {
        switch ($this->status) {
            case 'draft':
                return 'Last edited '.$this->updated_at->diffForHumans();
                break;
            case 'published':
                return 'Published '.$this->published_at->diffForHumans();
                break;
            case 'unpublished':
                return 'Last edited '.$this->updated_at->diffForHumans();
                break;
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
