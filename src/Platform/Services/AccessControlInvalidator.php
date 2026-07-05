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

        $this->forgetUserCache($userId);
        $this->deleteDatabaseSessions($userId);
    }

    public function invalidateUsers(iterable $users): void
    {
        $userIds = [];

        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                $userIds[] = $userId;
                $this->forgetUserCache($userId);
            }
        }

        if (! empty($userIds)) {
            // ⚡ Bolt: Bulk delete database sessions to prevent N+1 queries
            $this->deleteDatabaseSessions($userIds);
        }
    }

    protected function forgetUserCache(mixed $userId): void
    {
        Cache::forget("users.{$userId}.permissions");
    }

    protected function deleteDatabaseSessions(mixed $userIds): void
    {
        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

            if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'user_id')) {
                return;
            }

            $query = DB::connection($connection)->table($table);

            if (is_iterable($userIds)) {
                $query->whereIn('user_id', is_array($userIds) ? $userIds : iterator_to_array($userIds));
            } else {
                $query->where('user_id', $userIds);
            }

            $query->delete();
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
