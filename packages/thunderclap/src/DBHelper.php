<?php

namespace Laravolt\Thunderclap;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;

class DBHelper
{
    public function listTables()
    {
        $tables = DB::getDoctrineSchemaManager()->listTables();

        $data = [];
        foreach ($tables as $table) {
            $data[] = $table->getName();
        }

        return $data;
    }

    public function listColumns($table)
    {
        $columns = \DB::getDoctrineSchemaManager()->listTableColumns($table);

        $data = [];
        foreach ($columns as $column) {
            $columnName = $column->getName();
            $data[$columnName] = [
                'name'     => $columnName,
                'type'     => $column->getType(),
                'required' => $column->getNotnull(),
            ];
        }

        return $data;
    }
}
