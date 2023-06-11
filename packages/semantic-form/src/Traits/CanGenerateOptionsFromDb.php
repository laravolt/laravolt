<?php

namespace Laravolt\SemanticForm\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Grammars\Grammar;

trait CanGenerateOptionsFromDb
{
    protected $connection;

    protected $query;

    protected $keyColumn = 'id';

    protected $displayColumn = 'name';

    public function connection(?string $connection)
    {
        if ($connection) {
            $this->connection = $connection;
        }

        return $this;
    }

    public function query(?string $query)
    {
        if ($query) {
            $this->query = $query;
        }

        return $this;
    }

    public function keyColumn(?string $keyColumn)
    {
        if ($keyColumn) {
            $this->keyColumn = $keyColumn;
        }

        return $this;
    }

    public function displayColumn(?string $displayColumn)
    {
        if ($displayColumn) {
            $this->displayColumn = $displayColumn;
        }

        return $this;
    }

    protected function getConnection()
    {
        return $this->connection ?? config('database.default');
    }

    protected function getOptionsFromDb()
    {
        $keyColumn = $this->keyColumn;
        $valueColumn = $this->displayColumn;

        $options = [];

        if ($this->query) {
            $data = DB::connection($this->getConnection())->select(DB::raw($this->query)->getValue(new Grammar()));
            $options = collect($data)->mapWithKeys(function ($item) use ($keyColumn, $valueColumn) {
                $item = (array) $item;

                return [$item[$keyColumn] => $item[$valueColumn]];
            });
        }

        return $options;
    }
}
