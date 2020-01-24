<?php

declare(strict_types=1);

namespace Laravolt\Menu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Menu extends Model implements Sortable
{
    use NodeTrait;
    use SortableTrait;

    protected $table = 'menu';

    protected $casts = [
        'permission' => 'array',
        'roles' => 'array',
    ];

    protected $guarded = [];

    protected $appends = ['label_prefixed'];

    protected $searchableColumns = ['label', 'url', 'type'];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery()
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public static function boot()
    {
        static::creating(function (self $model) {

            // Jika order diisi, maka lakukan penyesuaian order secara manual
            if ($model->order) {

                // Skip perhitungan order otomatis dari SortableTrait
                $model->sortable['sort_when_creating'] = false;

                // Order tidak boleh melebihi jumlah item saat ini
                $model->order = min($model->order, $model->getHighestOrderNumber());

                // Order tidak boleh negatif atau 0
                $model->order = max($model->order, 1);

                $model->siblings()->where('order', '>=', $model->order)->increment('order');
            }
        });

        static::updating(function (self $model) {
            $model->order = min($model->order, $model->getHighestOrderNumber());
            $model->order = max($model->order, 1);
            if ($model->isDirty('order')) {
                $previous = $model->getOriginal('order');
                $new = $model->order;
                if ($new > $previous) {
                    $model->siblings()->whereBetween('order', [$previous, $new])->decrement('order');
                } else {
                    $model->siblings()->whereBetween('order', [$new, $previous])->increment('order');
                }
            }
        });

        static::deleting(function (self $model) {
            $model->siblings()->where('order', '>', $model->order)->decrement('order');
        });

        parent::boot();
    }

    public function scopeSearch(Builder $query, $keyword)
    {
        if ($keyword) {
            $query->whereLike($this->searchableColumns, $keyword);
        }
    }

    public static function toFlatSelect()
    {
        $result = (new static())->ordered()->withDepth()->get()->toFlatTree();

        return collect($result)->pluck('label_prefixed', 'id')->toArray();
    }

    public static function toStructuredArray()
    {
        $menu = (new static())->with('children.children')->ordered()->whereNull('parent_id')->withDepth()->get();
        $structuredArray = [];
        foreach ($menu as $m) {
            if ($m->children->isEmpty()) {
                $structuredArray[$m->label] = $m->toArray();
            } else {
                foreach ($m->children->sortBy('order') as $sub) {
                    if ($sub->children->isEmpty()) {
                        $structuredArray[$m->label]['menu'][$sub->label] = $sub->toArray();
                    } else {
                        foreach ($sub->children->sortBy('order') as $subsub) {
                            $structuredArray[$m->label]['menu'][$sub->label]['menu'][$subsub->label] = $subsub->toArray();
                        }
                    }
                }
            }
        }

        return $structuredArray;
    }

    public function setRolesAttribute(array $roles)
    {
        $roles = collect($roles)->transform(function ($item) {
            return (int) $item;
        });

        $this->attributes['roles'] = $roles->toJson();
    }

    public function getLabelPrefixedAttribute()
    {
        if ($this->depth) {
            return str_repeat('----', $this->depth).' '.$this->label;
        }

        return $this->label;
    }

    public function toArray()
    {
        return [
            'url' => url($this->url),
            'data' => [
                'icon' => $this->icon.' '.$this->color,
                'permission' => $this->permission,
                'roles' => $this->roles,
            ],
        ];
    }
}
