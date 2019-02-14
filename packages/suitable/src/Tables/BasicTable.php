<?php

namespace Laravolt\Suitable\Tables;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class BasicTable extends TableView
{
    protected $columns = [];

    public function __construct($source)
    {
        parent::__construct($source);
        $this->columns = $this->getColumnListing($source);
    }

    protected function columns()
    {
        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = Text::make($column);
        }

        return $columns;
    }

    protected function getColumnListing($source)
    {
        if (is_subclass_of($source, Model::class)) {
            return \Illuminate\Support\Facades\Schema::getColumnListing(app($source)->getTable());
        }

        return [];
    }
}
