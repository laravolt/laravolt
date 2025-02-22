<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl\Feature;

use Illuminate\Support\Facades\Schema;
use Laravolt\Tests\Feature\Acl\FeatureTest;

class DatabaseMigrationTest extends FeatureTest
{
    public function test_platform_tables_present()
    {
        $tables = [
            'users',
            'users_activation',
            'password_resets',
            'acl_permission_role',
            'acl_permissions',
            'acl_role_user',
            'acl_roles',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
    }
}
