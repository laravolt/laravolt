<?php
namespace Laravolt\Comma\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasSlug;

    protected $table = 'cms_posts';

    protected $with = ['category', 'tags', 'author'];

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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'cms_posts_tags');
    }

    public function publish()
    {
        if ($this->status === 'draft') {
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
}
