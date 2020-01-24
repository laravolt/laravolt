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
        $platform = DB::getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', Type::STRING);

        $columns = \DB::getDoctrineSchemaManager()->listTableColumns($table);

        $data = [];
        foreach ($columns as $column) {
            $data[$column->getName()] = [
                'name'     => $column->getName(),
                'type'     => $column->getType(),
                'required' => $column->getNotnull(),
            ];
        }

        return $data;
    }
}
