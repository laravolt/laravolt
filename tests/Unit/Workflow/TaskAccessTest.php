<?php

declare(strict_types=1);

use Laravolt\Workflow\TaskAccess;

it('accepts a task from the module process that has a form', function () {
    expect(TaskAccess::belongsToModule('leave', ['approve' => []], 'leave:3:abc-123', 'approve'))->toBeTrue();
});

it('rejects a task from another process or without a form in the module', function () {
    expect(TaskAccess::belongsToModule('leave', ['approve' => []], 'payroll:1:def-456', 'approve'))->toBeFalse()
        ->and(TaskAccess::belongsToModule('leave', ['approve' => []], 'leave:3:abc-123', 'pay'))->toBeFalse()
        ->and(TaskAccess::belongsToModule('leave', ['approve' => []], 'leaves:3:abc-123', 'approve'))->toBeFalse();
});

it('lets guests submit only unassigned tracker tasks', function () {
    expect(TaskAccess::maySubmit(['feedback'], 'feedback', null, null))->toBeTrue()
        ->and(TaskAccess::maySubmit(['feedback'], 'approve', null, null))->toBeFalse()
        ->and(TaskAccess::maySubmit(['feedback'], 'feedback', 'user-7', null))->toBeFalse();
});

it('lets signed-in users submit unassigned tasks and their own assigned tasks', function () {
    expect(TaskAccess::maySubmit([], 'approve', null, 7))->toBeTrue()
        ->and(TaskAccess::maySubmit([], 'approve', '7', 7))->toBeTrue()
        ->and(TaskAccess::maySubmit([], 'approve', '8', 7))->toBeFalse();
});
