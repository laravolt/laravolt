<?php

namespace Laravolt\Comma\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug;

    protected $table = 'cms_categories';

    protected $fillable = ['name'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where('name', 'like', "%$keyword%");
        }
    }
}
