<?php

declare(strict_types=1);

namespace Laravolt\Lookup\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Support\Traits\AutoFilter;
use Laravolt\Support\Traits\AutoSort;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Lookup extends Model
{
    use AutoFilter;
    use AutoSort;

    public $casts = [
        'meta' => 'array',
    ];

    protected $table = 'platform_lookup';

    protected $guarded = [];

    protected $searchable = ['lookup_key', 'lookup_value', 'parent.lookup_value'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_key', 'lookup_key');
    }

    public static function toDropdown($category)
    {
        return static::query()->whereCategory($category)->pluck('lookup_value', 'lookup_key')->toArray();
    }

    public function getMetaAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'meta');
    }

    public function scopeWithMeta(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('meta');
    }

    public function scopefromCollection(Builder $query, string $collection = null)
    {
        if ($collection) {
            $query->where('category', $collection);
        }
    }

    public function scopeSearch(Builder $query, $keyword)
    {
        if ($keyword) {
            $query->whereLike($this->searchable, $keyword);
        }
    }
}
