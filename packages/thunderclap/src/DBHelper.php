<?php

namespace Laravolt\Thunderclap;

use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\DriverManager;

class DBHelper
{
    public function listTables()
    {
        $laravelConfig = DB::connection()->getConfig();

        $connectionParams = [
            'dbname' => $laravelConfig['database'],
            'user' => $laravelConfig['username'],
            'password' => $laravelConfig['password'],
            'host' => $laravelConfig['host'],
            'driver' => $laravelConfig['driver'],
            'port' => $laravelConfig['port'] ?? 5432,
            'charset' => $laravelConfig['charset'] ?? 'utf8',
        ];

        $doctrineConnection = DriverManager::getConnection($connectionParams);

        // Gunakan SchemaManager dari Doctrine DBAL 3.x
        $schemaManager = $doctrineConnection->createSchemaManager();

        // Ambil daftar tabel
        $tableNames = $schemaManager->listTableNames();

        $data = [];
        foreach ($tableNames as $table) {
            $data[] = $table;
        }

        return $data;
    }

    public function listColumns($table)
    {
        $laravelConfig = DB::connection()->getConfig();

        $connectionParams = [
            'dbname' => $laravelConfig['database'],
            'user' => $laravelConfig['username'],
            'password' => $laravelConfig['password'],
            'host' => $laravelConfig['host'],
            'driver' => $laravelConfig['driver'],
            'port' => $laravelConfig['port'] ?? 5432,
            'charset' => $laravelConfig['charset'] ?? 'utf8',
        ];

        // Koneksi Doctrine
        $doctrineConnection = DriverManager::getConnection($connectionParams);

        // Schema Manager
        $schemaManager = $doctrineConnection->createSchemaManager();

        $columns = $schemaManager->listTableColumns($table);

        $data = [];
        foreach ($columns as $column) {
            $columnName = $column->getName();
            $data[$columnName] = [
                'name' => $columnName,
                'type' => $column->getType(),
                'required' => $column->getNotnull(),
            ];
        }

        return $data;
    }
}
