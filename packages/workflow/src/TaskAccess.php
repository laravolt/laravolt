<?php

declare(strict_types=1);

namespace Laravolt\Workflow;

/**
 * Decides whether a Camunda task may be submitted through a module's task route.
 */
final class TaskAccess
{
    /**
     * The task must come from the module's process and have a form in the module config.
     *
     * @param  array<string, mixed>  $moduleTasks  task definition key => form config
     */
    public static function belongsToModule(
        string $moduleProcessKey,
        array $moduleTasks,
        string $taskProcessDefinitionId,
        string $taskDefinitionKey,
    ): bool {
        // Camunda process definition ids look like "<key>:<version>:<id>".
        $taskProcessKey = explode(':', $taskProcessDefinitionId, 2)[0];

        return $taskProcessKey === $moduleProcessKey && array_key_exists($taskDefinitionKey, $moduleTasks);
    }

    /**
     * Guests may only submit tasks the module exposes on its public tracker; an assigned
     * task may only be submitted by its assignee.
     *
     * @param  array<int, string>  $trackerTasks
     */
    public static function maySubmit(
        array $trackerTasks,
        string $taskDefinitionKey,
        ?string $assignee,
        int|string|null $userId,
    ): bool {
        if ($userId === null) {
            return $assignee === null && in_array($taskDefinitionKey, $trackerTasks, true);
        }

        return $assignee === null || $assignee === (string) $userId;
    }
}
