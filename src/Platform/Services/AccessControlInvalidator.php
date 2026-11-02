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
    /**
     * Bumped on every invalidation. Users hold their permission lookup in
     * memory (HasRoleAndPermission) and reload it when this changes.
     */
    protected static int $generation = 0;

    public static function generation(): int
    {
        return static::$generation;
    }

    public function invalidateUser(Model $user): void
    {
        static::$generation++;
        $userId = $user->getKey();

        Cache::forget("users.{$userId}.permissions");
        $this->deleteDatabaseSessions($userId);
    }

    public function invalidateUsers(iterable $users): void
    {
        static::$generation++;
        $userIds = [];

        foreach ($users as $user) {
            if ($user instanceof Model) {
                $userId = $user->getKey();
                Cache::forget("users.{$userId}.permissions");
                $userIds[] = $userId;
            }
        }

        if (! empty($userIds)) {
            $this->deleteDatabaseSessions($userIds);
        }
    }

    protected function deleteDatabaseSessions(mixed $userId): void
    {
        try {
            $connection = config('session.connection');
            $table = config('session.table', 'sessions');
            $schema = Schema::connection($connection);

            if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'user_id')) {
                return;
            }

            $query = DB::connection($connection)->table($table);

            if (is_iterable($userId)) {
                $query->whereIn('user_id', $userId);
            } else {
                $query->where('user_id', $userId);
            }

            $query->delete();
        } catch (Throwable) {
            // Session invalidation is best-effort because Laravolt can run with
            // non-database session drivers or applications without session table.
        }
    }
}
