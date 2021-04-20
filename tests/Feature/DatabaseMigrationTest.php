<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Laravolt\Tests\FeatureTest;

class DatabaseMigrationTest extends FeatureTest
{
    public function testPlatformTablesPresent()
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
