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
                $userIds[] = $userId;
                Cache::forget("users.{$userId}.permissions");
            }
        }

        if (!empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    /**
     * @param mixed|array $userIds
     */
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

            if (is_array($userIds)) {
                $query->whereIn('user_id', $userIds)->delete();
            } else {
                $query->where('user_id', $userIds)->delete();
            }
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
