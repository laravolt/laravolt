<?php

namespace Laravolt\Suitable\Tables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
            $columns[] = Text::make($column)->sortable($column);
        }

        return $columns;
    }

    protected function getColumnListing($source)
    {
        $table = $source;

        if ($source instanceof Model) {
            $table = $source->getTable();
        } elseif (is_subclass_of($source, Model::class)) {
            $table = app($source)->getTable();
        } elseif ($source instanceof LengthAwarePaginator || $source instanceof Collection) {
            if (($item = $source->first()) !== null) {
                if ($item instanceof \stdClass) {
                    return array_keys((array) $item);
                }

                if ($item instanceof Model) {
                    $table = $item->getTable();
                }
            }
        } elseif ($source instanceof Builder) {
            $table = $source->getModel()->getTable();
        }

        if (is_string($table) && Schema::hasTable($table)) {
            return \Illuminate\Support\Facades\Schema::getColumnListing($table);
        }

        return [];
    }
}
