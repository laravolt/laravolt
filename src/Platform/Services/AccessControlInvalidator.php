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
        $this->deleteDatabaseSessions($userId);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];
        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                Cache::forget("users.{$userId}.permissions");
                $userIds[] = $userId;
            }
        }

        // Bolt optimization: Batch delete database sessions in a single query
        // instead of executing N+1 schema checks and deletes inside a loop.
        if (! empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    protected function deleteDatabaseSessions(mixed $userIds): void
    {
        try {
            $userIdsArray = is_iterable($userIds) ? (is_array($userIds) ? $userIds : iterator_to_array($userIds)) : [$userIds];

            if (empty($userIdsArray)) {
                return;
            }

            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

            if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'user_id')) {
                return;
            }

            DB::connection($connection)->table($table)->whereIn('user_id', $userIdsArray)->delete();
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
