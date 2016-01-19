<?php
namespace Laravolt\Thunderclap;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\DB;

class DBHelper
{

    /**
     * DBHelper constructor.
     */
    public function __construct()
    {
        $platform = DB::getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', Type::STRING);

    }

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
        foreach($columns as $column) {
            $data[$column->getName()] = [
                'name'  => $column->getName(),
                'type'  => $column->getType()
            ];
        }

        return $data;
    }
}
