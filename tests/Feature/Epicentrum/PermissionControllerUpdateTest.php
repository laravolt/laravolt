<?php

use Laravolt\Platform\Models\Permission;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use Laravolt\Tests\Bootstrap;

class PermissionControllerUpdateTest extends TestCase
{
    use Bootstrap;
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_updates_multiple_permissions_efficiently_without_n_plus_1()
    {
        $this->withoutMiddleware(); // Avoid auth checks since we're just testing the update logic

        $permissions = [];
        foreach (range(1, 3) as $i) {
            $permissions[] = Permission::create(['name' => 'perm_' . $i]);
        }

        $payload = [
            'permission' => [
                $permissions[0]->id => 'Desc 1',
                $permissions[1]->id => 'Desc 2',
                $permissions[2]->id => 'Desc 3',
            ],
        ];

        $queries = 0;
        DB::listen(
            function ($query) use (&$queries) {
                if (strpos($query->sql, 'update') !== false) {
                    $queries++;
                }
            }
        );

        $response = $this->put(route('epicentrum::permissions.update'), $payload);

        $response->assertSessionHas('success');

        // Before optimization, this is 3. After optimization, it should be 1.
        $this->assertEquals(1, $queries);

        $this->assertEquals('Desc 1', Permission::find($permissions[0]->id)->description);
        $this->assertEquals('Desc 2', Permission::find($permissions[1]->id)->description);
        $this->assertEquals('Desc 3', Permission::find($permissions[2]->id)->description);
    }
}
