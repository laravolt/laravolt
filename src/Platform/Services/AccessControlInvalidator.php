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

        $this->invalidateUserCache($userId);
        $this->deleteDatabaseSessions([$userId]);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];
        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                $userIds[] = $userId;
                // Note: Cache::forget does not support bulk invalidation natively,
                // so we still clear it per-user here.
                $this->invalidateUserCache($userId);
            }
        }

        if (!empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    protected function invalidateUserCache(mixed $userId): void
    {
        Cache::forget("users.{$userId}.permissions");
    }

    /**
     * @param array<mixed> $userIds
     */
    protected function deleteDatabaseSessions(array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }

        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

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
