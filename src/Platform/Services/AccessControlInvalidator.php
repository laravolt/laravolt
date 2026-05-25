<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AccessControlInvalidator
{
    public function invalidateUser(Model $user): void
    {
        $userId = $user->getKey();

        Cache::forget("users.{$userId}.permissions");
        $this->deleteDatabaseSessions([$userId]);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];

        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                // We still need to invalidate cache individually
                Cache::forget("users.{$userId}.permissions");
                $userIds[] = $userId;
            }
        }

        if (!empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    /**
     * @param array<mixed> $userIds
     */
    protected function deleteDatabaseSessions(array $userIds): void
    {
        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

            // Avoid checking schema in a loop by passing an array of user IDs
            // and executing a single check and bulk delete.
            if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'user_id')) {
                return;
            }

            DB::connection($connection)->table($table)->whereIn('user_id', $userIds)->delete();
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
