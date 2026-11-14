<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Workflow;

use Laravolt\Tests\UnitTest;
use Laravolt\Workflow\Commands\WorkflowCheckCommand;

class WorkflowCheckCommandTest extends UnitTest
{
    public function test_failure_table_masks_credentials(): void
    {
        $rows = (fn (array $config) => $this->configRows($config))->call(new WorkflowCheckCommand, [
            'url' => 'http://camunda.test/engine-rest',
            'user' => 'demo',
            'password' => 'example-password',
            'tenant_id' => '',
        ]);

        $this->assertSame([
            ['url', 'http://camunda.test/engine-rest'],
            ['user', 'demo'],
            ['password', '********'],
            ['tenant_id', ''],
        ], $rows);
    }
}
