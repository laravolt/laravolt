<?php

declare(strict_types=1);

namespace Laravolt\Thunderclap;

use Doctrine\DBAL\DriverManager;
use Illuminate\Support\Facades\DB;

class DBHelper
{
    public function listTables()
    {
        $connectionParams = $this->getConnectionParams();
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
        $connectionParams = $this->getConnectionParams();

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

    protected function getConnectionParams(): array
    {
        $laravelConfig = DB::connection()->getConfig();
        $driver = $laravelConfig['driver'];
        $isSQLite = $driver === 'sqlite' || $driver === 'sqlite3';
        $isMysql = $driver === 'mysql' || $driver === 'mysqli';

        if ($isMysql) {
            $driver = 'mysqli';
        }

        if ($isSQLite) {
            $driver = 'sqlite3';
        }

        $port = null;

        if (! $isSQLite) {
            $port = (int) $laravelConfig['port'] ?? '5432';
        }

        $connectionParams = [
            'dbname' => $laravelConfig['database'],
            'user' => $laravelConfig['username'] ?? null,
            'password' => $laravelConfig['password'] ?? null,
            'host' => $laravelConfig['host'] ?? null,
            'driver' => $driver,
            'port' => $port,
            'charset' => $laravelConfig['charset'] ?? 'utf8',
        ];

        if ($isSQLite) {
            $connectionParams['path'] = $laravelConfig['database'];
        }

        return $connectionParams;
    }
}
