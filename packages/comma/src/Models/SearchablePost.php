<?php

namespace Laravolt\Comma\Models;

use Laravel\Scout\Searchable;

class SearchablePost extends Post
{
    use Searchable;

    public function searchableAs()
    {
        return 'post';
    }

    public function toSearchableArray()
    {
        return [
            'id'            => $this->getKey(),
            'type'          => $this->type,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'content'       => $this->content,
            'es_content'    => $this->title.' '.$this->category->name ?? null.' '.$this->tag_list.' '.strip_tags($this->content),
            'category_id'   => $this->category_id,
            'category_name' => $this->category->name ?? null,
            'tags'          => $this->tag_array,
            'language'      => 'id',
        ];
    }
}
