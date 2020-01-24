<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Laravolt\SemanticForm\Elements\Button;
use Laravolt\Suitable\Columns\Column;
use Laravolt\Suitable\Columns\ColumnInterface;

class SelectButton extends Column implements ColumnInterface
{
    protected $fillable = null;

    public static function make($fillable, $header = null)
    {
        $button = parent::make('');

        return $button->setFillable($fillable);
    }

    public function setFillable($fillable)
    {
        $this->fillable = $fillable;

        return $this;
    }

    public function cell($cell, $collection, $loop)
    {
        if ($cell instanceof Model) {
            $payload = collect($cell->toArray());
        }

        if ($cell instanceof \stdClass || is_array($cell)) {
            $payload = collect($cell);
        }

        if (!empty($this->fillable)) {
            $payload = $payload->only($this->fillable);
        }

        return (new Button('Pilih', null))
            ->addClass('primary')
            ->data('button-select', 'true')
            ->data('payload', json_encode($payload));
    }
}
