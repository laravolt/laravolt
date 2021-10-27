<?php

namespace Laravolt\Suitable\Columns;

class BelongsTo extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relationship */
        $relationship = $cell->{$this->field}();

        /** @var \Illuminate\Database\Eloquent\Model $relatedModel */
        $relatedModel = $relationship->getRelated();

        $hasDisplayMethod = method_exists($relatedModel, 'display');

        return $hasDisplayMethod ? $cell->{$this->field}->display() : (string) $cell->{$this->field};
    }
}
