<?php

declare(strict_types=1);

namespace Laravolt\Menu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Laravolt\Support\Contracts\Sortable;
use Laravolt\Support\Traits\SortableTrait;

class Menu extends Model implements Sortable
{
    use NodeTrait, SortableTrait;

    protected $table = 'menu';

    protected $casts = [
        'permission' => 'array',
        'roles' => 'array',
    ];

    protected $guarded = [];

    protected $appends = ['label_prefixed'];

    protected $searchableColumns = ['label', 'url', 'type'];

    protected static $sortable = ['column' => 'order', 'group_by' => 'parent_id'];

    public function buildSortQuery()
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public function scopeSearch(Builder $query, $keyword)
    {
        if ($keyword) {
            $query->whereLike($this->searchableColumns, $keyword);
        }
    }

    public static function toFlatSelect()
    {
        $result = (new static())->orderByPosition()->withDepth()->get()->toFlatTree();

        return collect($result)->pluck('label_prefixed', 'id')->toArray();
    }

    public static function toStructuredArray()
    {
        $menu = (new static())->with('children.children')->orderByPosition()->whereNull('parent_id')->withDepth()->get();
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
