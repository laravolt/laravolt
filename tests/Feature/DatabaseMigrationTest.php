<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Laravolt\Tests\FeatureTest;
use PHPUnit\Framework\Attributes\DataProvider;

class DatabaseMigrationTest extends FeatureTest
{
    /**
     * @return array<string, array{string}>
     */
    public static function tables(): array
    {
        $tables = [
            'users',
            'users_activation',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'acl_roles',
            'acl_permissions',
            'acl_permission_role',
            'acl_role_user',
            'media',
        ];

        return array_combine($tables, array_map(fn (string $table): array => [$table], $tables));
    }

    #[DataProvider('tables')]
    public function test_the_package_migrations_create_the_table(string $table): void
    {
        $this->assertTrue(Schema::hasTable($table), "Table [{$table}] is missing.");
    }

    public function test_the_settings_table_follows_its_config(): void
    {
        $this->assertTrue(Schema::hasTable(config('setting.database.table')));
    }
}
