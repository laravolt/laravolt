<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Acl;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravolt\Tests\FeatureTest;

class PermissionCacheHardeningTest extends FeatureTest
{
    use RefreshDatabase;

    public function test_string_garbage_in_cache_is_discarded_and_rebuilt(): void
    {
        $user = $this->createUser();
        $key = "users.{$user->getKey()}.permissions";

        Cache::put($key, 'totally-not-an-array', 3600);

        $this->assertFalse($user->hasPermission('foo'));
        $this->assertIsArray(Cache::get($key));
        $this->assertSame([], Cache::get($key));
    }

    public function test_int_garbage_in_cache_is_discarded_and_rebuilt(): void
    {
        $user = $this->createUser();
        $key = "users.{$user->getKey()}.permissions";

        Cache::put($key, 12345, 3600);

        $this->assertFalse($user->hasPermission('foo'));
        $this->assertIsArray(Cache::get($key));
    }

    public function test_array_with_wrong_shape_is_discarded_and_rebuilt(): void
    {
        $user = $this->createUser();
        $key = "users.{$user->getKey()}.permissions";

        // shape that misses required keys
        Cache::put($key, [['foo' => 'bar']], 3600);

        $this->assertFalse($user->hasPermission('foo'));
        $this->assertIsArray(Cache::get($key));
    }

    public function test_incomplete_class_in_cache_is_discarded_and_rebuilt(): void
    {
        $user = $this->createUser();
        $key = "users.{$user->getKey()}.permissions";

        // Forge an __PHP_Incomplete_Class to mimic class-move serialization drift.
        $className = 'Some\\Old\\Namespace\\Permission\\Stub';
        $serialized = sprintf(
            'O:%d:"%s":2:{s:2:"id";i:1;s:4:"name";s:3:"foo";}',
            strlen($className),
            $className
        );
        $incomplete = @unserialize($serialized);
        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $incomplete);

        Cache::put($key, $incomplete, 3600);

        // Must not throw a TypeError when traversing the Collection.
        $this->assertFalse($user->hasPermission('foo'));

        $rebuilt = Cache::get($key);
        $this->assertIsArray($rebuilt);
    }

    public function test_valid_permissions_round_trip_through_primitive_cache(): void
    {
        $user = $this->createUser();
        $user->assignRole('Admin');

        $roleModel = app(config('laravolt.epicentrum.models.role'));
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));

        $perm = $permissionModel->create(['name' => 'create-user']);
        $role = $roleModel->where('name', 'Admin')->first();
        $role->permissions()->attach($perm->getKey());

        // Cold cache: hasPermission rebuilds.
        $this->assertTrue($user->hasPermission('create-user'));

        $cached = Cache::get("users.{$user->getKey()}.permissions");
        $this->assertIsArray($cached);
        $this->assertNotEmpty($cached);
        $this->assertArrayHasKey('id', $cached[0]);
        $this->assertArrayHasKey('name', $cached[0]);
        $this->assertIsString($cached[0]['name']);
        $this->assertSame('create-user', $cached[0]['name']);

        // Warm cache: still works, returns Collection.
        $permissions = $user->permissions();
        $this->assertInstanceOf(Collection::class, $permissions);
        $this->assertTrue($permissions->contains('name', 'create-user'));
        $this->assertTrue($permissions->contains('id', $perm->getKey()));
    }

    public function test_collection_signature_preserved_for_callers(): void
    {
        $user = $this->createUser();

        // No permissions; still returns Eloquent Collection (typed signature).
        $permissions = $user->permissions();
        $this->assertInstanceOf(Collection::class, $permissions);
        $this->assertCount(0, $permissions);

        // contains() with multiple shapes does not throw on empty collection.
        $this->assertFalse($permissions->contains('id', 1));
        $this->assertFalse($permissions->contains('name', 'whatever'));
    }
}
