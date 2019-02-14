<?php

namespace Laravolt\Suitable\Tables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Suitable\TableView;

class BasicTable extends TableView
{
    protected $columns = [];

    protected $only;

    public function __construct($source)
    {
        parent::__construct($source);
        $this->columns = collect($this->getColumnListing($source));
        $this->columns = $this->columns->combine($this->columns);
    }

    public function only($columns)
    {
        $onlyColumns = is_array($columns) ? $columns : func_get_args();

        $this->columns = $this->columns->only($onlyColumns);

        return $this;
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
        $table = $source;

        if (is_subclass_of($source, Model::class)) {
            $table = app($source)->getTable();
        } elseif ($source instanceof LengthAwarePaginator) {
            if (($item = $source->first()) !== null && ($item instanceof Model)) {
                $table = $item->getTable();
            }
        }

        if (Schema::hasTable($table)) {
            return \Illuminate\Support\Facades\Schema::getColumnListing($table);
        }

        return [];
    }
}
