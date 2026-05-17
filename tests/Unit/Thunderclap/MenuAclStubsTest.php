<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Thunderclap;

use Laravolt\Tests\UnitTest;

/**
 * Regression for v7 P1-8: Generated routes and menu must enforce permission
 * gating — not just menu visibility.
 */
class MenuAclStubsTest extends UnitTest
{
    private string $routeStub;

    private string $configStub;

    private string $serviceProviderStub;

    private string $testStub;

    protected function setUp(): void
    {
        parent::setUp();

        $base = __DIR__.'/../../../packages/thunderclap/stubs/laravolt';

        $this->routeStub = (string) file_get_contents($base.'/routes/web.php.stub');
        $this->configStub = (string) file_get_contents($base.'/config/config.php.stub');
        $this->serviceProviderStub = (string) file_get_contents($base.'/ServiceProvider.php.stub');
        $this->testStub = (string) file_get_contents($base.'/Tests/Test.php.stub');
    }

    public function test_config_stub_defines_a_non_empty_permission_by_default(): void
    {
        $this->assertStringContainsString(
            "'permission' => 'modules.:module-name:.view'",
            $this->configStub,
            'Generated config must define a default permission gate for the module.'
        );
    }

    public function test_route_stub_applies_can_middleware_from_config_permission(): void
    {
        $this->assertStringContainsString(
            "config('modules.:module-name:.permission')",
            $this->routeStub,
            'Generated routes must read the permission from module config.'
        );
        $this->assertStringContainsString(
            "'can:'.\$permission",
            $this->routeStub,
            'Generated routes must apply can:<permission> middleware when permission is set.'
        );
    }

    public function test_service_provider_registers_menu_with_permission_data(): void
    {
        $this->assertStringContainsString(
            "->data('permission', \$this->config['permission'] ?? [])",
            $this->serviceProviderStub,
            'Generated ServiceProvider must pass permission data to the menu entry.'
        );
    }

    public function test_generated_test_stub_grants_admin_role_for_permission_gated_routes(): void
    {
        $this->assertStringContainsString(
            "Role::firstOrCreate(['name' => 'admin'])",
            $this->testStub,
            'Generated test must create an admin role so permission-gated routes pass.'
        );
        $this->assertStringContainsString(
            "syncPermission(['*'])",
            $this->testStub,
            'Generated test must grant wildcard permission to the test user.'
        );
        $this->assertStringContainsString(
            '$this->user->assignRole($role)',
            $this->testStub,
            'Generated test must assign the admin role to the acting user.'
        );
    }
}
